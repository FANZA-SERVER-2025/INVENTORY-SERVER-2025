// Roles Page

$(document).ready(function() {
    let table = $('#rolesTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: window.location.href,
            type: 'GET'
        },
        columns: [
            { data: 'id', name: 'id' },
            {
                data: 'name',
                name: 'name',
                render: function(data) {
                    return '<span class="badge bg-primary">' + data + '</span>';
                }
            },
            { data: 'permissions_count', name: 'permissions_count' },
            { data: 'users_count', name: 'users_count' },
            {
                data: null,
                orderable: false,
                searchable: false,
                render: function(data) {
                    let buttons = '';
                    
                    // Don't show delete button for superadmin or admin role
                    let isSuperadmin = data.name === 'superadmin';
                    let isAdmin = data.name === 'admin';
                    
                    if (typeof canViewRoles !== 'undefined' && canViewRoles) {
                        buttons += '<a href="/roles/' + data.id + '" class="btn btn-sm btn-info" title="Lihat Detail">' +
                                  '<i class="fas fa-eye"></i></a> ';
                    }
                    
                    if (typeof canEditRoles !== 'undefined' && canEditRoles) {
                        buttons += '<a href="/roles/' + data.id + '/edit" class="btn btn-sm btn-warning" title="Edit">' +
                                  '<i class="fas fa-edit"></i></a> ';
                    }
                    
                    // Don't show delete button for superadmin or admin role
                    if (typeof canDeleteRoles !== 'undefined' && canDeleteRoles && !isSuperadmin && !isAdmin) {
                        buttons += '<button class="btn btn-sm btn-danger delete-btn" data-id="' + data.id + '" title="Hapus">' +
                                  '<i class="fas fa-trash"></i></button>';
                    }
                    
                    return buttons;
                }
            }
        ],
        order: [[0, 'asc']],
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json'
        }
    });

    // Delete role
    $('#rolesTable').on('click', '.delete-btn', function() {
        let id = $(this).data('id');
        
        Swal.fire({
            title: 'Hapus Role?',
            text: "Data role akan dihapus secara permanen!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '/roles/' + id,
                    type: 'DELETE',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        Swal.fire('Berhasil!', response.message || 'Role berhasil dihapus.', 'success');
                        table.ajax.reload();
                    },
                    error: function(xhr) {
                        let message = 'Terjadi kesalahan saat menghapus data.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            message = xhr.responseJSON.message;
                        }
                        Swal.fire('Error!', message, 'error');
                    }
                });
            }
        });
    });
});
