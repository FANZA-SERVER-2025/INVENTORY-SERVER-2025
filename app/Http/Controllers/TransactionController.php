<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\TransactionDetail;
use App\Models\Item;
use App\Models\Vehicle;
use App\Exports\TransactionsExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class TransactionController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view-transactions', ['only' => ['index', 'show']]);
        $this->middleware('permission:create-transactions', ['only' => ['create', 'store']]);
        $this->middleware('permission:edit-transactions', ['only' => ['updatePayment']]);
        $this->middleware('permission:delete-transactions', ['only' => ['destroy']]);
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Transaction::with(['user', 'vehicle']);

            // Filter by transaction type
            if ($request->has('type_filter') && $request->type_filter != '') {
                $query->where('type', $request->type_filter);
            }

            // Filter by payment status (only for 'out' transactions)
            if ($request->has('payment_status_filter') && $request->payment_status_filter != '') {
                $query->where('payment_status', $request->payment_status_filter);
            }

            // All users (superadmin and admin) can see all transactions

            if ($request->has('search') && $request->search['value']) {
                $search = $request->search['value'];
                $query->where(function($q) use ($search) {
                    $q->where('transaction_number', 'like', "%{$search}%")
                      ->orWhereHas('user', function($q) use ($search) {
                          $q->where('name', 'like', "%{$search}%");
                      });
                });
            }

            $total = $query->count();

            if ($request->has('order')) {
                $columns = ['id', 'transaction_number', 'type', 'transaction_date', 'total_amount'];
                $columnIndex = $request->order[0]['column'];
                $columnName = $columns[$columnIndex] ?? 'id';
                $direction = $request->order[0]['dir'];
                $query->orderBy($columnName, $direction);
            }

            $start = $request->start ?? 0;
            $length = $request->length ?? 10;
            $transactions = $query->skip($start)->take($length)->get();

            // All users can see all transactions
            $recordsTotal = Transaction::query();

            return response()->json([
                'draw' => $request->draw,
                'recordsTotal' => $recordsTotal->count(),
                'recordsFiltered' => $total,
                'data' => $transactions
            ]);
        }

        return view('transactions.index');
    }

    public function create()
    {
        $items = Item::where('is_active', true)->get();
        $vehicles = Vehicle::where('is_active', true)->get();
        
        return view('transactions.create', compact('items', 'vehicles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:in,out',
            'transaction_date' => 'required|date',
            'vehicle_id' => 'nullable|exists:vehicles,id',
            'customer_name' => 'nullable|string|max:255',
            'customer_address' => 'nullable|string',
            'store_name' => 'nullable|string|max:255',
            'payment_status' => 'nullable|in:paid,unpaid',
            'discount' => 'nullable|numeric|min:0',
            'bonus' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.item_id' => 'required|exists:items,id',
            'items.*.unit_type' => 'required|in:pcs,dozen,box',
            'items.*.box_quantity' => 'nullable|integer|min:1',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0'
        ]);

        DB::beginTransaction();
        try {
            // Generate transaction number
            $lastTransaction = Transaction::whereDate('created_at', today())->latest()->first();
            $number = $lastTransaction ? intval(substr($lastTransaction->transaction_number, -4)) + 1 : 1;
            $transactionNumber = 'TRX-' . date('Ymd') . '-' . str_pad($number, 4, '0', STR_PAD_LEFT);

            // Create transaction
            $transaction = Transaction::create([
                'transaction_number' => $transactionNumber,
                'user_id' => auth()->id(),
                'type' => $validated['type'],
                'transaction_date' => $validated['transaction_date'],
                'vehicle_id' => $validated['vehicle_id'] ?? null,
                'customer_name' => $validated['customer_name'] ?? null,
                'customer_address' => $validated['customer_address'] ?? null,
                'store_name' => $validated['store_name'] ?? null,
                'payment_status' => $validated['payment_status'] ?? 'unpaid',
                'discount' => $validated['discount'] ?? 0,
                'bonus' => $validated['bonus'] ?? 0,
                'notes' => $validated['notes'] ?? null,
                'total_amount' => 0
            ]);

            $totalAmount = 0;

            // Create transaction details and update stock
            foreach ($validated['items'] as $itemData) {
                $item = Item::findOrFail($itemData['item_id']);
                $unitType = $itemData['unit_type'];
                $quantity = $itemData['quantity'];
                $price = $itemData['price'];
                
                // Calculate actual quantity in pcs based on unit type
                $actualQuantityInPcs = $quantity;
                
                if ($unitType === 'dozen') {
                    // For dozen: use default 12 pcs per dozen
                    $actualQuantityInPcs = $quantity * 12;
                } elseif ($unitType === 'box') {
                    // For box: quantity already calculated in frontend based on user input
                    $actualQuantityInPcs = $quantity;
                }
                
                $subtotal = $actualQuantityInPcs * $price;

                // Create detail with unit information
                $detailData = [
                    'transaction_id' => $transaction->id,
                    'item_id' => $item->id,
                    'quantity' => $actualQuantityInPcs,
                    'unit_type' => $unitType,
                    'price' => $price,
                    'subtotal' => $subtotal
                ];
                
                // Add box-specific data if unit type is box
                if ($unitType === 'box' && isset($itemData['box_quantity']) && $itemData['box_quantity'] > 0) {
                    $detailData['box_quantity'] = $itemData['box_quantity'];
                    $detailData['sub_unit_type'] = $itemData['sub_unit_type'] ?? null;
                    $detailData['sub_unit_quantity'] = $itemData['sub_unit_quantity'] ?? 0;
                }
                
                TransactionDetail::create($detailData);

                // Update stock based on transaction type
                if ($validated['type'] === 'in') {
                    $item->increment('stock', $actualQuantityInPcs);
                } else {
                    if ($item->stock < $actualQuantityInPcs) {
                        throw new \Exception("Stok {$item->name} tidak mencukupi. Tersedia: {$item->stock} pcs, diminta: {$actualQuantityInPcs} pcs");
                    }
                    $item->decrement('stock', $actualQuantityInPcs);
                }

                $totalAmount += $subtotal;
            }

            // Apply discount and bonus (only for 'out' transactions)
            $discount = $validated['discount'] ?? 0;
            $bonus = $validated['bonus'] ?? 0;
            $finalTotal = $totalAmount - $discount - $bonus;
            
            // Make sure final total is not negative
            $finalTotal = max(0, $finalTotal);

            // Update total amount
            $transaction->update(['total_amount' => $finalTotal]);

            DB::commit();

            return redirect()->route('transactions.index')
                ->with('success', 'Transaksi berhasil dibuat!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function show(Transaction $transaction)
    {
        // All users (superadmin and admin) can view all transactions

        $transaction->load(['user', 'vehicle', 'details.item']);
        return view('transactions.show', compact('transaction'));
    }

    public function destroy(Transaction $transaction)
    {
        DB::beginTransaction();
        try {
            // Revert stock changes
            foreach ($transaction->details as $detail) {
                if ($transaction->type === 'in') {
                    $detail->item->decrement('stock', $detail->quantity);
                } else {
                    $detail->item->increment('stock', $detail->quantity);
                }
            }

            $transaction->delete();
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Transaksi berhasil dihapus dan stok telah dikembalikan!'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function updatePayment(Request $request, Transaction $transaction)
    {
        try {
            // Only allow updating payment status for 'out' transactions
            if ($transaction->type !== 'out') {
                return response()->json([
                    'success' => false,
                    'message' => 'Hanya transaksi barang keluar yang memiliki status pembayaran.'
                ], 400);
            }

            $validated = $request->validate([
                'payment_status' => 'required|in:paid,unpaid'
            ]);

            $transaction->update([
                'payment_status' => $validated['payment_status']
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Status pembayaran berhasil diupdate!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function export(Request $request)
    {
        $typeFilter = $request->query('type_filter');
        $paymentStatusFilter = $request->query('payment_status_filter');
        
        $filename = 'transactions';
        if ($typeFilter === 'in') {
            $filename .= '_masuk';
        } elseif ($typeFilter === 'out') {
            $filename .= '_keluar';
            if ($paymentStatusFilter === 'paid') {
                $filename .= '_lunas';
            } elseif ($paymentStatusFilter === 'unpaid') {
                $filename .= '_belum_lunas';
            }
        }
        $filename .= '_' . date('Y-m-d_His') . '.xlsx';
        
        return Excel::download(new TransactionsExport($typeFilter, $paymentStatusFilter), $filename);
    }

    public function invoice(Transaction $transaction)
    {
        $transaction->load(['user', 'vehicle', 'details.item']);
        $pdf = Pdf::loadView('transactions.invoice', compact('transaction'));
        return $pdf->download('invoice_' . $transaction->transaction_number . '.pdf');
    }
}
