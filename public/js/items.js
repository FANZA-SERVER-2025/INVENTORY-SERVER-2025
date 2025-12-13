// Items DataTable and CRUD Operations

$(document).ready(function() {
    // Initialize DataTable
    let table = $('#itemsTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: window.location.href,
            type: 'GET'
        },
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
                data: 'stock',
                name: 'stock',
                render: function(data, type, row) {
                    let badgeClass = 'bg-success';
                    if (data < row.minimum_stock) {
                        badgeClass = 'bg-danger';
                    } else if (data < row.minimum_stock * 2) {
                        badgeClass = 'bg-warning';
                    }
                    return '<span class="badge ' + badgeClass + '">' + data + '</span>';
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
                    
                    // Add to Cart button for active items
                    if (typeof canViewCart !== 'undefined' && canViewCart) {
                        if (data.is_active && data.stock > 0) {
                            buttons += '<button class="btn btn-sm btn-success add-to-cart-btn me-1" ' +
                                      'data-id="' + data.id + '" ' +
                                      'data-name="' + data.name + '" ' +
                                      'data-stock="' + data.stock + '" ' +
                                      'title="Tambah ke Keranjang">' +
                                      '<i class="fas fa-cart-plus"></i>' +
                                      '</button> ';
                        }
                    }
                    
                    if (typeof canViewItems !== 'undefined' && canViewItems) {
                        buttons += '<a href="/items/' + data.id + '" class="btn btn-sm btn-info" title="Lihat Detail">' +
                                  '<i class="fas fa-eye"></i>' +
                                  '</a> ';
                    }
                    
                    if (typeof canEditItems !== 'undefined' && canEditItems) {
                        buttons += '<a href="/items/' + data.id + '/edit" class="btn btn-sm btn-warning" title="Edit">' +
                                  '<i class="fas fa-edit"></i>' +
                                  '</a> ';
                    }
                    
                    if (typeof canDeleteItems !== 'undefined' && canDeleteItems) {
                        buttons += '<button class="btn btn-sm btn-danger delete-btn" data-id="' + data.id + '" title="Hapus">' +
                                  '<i class="fas fa-trash"></i>' +
                                  '</button>';
                    }
                    
                    return buttons;
                }
            }
        ],
        order: [[0, 'desc']],
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json'
        }
    });

    // Delete Item
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
                    url: '/items/' + id,
                    type: 'DELETE',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        Swal.fire('Berhasil!', response.message, 'success');
                        table.ajax.reload();
                    },
                    error: function(xhr) {
                        Swal.fire('Error!', 'Terjadi kesalahan saat menghapus data.', 'error');
                    }
                });
            }
        });
    });
});
