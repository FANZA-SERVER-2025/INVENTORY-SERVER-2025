// Vehicles Page

$(document).ready(function() {
    initDataTable({
        tableId: '#vehiclesTable',
        columns: [
            { data: 'id', name: 'id' },
            { data: 'name', name: 'name' },
            { data: 'plate_number', name: 'plate_number' },
            { data: 'type', name: 'type' },
            { data: 'brand', name: 'brand' },
            { data: 'year', name: 'year' },
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
                    
                    if (typeof canViewVehicles !== 'undefined' && canViewVehicles) {
                        buttons += '<a href="/vehicles/' + data.id + '" class="btn btn-sm btn-info" title="Lihat Detail">' +
                                  '<i class="fas fa-eye"></i></a> ';
                    }
                    
                    if (typeof canEditVehicles !== 'undefined' && canEditVehicles) {
                        buttons += '<a href="/vehicles/' + data.id + '/edit" class="btn btn-sm btn-warning" title="Edit">' +
                                  '<i class="fas fa-edit"></i></a> ';
                    }
                    
                    if (typeof canDeleteVehicles !== 'undefined' && canDeleteVehicles) {
                        buttons += '<button class="btn btn-sm btn-danger delete-btn" data-id="' + data.id + '" title="Hapus">' +
                                  '<i class="fas fa-trash"></i></button>';
                    }
                    
                    return buttons;
                }
            }
        ],
        deleteUrl: '/vehicles',
        deleteTitle: 'Hapus Kendaraan?',
        deleteMessage: 'Data kendaraan akan dihapus secara permanen!',
        deleteSuccessMessage: 'Kendaraan berhasil dihapus.'
    });
});
