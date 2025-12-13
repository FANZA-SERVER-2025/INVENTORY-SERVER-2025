@extends('layouts.app')

@section('title', 'Data Kendaraan')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Data Kendaraan</h1>
        <div>
            @can('export-vehicles')
            <a href="{{ route('vehicles.export') }}" class="btn btn-success">
                <i class="fas fa-file-excel"></i> Export Excel
            </a>
            @endcan
            @can('create-vehicles')
            <a href="{{ route('vehicles.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Tambah Kendaraan
            </a>
            @endcan
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table id="vehiclesTable" class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nama</th>
                            <th>Plat Nomor</th>
                            <th>Tipe</th>
                            <th>Merk</th>
                            <th>Tahun</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    const canViewVehicles = {{ auth()->user()->can('view-vehicles') ? 'true' : 'false' }};
    const canEditVehicles = {{ auth()->user()->can('edit-vehicles') ? 'true' : 'false' }};
    const canDeleteVehicles = {{ auth()->user()->can('delete-vehicles') ? 'true' : 'false' }};
</script>
<script src="{{ asset('js/datatable-crud.js') }}"></script>
<script src="{{ asset('js/vehicles.js') }}"></script>
@endpush
@endsection
