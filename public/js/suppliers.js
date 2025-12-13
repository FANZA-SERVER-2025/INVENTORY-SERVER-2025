// Suppliers Page

$(document).ready(function() {
    initDataTable({
        tableId: '#suppliersTable',
        columns: [
            { data: 'id', name: 'id' },
            { data: 'code', name: 'code' },
            { data: 'name', name: 'name' },
            { data: 'email', name: 'email' },
            { data: 'phone', name: 'phone' },
            { data: 'contact_person', name: 'contact_person' },
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
                    
                    if (typeof canViewSuppliers !== 'undefined' && canViewSuppliers) {
                        buttons += '<a href="/suppliers/' + data.id + '" class="btn btn-sm btn-info" title="Lihat Detail">' +
                                  '<i class="fas fa-eye"></i></a> ';
                    }
                    
                    if (typeof canEditSuppliers !== 'undefined' && canEditSuppliers) {
                        buttons += '<a href="/suppliers/' + data.id + '/edit" class="btn btn-sm btn-warning" title="Edit">' +
                                  '<i class="fas fa-edit"></i></a> ';
                    }
                    
                    if (typeof canDeleteSuppliers !== 'undefined' && canDeleteSuppliers) {
                        buttons += '<button class="btn btn-sm btn-danger delete-btn" data-id="' + data.id + '" title="Hapus">' +
                                  '<i class="fas fa-trash"></i></button>';
                    }
                    
                    return buttons;
                }
            }
        ],
        deleteUrl: '/suppliers',
        deleteTitle: 'Hapus Supplier?',
        deleteMessage: 'Data supplier akan dihapus secara permanen!',
        deleteSuccessMessage: 'Supplier berhasil dihapus.'
    });
});
