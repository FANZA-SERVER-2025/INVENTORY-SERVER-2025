@extends('layouts.app')

@section('title', 'Detail Item')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Detail Item</h1>
        <div>
            <a href="{{ route('items.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
            @can('edit-items')
            <a href="{{ route('items.edit', $item) }}" class="btn btn-warning">
                <i class="fas fa-edit"></i> Edit
            </a>
            @endcan
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Informasi Item</h5>
                </div>
                <div class="card-body">
                    @if($item->image)
                        <div class="text-center mb-3">
                            <img src="{{ asset('storage/' . $item->image) }}" class="img-fluid rounded" style="max-height: 250px;">
                        </div>
                    @else
                        <div class="text-center mb-3 p-4 bg-light rounded">
                            <i class="fas fa-box fa-4x text-muted"></i>
                            <p class="text-muted mt-2">Tidak ada gambar</p>
                        </div>
                    @endif

                    <table class="table table-borderless">
                        <tr>
                            <td class="fw-bold">Kode:</td>
                            <td>{{ $item->code }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Nama:</td>
                            <td>{{ $item->name }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Kategori:</td>
                            <td>
                                <a href="{{ route('categories.show', $item->category) }}">
                                    {{ $item->category->name }}
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Supplier:</td>
                            <td>
                                <a href="{{ route('suppliers.show', $item->supplier) }}">
                                    {{ $item->supplier->name }}
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Stok:</td>
                            <td>
                                <strong>{{ $item->stock }} {{ $item->unit }}</strong>
                                @if($item->isLowStock())
                                    <span class="badge bg-warning">Low Stock</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Stok Minimum:</td>
                            <td>{{ $item->minimum_stock }} {{ $item->unit }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Harga Beli:</td>
                            <td>Rp {{ number_format($item->purchase_price, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Harga Jual:</td>
                            <td>Rp {{ number_format($item->selling_price, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Status:</td>
                            <td>
                                @if($item->is_active)
                                    <span class="badge bg-success">Aktif</span>
                                @else
                                    <span class="badge bg-secondary">Tidak Aktif</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Dibuat:</td>
                            <td>{{ $item->created_at->format('d M Y H:i') }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Diperbarui:</td>
                            <td>{{ $item->updated_at->format('d M Y H:i') }}</td>
                        </tr>
                    </table>

                    @if($item->description)
                    <hr>
                    <p class="mb-0"><strong>Deskripsi:</strong></p>
                    <p class="text-muted">{{ $item->description }}</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <!-- Placeholder for future content -->
        </div>
    </div>
</div>
@endsection


