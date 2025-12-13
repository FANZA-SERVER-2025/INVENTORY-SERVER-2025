// Users Page

$(document).ready(function() {
    let table = $('#usersTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: window.location.href,
            type: 'GET'
        },
        columns: [
            { data: 'id', name: 'id' },
            { data: 'name', name: 'name' },
            { data: 'email', name: 'email' },
            { data: 'phone', name: 'phone' },
            {
                data: 'roles',
                name: 'roles',
                orderable: false,
                searchable: false,
                render: function(data) {
                    if (!data || data.length === 0) return '-';
                    
                    let badges = '';
                    data.forEach(function(role) {
                        let badgeClass = 'bg-secondary';
                        if (role.name === 'superadmin') {
                            badgeClass = 'bg-danger';
                        } else if (role.name === 'admin') {
                            badgeClass = 'bg-primary';
                        } else if (role.name === 'operator') {
                            badgeClass = 'bg-warning';
                        } else if (role.name === 'customer') {
                            badgeClass = 'bg-info';
                        }
                        badges += '<span class="badge ' + badgeClass + ' me-1">' + role.name + '</span>';
                    });
                    return badges;
                }
            },
            {
                data: 'is_active',
                name: 'is_active',
                render: function(data) {
                    return data 
                        ? '<span class="badge bg-success">Aktif</span>'
                        : '<span class="badge bg-secondary">Tidak Aktif</span>';
                }
            },
            {
                data: null,
                orderable: false,
                searchable: false,
                render: function(data) {
                    let buttons = '';
                    
                    // Check if user has superadmin or admin role
                    let isSuperadmin = false;
                    let isAdmin = false;
                    if (data.roles && data.roles.length > 0) {
                        isSuperadmin = data.roles.some(role => role.name === 'superadmin');
                        isAdmin = data.roles.some(role => role.name === 'admin');
                    }
                    
                    if (typeof canViewUsers !== 'undefined' && canViewUsers) {
                        buttons += '<a href="/users/' + data.id + '" class="btn btn-sm btn-info" title="Lihat Detail">' +
                                  '<i class="fas fa-eye"></i></a> ';
                    }
                    
                    if (typeof canEditUsers !== 'undefined' && canEditUsers) {
                        buttons += '<a href="/users/' + data.id + '/edit" class="btn btn-sm btn-warning" title="Edit">' +
                                  '<i class="fas fa-edit"></i></a> ';
                    }
                    
                    // Don't show delete button for superadmin or admin users
                    if (typeof canDeleteUsers !== 'undefined' && canDeleteUsers && !isSuperadmin && !isAdmin) {
                        buttons += '<button class="btn btn-sm btn-danger delete-btn" data-id="' + data.id + '" title="Hapus">' +
                                  '<i class="fas fa-trash"></i></button>';
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

    // Delete user
    $('#usersTable').on('click', '.delete-btn', function() {
        let id = $(this).data('id');
        
        Swal.fire({
            title: 'Hapus User?',
            text: "Data user akan dihapus secara permanen!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '/users/' + id,
                    type: 'DELETE',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        Swal.fire('Berhasil!', response.message || 'User berhasil dihapus.', 'success');
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
