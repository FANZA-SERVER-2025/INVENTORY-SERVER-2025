@extends('layouts.app')

@section('title', 'Buat Transaksi')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Buat Transaksi Baru</h1>
        <a href="{{ route('transactions.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    <form action="{{ route('transactions.store') }}" method="POST" id="transactionForm">
        @csrf
        
        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Detail Transaksi</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="type" class="form-label">Tipe Transaksi <span class="text-danger">*</span></label>
                                <select class="form-select @error('type') is-invalid @enderror" id="type" name="type" required>
                                    <option value="">Pilih Tipe</option>
                                    <option value="in" {{ old('type') == 'in' ? 'selected' : '' }}>Barang Masuk</option>
                                    <option value="out" {{ old('type') == 'out' ? 'selected' : '' }}>Barang Keluar</option>
                                </select>
                                @error('type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="transaction_date" class="form-label">Tanggal <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('transaction_date') is-invalid @enderror" 
                                       id="transaction_date" name="transaction_date" value="{{ old('transaction_date', date('Y-m-d')) }}" required>
                                @error('transaction_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div id="customer-fields" style="display: none;">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="customer_name" class="form-label">Nama Pelanggan <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('customer_name') is-invalid @enderror" 
                                           id="customer_name" name="customer_name" value="{{ old('customer_name') }}" placeholder="Masukkan nama pelanggan">
                                    @error('customer_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="store_name" class="form-label">Nama Toko <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('store_name') is-invalid @enderror" 
                                           id="store_name" name="store_name" value="{{ old('store_name') }}" placeholder="Masukkan nama toko">
                                    @error('store_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="customer_address" class="form-label">Alamat Pelanggan</label>
                                <textarea class="form-control @error('customer_address') is-invalid @enderror" 
                                          id="customer_address" name="customer_address" rows="2" placeholder="Masukkan alamat pelanggan">{{ old('customer_address') }}</textarea>
                                @error('customer_address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div id="payment-status-field" style="display: none;">
                            <div class="mb-3">
                                <label for="payment_status" class="form-label">Status Pembayaran</label>
                                <select class="form-select @error('payment_status') is-invalid @enderror" id="payment_status" name="payment_status">
                                    <option value="unpaid" {{ old('payment_status') == 'unpaid' ? 'selected' : '' }}>Belum Lunas</option>
                                    <option value="paid" {{ old('payment_status') == 'paid' ? 'selected' : '' }}>Lunas</option>
                                </select>
                                @error('payment_status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="notes" class="form-label">Catatan</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror" 
                                      id="notes" name="notes" rows="2">{{ old('notes') }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="card mt-3">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Item Transaksi</h5>
                        <button type="button" class="btn btn-sm btn-primary" id="addItemBtn">
                            <i class="fas fa-plus"></i> Tambah Item
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered" id="itemsTable">
                                <thead>
                                    <tr>
                                        <th width="30%">Item</th>
                                        <th width="10%">Stok</th>
                                        <th width="10%">Satuan</th>
                                        <th width="12%">Quantity</th>
                                        <th width="15%">Harga</th>
                                        <th id="discountHeader" width="10%" style="display: none;">Diskon</th>
                                        <th id="bonusHeader" width="10%" style="display: none;">Bonus</th>
                                        <th width="8%">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="itemsTableBody">
                                    <tr id="emptyRow">
                                        <td colspan="6" class="text-center text-muted">Belum ada item. Klik "Tambah Item" untuk menambahkan.</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        @error('items')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="sticky-top" style="top: 20px;">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Ringkasan</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-borderless">
                            <tr>
                                <td class="fw-bold">Total Item:</td>
                                <td class="text-end" id="totalItems">0</td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Total Quantity:</td>
                                <td class="text-end" id="totalQuantity">0</td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Subtotal:</td>
                                <td class="text-end" id="subtotalAmount">Rp 0</td>
                            </tr>
                        </table>

                        <div id="discount-bonus-section" style="display: none;">
                            <hr class="my-2">
                            <div class="mb-3">
                                <label for="discount" class="form-label">Diskon (Rp)</label>
                                <input type="text" class="form-control" id="discount_display" value="0">
                                <input type="hidden" id="discount" name="discount" value="0">
                            </div>
                            <div class="mb-3">
                                <label for="bonus" class="form-label">Bonus (Rp)</label>
                                <input type="text" class="form-control" id="bonus_display" value="0">
                                <input type="hidden" id="bonus" name="bonus" value="0">
                            </div>
                        </div>

                        <hr class="my-2">
                        <table class="table table-borderless mb-0">
                            <tr class="border-top">
                                <td class="fw-bold">Total Amount:</td>
                                <td class="text-end">
                                    <h4 class="mb-0 text-primary" id="totalAmount">Rp 0</h4>
                                </td>
                            </tr>
                        </table>

                        <hr>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-save"></i> Simpan Transaksi
                            </button>
                            <a href="{{ route('transactions.index') }}" class="btn btn-secondary">
                                Batal
                            </a>
                        </div>
                    </div>
                </div>

                <div class="card mt-3">
                    <div class="card-header">
                        <h5 class="card-title mb-0"><i class="fas fa-info-circle"></i> Informasi</h5>
                    </div>
                    <div class="card-body">
                        <ul class="small mb-0">
                            <li><strong>Barang Masuk:</strong> Menambah stok</li>
                            <li><strong>Barang Keluar:</strong> Mengurangi stok</li>
                            <li>Minimal 1 item harus ditambahkan</li>
                            <li>Satuan otomatis sesuai pengaturan item</li>
                        </ul>
                    </div>
                </div>
                </div>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
let itemIndex = 0;
const items = @json($items);

// Currency formatter
function formatCurrency(value) {
    return new Intl.NumberFormat('id-ID').format(value);
}

function parseCurrency(value) {
    return parseInt(value.toString().replace(/\./g, '')) || 0;
}

function toggleDiscountColumn(type) {
    if (type === 'out') {
        $('#discountHeader').show();
        $('#bonusHeader').show();
        $('#emptyRow td').attr('colspan', 8);
        
        // Add discount and bonus columns to existing rows if not present
        $('#itemsTableBody tr').each(function() {
            const row = $(this);
            if (!row.hasClass('empty-row') && row.find('.item-discount').length === 0) {
                const index = row.data('index');
                const discountTd = `
                    <td>
                        <input type="text" class="form-control form-control-sm item-discount-display" value="0">
                        <input type="hidden" class="item-discount" name="items[${index}][discount]" value="0">
                    </td>
                    <td>
                        <input type="text" class="form-control form-control-sm item-bonus-display" value="0">
                        <input type="hidden" class="item-bonus" name="items[${index}][bonus]" value="0">
                    </td>
                `;
                row.find('td').eq(4).after(discountTd);
            }
        });
    } else {
        $('#discountHeader').hide();
        $('#bonusHeader').hide();
        $('#emptyRow td').attr('colspan', 6);
        
        // Remove discount and bonus columns from existing rows
        $('#itemsTableBody tr').each(function() {
            const row = $(this);
            if (!row.hasClass('empty-row')) {
                row.find('.item-discount-display, .item-discount, .item-bonus-display, .item-bonus').closest('td').remove();
            }
        });
    }
}

$(document).ready(function() {
    // Form validation before submit
    $('#transactionForm').on('submit', function(e) {
        const type = $('#type').val();
        
        if (type === 'out') {
            const customerName = $('#customer_name').val().trim();
            const storeName = $('#store_name').val().trim();
            
            if (!customerName || !storeName) {
                e.preventDefault();
                Swal.fire({
                    icon: 'warning',
                    title: 'Data Belum Lengkap',
                    text: 'Untuk transaksi Barang Keluar, harap isi Nama Pelanggan dan Nama Toko terlebih dahulu.',
                    confirmButtonText: 'OK'
                });
                return false;
            }
        }
    });

    // Show/hide customer fields and payment status based on transaction type
    $('#type').on('change', function() {
        const type = $(this).val();
        if (type === 'out') {
            $('#customer-fields').show();
            $('#payment-status-field').show();
            $('#discount-bonus-section').show();
        } else {
            $('#customer-fields').hide();
            $('#payment-status-field').hide();
            $('#discount-bonus-section').hide();
            $('#discount').val(0);
            $('#bonus').val(0);
        }
        
        toggleDiscountColumn(type);
        
        // Update all existing item prices when transaction type changes
        $('#itemsTableBody tr').each(function() {
            const row = $(this);
            const itemSelect = row.find('.item-select');
            const itemId = itemSelect.val();
            
            if (itemId) {
                const item = items.find(i => i.id == itemId);
                if (item) {
                    if (type === 'in') {
                        row.find('.item-price').val(item.purchase_price);
                        row.find('.item-price-display').val(formatCurrency(item.purchase_price));
                    } else {
                        row.find('.item-price').val(item.selling_price);
                        row.find('.item-price-display').val(formatCurrency(item.selling_price));
                    }
                    updateSummary();
                }
            }
        });
    });

    // Trigger on page load if old value exists
    if ($('#type').val() === 'out') {
        $('#customer-fields').show();
        $('#payment-status-field').show();
        $('#discount-bonus-section').show();
    }
    toggleDiscountColumn($('#type').val());

    $('#addItemBtn').on('click', function() {
        const transactionType = $('#type').val();
        if (!transactionType) {
            Swal.fire({
                icon: 'warning',
                title: 'Pilih Tipe Transaksi',
                text: 'Silakan pilih Barang Masuk atau Barang Keluar terlebih dahulu sebelum menambah item.',
                confirmButtonText: 'OK'
            });
            return;
        }
        addItemRow();
    });

    $(document).on('click', '.remove-item-btn', function() {
        $(this).closest('tr').remove();
        updateSummary();
        
        if ($('#itemsTableBody tr').length === 0) {
            $('#itemsTableBody').html('<tr><td colspan="6" class="text-center text-muted">Belum ada item. Klik "Tambah Item" untuk menambahkan.</td></tr>');
        }
    });

    $(document).on('change', '.item-select', function() {
        const itemId = $(this).val();
        const row = $(this).closest('tr');
        
        if (itemId) {
            const item = items.find(i => i.id == itemId);
            if (item) {
                // Display stock
                row.find('.item-stock').html(item.stock + ' pcs');
                
                // Set price based on transaction type
                const transactionType = $('#type').val();
                if (transactionType === 'in') {
                    row.find('.item-price').val(item.purchase_price);
                    row.find('.item-price-display').val(formatCurrency(item.purchase_price));
                } else {
                    row.find('.item-price').val(item.selling_price);
                    row.find('.item-price-display').val(formatCurrency(item.selling_price));
                }
                
                // Set unit type based on item setting
                const unitType = item.unit_type || 'pcs';
                row.find('.unit-display').text(unitType.toUpperCase());
                row.find('.unit-type-input').val(unitType);
                
                row.data('item', item);
                row.data('unit-type', unitType);
                
                // Update placeholders for discount and bonus
                const itemName = item ? item.name : '';
                row.find('.item-discount-display').attr('placeholder', `Diskon (${itemName})`);
                row.find('.item-bonus-display').attr('placeholder', `Bonus (${itemName})`);
            }
        } else {
            row.find('.item-stock').text('-');
            row.find('.item-price').val(0);
            row.find('.unit-display').text('-');
            row.data('item', null);
        }
        updateSummary();
    });

    $(document).on('input', '.item-quantity, .item-price, .item-discount', function() {
        updateSummary();
    });
});

function addItemRow() {
    if ($('#itemsTableBody tr td').attr('colspan')) {
        $('#itemsTableBody').empty();
    }

    const transactionType = $('#type').val();
    const hasDiscount = transactionType === 'out';

    const row = `
        <tr data-index="${itemIndex}">
            <td>
                <select class="form-select form-select-sm item-select" name="items[${itemIndex}][item_id]" required>
                    <option value="">Pilih Item</option>
                    ${items.map(item => {
                        const unitType = item.unit_type || 'pcs';
                        return `<option value="${item.id}">${item.name} (${item.code}) [${unitType.toUpperCase()}]</option>`;
                    }).join('')}
                </select>
            </td>
            <td class="item-stock text-center">-</td>
            <td class="text-center">
                <span class="unit-display badge bg-secondary">-</span>
                <input type="hidden" class="unit-type-input" name="items[${itemIndex}][unit_type]" value="pcs">
            </td>
            <td>
                <input type="number" class="form-control form-control-sm item-quantity" 
                       name="items[${itemIndex}][quantity]" min="1" value="1" required>
            </td>
            <td>
                <input type="text" class="form-control form-control-sm item-price-display" value="0" readonly>
                <input type="hidden" class="item-price" name="items[${itemIndex}][price]" value="0">
            </td>
            ${hasDiscount ? `
            <td>
                <input type="text" class="form-control form-control-sm item-discount-display" value="0" placeholder="Diskon">
                <input type="hidden" class="item-discount" name="items[${itemIndex}][discount]" value="0">
            </td>
            <td>
                <input type="text" class="form-control form-control-sm item-bonus-display" value="0" placeholder="Bonus">
                <input type="hidden" class="item-bonus" name="items[${itemIndex}][bonus]" value="0">
            </td>
            ` : ''}
            <td class="text-center">
                <button type="button" class="btn btn-sm btn-danger remove-item-btn">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        </tr>
    `;

    $('#itemsTableBody').append(row);
    itemIndex++;
    updateSummary();
}

function updateSummary() {
    let totalItems = 0;
    let totalQuantity = 0;
    let totalAmount = 0;

    $('#itemsTableBody tr').each(function() {
        const row = $(this);
        const item = row.data('item');
        
        if (!item) return;
        
        const qty = parseInt(row.find('.item-quantity').val()) || 0;
        const price = parseFloat(row.find('.item-price').val()) || 0;
        const discount = parseFloat(row.find('.item-discount').val()) || 0;
        const bonus = parseFloat(row.find('.item-bonus').val()) || 0;
        
        if (qty > 0 && price > 0) {
            totalItems++;
            totalQuantity += qty;
            totalAmount += (qty * price) - discount - bonus;
        }
    });

    $('#totalItems').text(totalItems);
    $('#totalQuantity').text(totalQuantity);
    $('#subtotalAmount').text('Rp ' + formatCurrency(totalAmount));
    
    // Calculate final total with discount and bonus
    let discount = parseFloat($('#discount').val()) || 0;
    let bonus = parseFloat($('#bonus').val()) || 0;
    
    const finalTotal = totalAmount - discount - bonus;
    
    $('#totalAmount').text('Rp ' + formatCurrency(Math.max(0, finalTotal)));
}

// Format discount input
$('#discount_display').on('input', function() {
    let value = parseCurrency($(this).val());
    $(this).val(formatCurrency(value));
    $('#discount').val(value);
    updateSummary();
});

// Format bonus input
$('#bonus_display').on('input', function() {
    let value = parseCurrency($(this).val());
    $(this).val(formatCurrency(value));
    $('#bonus').val(value);
    updateSummary();
});

// Format item discount inputs
$(document).on('input', '.item-discount-display', function() {
    let value = parseCurrency($(this).val());
    $(this).val(formatCurrency(value));
    $(this).siblings('.item-discount').val(value);
    updateSummary();
});

// Format item bonus inputs
$(document).on('input', '.item-bonus-display', function() {
    let value = parseCurrency($(this).val());
    $(this).val(formatCurrency(value));
    $(this).siblings('.item-bonus').val(value);
    updateSummary();
});
</script>
@endpush
@endsection
