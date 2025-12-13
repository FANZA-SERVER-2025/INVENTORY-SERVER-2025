// Transactions Page

$(document).ready(function() {
    let table = $('#transactionsTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: window.location.href,
            type: 'GET',
            data: function(d) {
                d.type_filter = $('#filterType').val();
                d.payment_status_filter = $('#filterPaymentStatus').val();
            }
        },
        columns: [
            { data: 'id', name: 'id' },
            { data: 'transaction_number', name: 'transaction_number' },
            {
                data: 'type',
                name: 'type',
                render: function(data) {
                    return data === 'in' 
                        ? '<span class="badge bg-success"><i class="fas fa-arrow-down"></i> Masuk</span>' 
                        : '<span class="badge bg-danger"><i class="fas fa-arrow-up"></i> Keluar</span>';
                }
            },
            {
                data: 'transaction_date',
                name: 'transaction_date',
                render: function(data) {
                    return new Date(data).toLocaleDateString('id-ID');
                }
            },
            {
                data: 'user',
                name: 'user.name',
                render: function(data) {
                    return data ? data.name : '-';
                }
            },
            {
                data: 'total_amount',
                name: 'total_amount',
                render: function(data) {
                    return 'Rp ' + new Intl.NumberFormat('id-ID').format(data);
                }
            },
            {
                data: 'payment_status',
                name: 'payment_status',
                render: function(data, type, row) {
                    if (row.type === 'out') {
                        return data === 'paid' 
                            ? '<span class="badge bg-success"><i class="fas fa-check-circle"></i> Lunas</span>' 
                            : '<span class="badge bg-warning text-dark"><i class="fas fa-clock"></i> Belum Lunas</span>';
                    }
                    return '<span class="text-muted">-</span>';
                }
            },
            {
                data: null,
                orderable: false,
                searchable: false,
                render: function(data) {
                    let buttons = '';
                    
                    if (typeof canViewTransactions !== 'undefined' && canViewTransactions) {
                        buttons += '<a href="/transactions/' + data.id + '" class="btn btn-sm btn-info me-1" title="Detail">' +
                                  '<i class="fas fa-eye"></i></a>';
                        buttons += '<a href="/transactions/' + data.id + '/invoice" class="btn btn-sm btn-secondary me-1" title="Invoice" target="_blank">' +
                                  '<i class="fas fa-file-pdf"></i></a>';
                    }
                    
                    // Edit button for unpaid transactions
                    if (typeof canEditTransactions !== 'undefined' && canEditTransactions && 
                        data.type === 'out' && data.payment_status === 'unpaid') {
                        buttons += '<button class="btn btn-sm btn-warning edit-payment-btn me-1" data-id="' + data.id + '" title="Update Pembayaran">' +
                                  '<i class="fas fa-edit"></i></button>';
                    }
                    
                    if (typeof canDeleteTransactions !== 'undefined' && canDeleteTransactions) {
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

    // Filter by transaction type
    $('#filterType').on('change', function() {
        const type = $(this).val();
        
        // Show/hide payment status filter
        if (type === 'out') {
            $('#paymentStatusFilter').show();
        } else {
            $('#paymentStatusFilter').hide();
            $('#filterPaymentStatus').val(''); // Reset payment status filter
        }
        
        table.ajax.reload();
    });

    // Filter by payment status
    $('#filterPaymentStatus').on('change', function() {
        table.ajax.reload();
    });

    // Export with current filters
    $('#exportBtn').on('click', function() {
        const typeFilter = $('#filterType').val();
        const paymentStatusFilter = $('#filterPaymentStatus').val();
        
        let url = '/transactions/export?';
        if (typeFilter) {
            url += 'type_filter=' + typeFilter;
        }
        if (paymentStatusFilter) {
            url += (typeFilter ? '&' : '') + 'payment_status_filter=' + paymentStatusFilter;
        }
        
        window.location.href = url;
    });

    // Edit Payment Status
    $('#transactionsTable').on('click', '.edit-payment-btn', function() {
        let id = $(this).data('id');
        
        Swal.fire({
            title: 'Update Status Pembayaran',
            text: "Ubah status menjadi LUNAS?",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Lunas!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '/transactions/' + id + '/update-payment',
                    type: 'PATCH',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        payment_status: 'paid'
                    },
                    success: function(response) {
                        Swal.fire('Berhasil!', response.message, 'success');
                        table.ajax.reload();
                    },
                    error: function(xhr) {
                        Swal.fire('Error!', 'Terjadi kesalahan saat mengupdate status pembayaran.', 'error');
                    }
                });
            }
        });
    });

    // Delete Transaction
    $('#transactionsTable').on('click', '.delete-btn', function() {
        let id = $(this).data('id');
        
        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: "Transaksi akan dihapus dan stok akan dikembalikan!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '/transactions/' + id,
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
