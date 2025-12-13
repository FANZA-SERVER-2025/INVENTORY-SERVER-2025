<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Category;
use App\Models\Supplier;
use App\Exports\ItemsExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class ItemController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view-items', ['only' => ['index', 'show']]);
        $this->middleware('permission:create-items', ['only' => ['create', 'store']]);
        $this->middleware('permission:edit-items', ['only' => ['edit', 'update']]);
        $this->middleware('permission:delete-items', ['only' => ['destroy']]);
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Item::with(['category', 'supplier']);

            if ($request->has('search') && $request->search['value']) {
                $search = $request->search['value'];
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('code', 'like', "%{$search}%")
                      ->orWhere('unit', 'like', "%{$search}%")
                      ->orWhereHas('category', function($q) use ($search) {
                          $q->where('name', 'like', "%{$search}%");
                      })
                      ->orWhereHas('supplier', function($q) use ($search) {
                          $q->where('name', 'like', "%{$search}%");
                      });
                });
            }

            $total = $query->count();

            if ($request->has('order')) {
                $columns = ['id', 'code', 'name', 'category_id', 'supplier_id', 'stock', 'unit', 'purchase_price', 'selling_price', 'is_active'];
                $columnIndex = $request->order[0]['column'];
                $columnName = $columns[$columnIndex] ?? 'id';
                $direction = $request->order[0]['dir'];
                $query->orderBy($columnName, $direction);
            }

            $start = $request->start ?? 0;
            $length = $request->length ?? 10;
            $items = $query->skip($start)->take($length)->get();

            return response()->json([
                'draw' => $request->draw,
                'recordsTotal' => Item::count(),
                'recordsFiltered' => $total,
                'data' => $items
            ]);
        }

        return view('items.index');
    }

    public function create(Request $request)
    {
        $categories = Category::where('is_active', true)->get();
        $suppliers = Supplier::where('is_active', true)->get();
        $supplierId = $request->get('supplier_id');
        
        return view('items.create', compact('categories', 'suppliers', 'supplierId'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'supplier_id' => 'required|exists:suppliers,id',
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:items,code',
            'stock' => 'required|integer|min:0',
            'minimum_stock' => 'required|integer|min:0',
            'box_type' => 'nullable|in:dozen,pcs',
            'box_quantity' => 'nullable|integer|min:1',
            'purchase_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'description' => 'nullable|string',
            'is_active' => 'boolean'
        ]);

        $validated['is_active'] = $request->has('is_active');

        // Clear box_quantity if box_type is not set
        if (empty($validated['box_type'])) {
            $validated['box_quantity'] = null;
        }

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('items', 'public');
        }

        Item::create($validated);

        return redirect()->route('items.index')
            ->with('success', 'Item berhasil ditambahkan!');
    }

    public function show(Item $item)
    {
        $item->load(['category', 'supplier']);
        
        return view('items.show', compact('item'));
    }

    public function edit(Item $item)
    {
        $categories = Category::where('is_active', true)->get();
        $suppliers = Supplier::where('is_active', true)->get();
        
        return view('items.edit', compact('item', 'categories', 'suppliers'));
    }

    public function update(Request $request, Item $item)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'supplier_id' => 'required|exists:suppliers,id',
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:items,code,' . $item->id,
            'stock' => 'required|integer|min:0',
            'minimum_stock' => 'required|integer|min:0',
            'box_type' => 'nullable|in:dozen,pcs',
            'box_quantity' => 'nullable|integer|min:1',
            'purchase_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'description' => 'nullable|string',
            'is_active' => 'boolean'
        ]);

        $validated['is_active'] = $request->has('is_active');

        // Clear box_quantity if box_type is not set
        if (empty($validated['box_type'])) {
            $validated['box_quantity'] = null;
        }

        if ($request->hasFile('image')) {
            // Delete old image
            if ($item->image) {
                Storage::disk('public')->delete($item->image);
            }
            $validated['image'] = $request->file('image')->store('items', 'public');
        }

        $item->update($validated);

        return redirect()->route('items.index')
            ->with('success', 'Item berhasil diperbarui!');
    }

    public function destroy(Item $item)
    {
        // Check if item is used in any transactions
        $transactionCount = $item->transactionDetails()->count();
        
        if ($transactionCount > 0) {
            return response()->json([
                'success' => false,
                'message' => "Item '{$item->name}' tidak dapat dihapus karena masih digunakan di {$transactionCount} transaksi. Hapus transaksi terkait terlebih dahulu atau ubah status item menjadi tidak aktif."
            ], 400);
        }

        try {
            // Delete image if exists
            if ($item->image) {
                Storage::disk('public')->delete($item->image);
            }
            
            $item->delete();

            return response()->json([
                'success' => true,
                'message' => 'Item berhasil dihapus!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function export()
    {
        return Excel::download(new ItemsExport, 'items_' . date('Y-m-d_His') . '.xlsx');
    }
}
