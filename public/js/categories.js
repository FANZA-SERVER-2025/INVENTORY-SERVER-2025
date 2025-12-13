// Categories DataTable and CRUD Operations

$(document).ready(function() {
    // Initialize DataTable
    let table = $('#categoriesTable').DataTable({
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
            { data: 'description', name: 'description' },
            {
                data: 'is_active',
                name: 'is_active',
                render: function(data) {
                    return data ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-danger">Inactive</span>';
                }
            },
            {
                data: 'items_count',
                name: 'items_count',
                searchable: false,
                render: function(data) {
                    return '<span class="badge bg-info">' + (data || 0) + '</span>';
                }
            },
            {
                data: 'created_at',
                name: 'created_at',
                render: function(data) {
                    return new Date(data).toLocaleDateString('id-ID');
                }
            },
            {
                data: 'id',
                orderable: false,
                searchable: false,
                render: function(data, type, row) {
                    var actions = '';
                    
                    if (typeof canViewCategories !== 'undefined' && canViewCategories) {
                        actions += '<a href="/categories/' + data + '" class="btn btn-sm btn-info me-1" title="View"><i class="fas fa-eye"></i></a>';
                    }
                    
                    if (typeof canEditCategories !== 'undefined' && canEditCategories) {
                        actions += '<a href="/categories/' + data + '/edit" class="btn btn-sm btn-warning me-1" title="Edit"><i class="fas fa-edit"></i></a>';
                    }
                    
                    if (typeof canDeleteCategories !== 'undefined' && canDeleteCategories) {
                        actions += '<button onclick="deleteCategory(' + data + ')" class="btn btn-sm btn-danger" title="Delete"><i class="fas fa-trash"></i></button>';
                    }
                    
                    return actions;
                }
            }
        ],
        order: [[0, 'desc']],
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json'
        }
    });
});

// Delete Category
function deleteCategory(id) {
    Swal.fire({
        title: 'Apakah Anda yakin?',
        text: "Data kategori akan dihapus secara permanen!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '/categories/' + id,
                type: 'DELETE',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    Swal.fire('Berhasil!', response.message, 'success');
                    $('#categoriesTable').DataTable().ajax.reload();
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
}
