@extends('layouts.app')

@section('title', 'Detail Supplier')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Detail Supplier</h1>
        <div>
            <a href="{{ route('suppliers.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
            @can('edit-suppliers')
            <a href="{{ route('suppliers.edit', $supplier) }}" class="btn btn-warning">
                <i class="fas fa-edit"></i> Edit
            </a>
            @endcan
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Informasi Supplier</h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <td class="fw-bold">Kode:</td>
                            <td>{{ $supplier->code }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Nama:</td>
                            <td>{{ $supplier->name }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Email:</td>
                            <td>{{ $supplier->email ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Telepon:</td>
                            <td>{{ $supplier->phone ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Contact Person:</td>
                            <td>{{ $supplier->contact_person ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Alamat:</td>
                            <td>{{ $supplier->address ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Status:</td>
                            <td>
                                @if($supplier->is_active)
                                    <span class="badge bg-success">Aktif</span>
                                @else
                                    <span class="badge bg-secondary">Tidak Aktif</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Dibuat:</td>
                            <td>{{ $supplier->created_at->format('d M Y H:i') }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Diperbarui:</td>
                            <td>{{ $supplier->updated_at->format('d M Y H:i') }}</td>
                        </tr>
                    </table>

                    @if($supplier->description)
                    <hr>
                    <p class="mb-0"><strong>Deskripsi:</strong></p>
                    <p class="text-muted">{{ $supplier->description }}</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Item dari Supplier Ini</h5>
                    @can('create-items')
                    <a href="{{ route('items.create', ['supplier_id' => $supplier->id]) }}" class="btn btn-sm btn-primary">
                        <i class="fas fa-plus"></i> Tambah Item
                    </a>
                    @endcan
                </div>
                <div class="card-body">
                    @if($supplier->items->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Kode</th>
                                    <th>Nama Item</th>
                                    <th>Kategori</th>
                                    <th>Stok</th>
                                    <th>Harga Beli</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($supplier->items as $item)
                                <tr>
                                    <td>{{ $item->code }}</td>
                                    <td>{{ $item->name }}</td>
                                    <td>{{ $item->category->name }}</td>
                                    <td>
                                        {{ $item->stock }} {{ $item->unit }}
                                        @if($item->isLowStock())
                                            <span class="badge bg-warning">Low</span>
                                        @endif
                                    </td>
                                    <td>Rp {{ number_format($item->purchase_price, 0, ',', '.') }}</td>
                                    <td>
                                        @if($item->is_active)
                                            <span class="badge bg-success">Aktif</span>
                                        @else
                                            <span class="badge bg-secondary">Tidak Aktif</span>
                                        @endif
                                    </td>
                                    <td>
                                        @can('view-items')
                                        <a href="{{ route('items.show', $item) }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @endcan
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="text-center py-4">
                        <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                        <p class="text-muted">Belum ada item dari supplier ini</p>
                        @can('create-items')
                        <a href="{{ route('items.create', ['supplier_id' => $supplier->id]) }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Tambah Item Pertama
                        </a>
                        @endcan
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
