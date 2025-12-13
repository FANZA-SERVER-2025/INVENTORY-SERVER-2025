<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use App\Exports\VehiclesExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class VehicleController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view-vehicles', ['only' => ['index', 'show']]);
        $this->middleware('permission:create-vehicles', ['only' => ['create', 'store']]);
        $this->middleware('permission:edit-vehicles', ['only' => ['edit', 'update']]);
        $this->middleware('permission:delete-vehicles', ['only' => ['destroy']]);
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Vehicle::query();

            if ($request->has('search') && $request->search['value']) {
                $search = $request->search['value'];
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('plate_number', 'like', "%{$search}%")
                      ->orWhere('type', 'like', "%{$search}%")
                      ->orWhere('brand', 'like', "%{$search}%");
                });
            }

            $total = $query->count();

            if ($request->has('order')) {
                $columns = ['id', 'name', 'plate_number', 'type', 'brand', 'year', 'is_active'];
                $columnIndex = $request->order[0]['column'];
                $columnName = $columns[$columnIndex] ?? 'id';
                $direction = $request->order[0]['dir'];
                $query->orderBy($columnName, $direction);
            }

            $start = $request->start ?? 0;
            $length = $request->length ?? 10;
            $vehicles = $query->skip($start)->take($length)->get();

            return response()->json([
                'draw' => $request->draw,
                'recordsTotal' => Vehicle::count(),
                'recordsFiltered' => $total,
                'data' => $vehicles
            ]);
        }

        return view('vehicles.index');
    }

    public function create()
    {
        return view('vehicles.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'plate_number' => 'required|string|max:20|unique:vehicles,plate_number',
            'type' => 'required|string|max:100',
            'brand' => 'nullable|string|max:100',
            'year' => 'nullable|integer|min:1900|max:' . (date('Y') + 1),
            'description' => 'nullable|string',
            'is_active' => 'boolean'
        ]);

        $validated['is_active'] = $request->has('is_active');

        Vehicle::create($validated);

        return redirect()->route('vehicles.index')
            ->with('success', 'Kendaraan berhasil ditambahkan!');
    }

    public function show(Vehicle $vehicle)
    {
        $vehicle->load(['transactions' => function($query) {
            $query->latest()->take(10);
        }]);
        
        return view('vehicles.show', compact('vehicle'));
    }

    public function edit(Vehicle $vehicle)
    {
        return view('vehicles.edit', compact('vehicle'));
    }

    public function update(Request $request, Vehicle $vehicle)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'plate_number' => 'required|string|max:20|unique:vehicles,plate_number,' . $vehicle->id,
            'type' => 'required|string|max:100',
            'brand' => 'nullable|string|max:100',
            'year' => 'nullable|integer|min:1900|max:' . (date('Y') + 1),
            'description' => 'nullable|string',
            'is_active' => 'boolean'
        ]);

        $validated['is_active'] = $request->has('is_active');

        $vehicle->update($validated);

        return redirect()->route('vehicles.index')
            ->with('success', 'Kendaraan berhasil diperbarui!');
    }

    public function destroy(Vehicle $vehicle)
    {
        // Check if vehicle is used in any transactions
        $transactionCount = $vehicle->transactions()->count();
        
        if ($transactionCount > 0) {
            return response()->json([
                'success' => false,
                'message' => "Kendaraan '{$vehicle->name}' tidak dapat dihapus karena masih digunakan di {$transactionCount} transaksi. Hapus transaksi terkait terlebih dahulu."
            ], 400);
        }

        try {
            $vehicle->delete();

            return response()->json([
                'success' => true,
                'message' => 'Kendaraan berhasil dihapus!'
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
        return Excel::download(new VehiclesExport, 'vehicles_' . date('Y-m-d_His') . '.xlsx');
    }
}
