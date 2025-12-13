<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use App\Exports\SuppliersExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class SupplierController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view-suppliers', ['only' => ['index', 'show']]);
        $this->middleware('permission:create-suppliers', ['only' => ['create', 'store']]);
        $this->middleware('permission:edit-suppliers', ['only' => ['edit', 'update']]);
        $this->middleware('permission:delete-suppliers', ['only' => ['destroy']]);
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Supplier::query();

            // Search
            if ($request->has('search') && $request->search['value']) {
                $search = $request->search['value'];
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('code', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('phone', 'like', "%{$search}%")
                      ->orWhere('contact_person', 'like', "%{$search}%");
                });
            }

            $total = $query->count();

            // Sorting
            if ($request->has('order')) {
                $columns = ['id', 'code', 'name', 'email', 'phone', 'contact_person', 'is_active'];
                $columnIndex = $request->order[0]['column'];
                $columnName = $columns[$columnIndex] ?? 'id';
                $direction = $request->order[0]['dir'];
                $query->orderBy($columnName, $direction);
            }

            // Pagination
            $start = $request->start ?? 0;
            $length = $request->length ?? 10;
            $suppliers = $query->skip($start)->take($length)->get();

            return response()->json([
                'draw' => $request->draw,
                'recordsTotal' => Supplier::count(),
                'recordsFiltered' => $total,
                'data' => $suppliers
            ]);
        }

        return view('suppliers.index');
    }

    public function create()
    {
        return view('suppliers.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:suppliers,code',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'contact_person' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean'
        ]);

        $validated['is_active'] = $request->has('is_active');

        Supplier::create($validated);

        return redirect()->route('suppliers.index')
            ->with('success', 'Supplier berhasil ditambahkan!');
    }

    public function show(Supplier $supplier)
    {
        $supplier->load(['items' => function($query) {
            $query->latest()->take(10);
        }]);
        
        return view('suppliers.show', compact('supplier'));
    }

    public function edit(Supplier $supplier)
    {
        return view('suppliers.edit', compact('supplier'));
    }

    public function update(Request $request, Supplier $supplier)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:suppliers,code,' . $supplier->id,
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'contact_person' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean'
        ]);

        $validated['is_active'] = $request->has('is_active');

        $supplier->update($validated);

        return redirect()->route('suppliers.index')
            ->with('success', 'Supplier berhasil diperbarui!');
    }

    public function destroy(Supplier $supplier)
    {
        // Check if supplier has items
        $itemCount = $supplier->items()->count();
        
        if ($itemCount > 0) {
            return response()->json([
                'success' => false,
                'message' => "Supplier '{$supplier->name}' tidak dapat dihapus karena masih memiliki {$itemCount} item. Hapus atau pindahkan item terlebih dahulu."
            ], 400);
        }

        try {
            $supplier->delete();

            return response()->json([
                'success' => true,
                'message' => 'Supplier berhasil dihapus!'
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
        return Excel::download(new SuppliersExport, 'suppliers_' . date('Y-m-d_His') . '.xlsx');
    }
}
