@extends('layouts.app')

@section('title', 'Data Item')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Data Item</h1>
        <div>
            @can('export-items')
            <a href="{{ route('items.export') }}" class="btn btn-success">
                <i class="fas fa-file-excel"></i> Export Excel
            </a>
            @endcan
            @can('create-items')
            <a href="{{ route('items.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Tambah Item
            </a>
            @endcan
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table id="itemsTable" class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Kode</th>
                            <th>Nama</th>
                            <th>Kategori</th>
                            <th>Supplier</th>
                            <th>Stok</th>
                            <th>Unit</th>
                            <th>Harga Beli</th>
                            <th>Harga Jual</th>
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
$(document).ready(function() {
    let table = $('#itemsTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{ route('items.index') }}',
        columns: [
            { data: 'id', name: 'id' },
            { data: 'code', name: 'code' },
            { data: 'name', name: 'name' },
            { 
                data: 'category',
                name: 'category.name',
                render: function(data) {
                    return data ? data.name : '-';
                }
            },
            { 
                data: 'supplier',
                name: 'supplier.name',
                render: function(data) {
                    return data ? data.name : '-';
                }
            },
            {
                data: null,
                name: 'stock',
                render: function(data) {
                    let badge = '';
                    if (data.stock < data.minimum_stock) {
                        badge = '<span class="badge bg-warning ms-1">Low</span>';
                    }
                    return data.stock + badge;
                }
            },
            { data: 'unit', name: 'unit' },
            {
                data: 'purchase_price',
                name: 'purchase_price',
                render: function(data) {
                    return 'Rp ' + new Intl.NumberFormat('id-ID').format(data);
                }
            },
            {
                data: 'selling_price',
                name: 'selling_price',
                render: function(data) {
                    return 'Rp ' + new Intl.NumberFormat('id-ID').format(data);
                }
            },
            {
                data: 'is_active',
                name: 'is_active',
                render: function(data) {
                    return data ? '<span class="badge bg-success">Aktif</span>' : '<span class="badge bg-secondary">Tidak Aktif</span>';
                }
            },
            {
                data: null,
                orderable: false,
                searchable: false,
                render: function(data) {
                    let buttons = '';
                    
                    @can('view-items')
                    buttons += `<a href="/items/${data.id}" class="btn btn-sm btn-info" title="Lihat Detail">
                        <i class="fas fa-eye"></i>
                    </a> `;
                    @endcan
                    
                    @can('edit-items')
                    buttons += `<a href="/items/${data.id}/edit" class="btn btn-sm btn-warning" title="Edit">
                        <i class="fas fa-edit"></i>
                    </a> `;
                    @endcan
                    
                    @can('delete-items')
                    buttons += `<button class="btn btn-sm btn-danger delete-btn" data-id="${data.id}" title="Hapus">
                        <i class="fas fa-trash"></i>
                    </button>`;
                    @endcan
                    
                    return buttons;
                }
            }
        ],
        order: [[0, 'desc']],
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json'
        }
    });

    $('#itemsTable').on('click', '.delete-btn', function() {
        let id = $(this).data('id');
        
        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: "Data item akan dihapus secara permanen!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/items/${id}`,
                    type: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        Swal.fire('Berhasil!', response.message, 'success');
                        table.ajax.reload();
                    },
                    error: function(xhr) {
                        let errorMessage = 'Terjadi kesalahan saat menghapus data.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }
                        Swal.fire('Error!', errorMessage, 'error');
                    }
                });
            }
        });
    });
});
</script>
@endpush
@endsection
