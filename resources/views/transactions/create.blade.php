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

                        <div class="mb-3">
                            <label for="vehicle_id" class="form-label">Kendaraan</label>
                            <select class="form-select select2 @error('vehicle_id') is-invalid @enderror" id="vehicle_id" name="vehicle_id">
                                <option value="">Pilih Kendaraan (Opsional)</option>
                                @foreach($vehicles as $vehicle)
                                    <option value="{{ $vehicle->id }}" {{ old('vehicle_id') == $vehicle->id ? 'selected' : '' }}>
                                        {{ $vehicle->name }} ({{ $vehicle->plate_number }})
                                    </option>
                                @endforeach
                            </select>
                            @error('vehicle_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div id="customer-fields" style="display: none;">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="customer_name" class="form-label">Nama Pelanggan</label>
                                    <input type="text" class="form-control @error('customer_name') is-invalid @enderror" 
                                           id="customer_name" name="customer_name" value="{{ old('customer_name') }}" placeholder="Masukkan nama pelanggan">
                                    @error('customer_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="store_name" class="form-label">Nama Toko</label>
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
                                        <th width="12%">Stok</th>
                                        <th width="12%">Satuan</th>
                                        <th width="20%">Quantity</th>
                                        <th width="15%">Harga</th>
                                        <th width="8%">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="itemsTableBody">
                                    <tr>
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
                                <input type="number" class="form-control" id="discount" name="discount" 
                                       value="0" min="0" step="1000">
                            </div>
                            <div class="mb-3">
                                <label for="bonus" class="form-label">Bonus (Rp)</label>
                                <input type="number" class="form-control" id="bonus" name="bonus" 
                                       value="0" min="0" step="1000">
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
                            <li>Stok akan otomatis terupdate</li>
                        </ul>
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
const transactionType = $('#type');

$(document).ready(function() {
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
                    } else {
                        row.find('.item-price').val(item.selling_price);
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

    $('#addItemBtn').on('click', function() {
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
                row.find('.item-stock').html(item.stock + ' ' + item.unit);
                
                // Set price based on transaction type
                const transactionType = $('#type').val();
                if (transactionType === 'in') {
                    row.find('.item-price').val(item.purchase_price);
                } else {
                    row.find('.item-price').val(item.selling_price);
                }
                
                row.data('item', item);
                
                // Reset unit selection
                const unitSelect = row.find('.unit-type-select');
                unitSelect.val('pcs').trigger('change');
                
                // If item has box settings, set them automatically
                if (item.box_type && item.box_quantity) {
                    row.data('has-box-settings', true);
                    row.data('box-type', item.box_type);
                    row.data('box-quantity', item.box_quantity);
                } else {
                    row.data('has-box-settings', false);
                }
            }
        } else {
            row.find('.item-stock').text('-');
            row.find('.item-price').val(0);
            row.data('item', null);
            row.data('has-box-settings', false);
        }
        updateSummary();
    });

    $(document).on('change', '.unit-type-select', function() {
        const unitType = $(this).val();
        const row = $(this).closest('tr');
        
        // Show/hide box details based on unit type
        if (unitType === 'box') {
            const hasBoxSettings = row.data('has-box-settings');
            const boxType = row.data('box-type');
            const boxQuantity = row.data('box-quantity');
            
            row.find('.box-details').show();
            row.find('.simple-qty').hide();
            
            if (hasBoxSettings) {
                // Use box settings from item - show badge
                row.find('.box-settings-badge').show();
                row.find('.sub-unit-select').val(boxType).prop('disabled', true);
                
                if (boxType === 'dozen') {
                    row.find('.dozen-per-box').val(boxQuantity).prop('readonly', true);
                    row.find('.pcs-per-dozen').val(12).prop('readonly', true);
                } else if (boxType === 'pcs') {
                    row.find('.pcs-per-box').val(boxQuantity).prop('readonly', true);
                }
            } else {
                // Manual input for box settings - hide badge
                row.find('.box-settings-badge').hide();
                row.find('.sub-unit-select').prop('disabled', false);
                row.find('.dozen-per-box, .pcs-per-dozen, .pcs-per-box').prop('readonly', false);
            }
            
            // Trigger sub-unit change to show proper conversion fields
            row.find('.sub-unit-select').trigger('change');
        } else if (unitType === 'dozen') {
            row.find('.box-details').hide();
            row.find('.simple-qty').show();
        } else {
            row.find('.box-details').hide();
            row.find('.simple-qty').show();
        }
        
        updateRowQuantity(row);
    });

    $(document).on('input', '.item-quantity, .box-qty, .sub-unit-qty, .sub-unit-qty-pcs, .item-price, .dozen-per-box, .pcs-per-dozen, .pcs-per-box', function() {
        const row = $(this).closest('tr');
        updateConversionInfo(row);
        updateRowQuantity(row);
    });

    $(document).on('change', '.sub-unit-select', function() {
        const row = $(this).closest('tr');
        const subUnit = $(this).val();
        
        if (subUnit === 'dozen') {
            row.find('.lusin-conversion').show();
            row.find('.pcs-conversion').hide();
        } else {
            row.find('.lusin-conversion').hide();
            row.find('.pcs-conversion').show();
        }
        
        updateConversionInfo(row);
        updateRowQuantity(row);
    });
});

function addItemRow() {
    if ($('#itemsTableBody tr td').attr('colspan')) {
        $('#itemsTableBody').empty();
    }

    const row = `
        <tr data-index="${itemIndex}">
            <td>
                <select class="form-select form-select-sm item-select" name="items[${itemIndex}][item_id]" required>
                    <option value="">Pilih Item</option>
                    ${items.map(item => {
                        let boxInfo = '';
                        if (item.box_type && item.box_quantity) {
                            boxInfo = ` [📦 ${item.box_quantity} ${item.box_type}]`;
                        }
                        return `<option value="${item.id}">${item.name} (${item.code})</option>`;
                    }).join('')}
                </select>
            </td>
            <td class="item-stock text-center">-</td>
            <td>
                <select class="form-select form-select-sm unit-type-select" name="items[${itemIndex}][unit_type]" required>
                    <option value="pcs">Pcs</option>
                    <option value="dozen">Lusin</option>
                    <option value="box">Box</option>
                </select>
            </td>
            <td>
                <!-- Simple Quantity (for pcs and dozen) -->
                <div class="simple-qty">
                    <input type="number" class="form-control form-control-sm item-quantity" 
                           name="items[${itemIndex}][quantity]" min="1" value="1" required>
                </div>
                
                <!-- Box Details (only for box) -->
                <div class="box-details" style="display: none;">
                    <div class="alert alert-success py-1 px-2 mb-2 box-settings-badge" style="display:none;">
                        <small><i class="fas fa-check-circle"></i> Menggunakan box settings dari item</small>
                    </div>
                    <div class="mb-2">
                        <label class="form-label form-label-sm mb-1">Qty Box</label>
                        <input type="number" class="form-control form-control-sm box-qty" 
                               name="items[${itemIndex}][box_quantity]" min="1" value="1">
                    </div>
                    <div class="mb-2">
                        <label class="form-label form-label-sm mb-1">Sub Unit</label>
                        <select class="form-select form-select-sm sub-unit-select" 
                                name="items[${itemIndex}][sub_unit_type]">
                            <option value="dozen">Lusin</option>
                            <option value="pcs">Pcs</option>
                        </select>
                    </div>
                    
                    <!-- If sub unit is Lusin -->
                    <div class="lusin-conversion" style="display: none;">
                        <div class="row mb-2">
                            <div class="col-6">
                                <label class="form-label form-label-sm mb-1">Lusin per Box</label>
                                <input type="number" class="form-control form-control-sm dozen-per-box" 
                                       name="items[${itemIndex}][dozen_per_box]" min="1" value="1">
                            </div>
                            <div class="col-6">
                                <label class="form-label form-label-sm mb-1">Pcs per Lusin</label>
                                <input type="number" class="form-control form-control-sm pcs-per-dozen" 
                                       name="items[${itemIndex}][pcs_per_dozen]" min="1" value="12">
                            </div>
                        </div>
                        <div class="mb-2">
                            <label class="form-label form-label-sm mb-1">Qty Lusin (tambahan)</label>
                            <input type="number" class="form-control form-control-sm sub-unit-qty" 
                                   name="items[${itemIndex}][sub_unit_quantity]" min="0" value="0">
                        </div>
                    </div>
                    
                    <!-- If sub unit is Pcs -->
                    <div class="pcs-conversion" style="display: none;">
                        <div class="row mb-2">
                            <div class="col-12">
                                <label class="form-label form-label-sm mb-1">Pcs per Box</label>
                                <input type="number" class="form-control form-control-sm pcs-per-box" 
                                       name="items[${itemIndex}][pcs_per_box]" min="1" value="1">
                            </div>
                        </div>
                        <div class="mb-2">
                            <label class="form-label form-label-sm mb-1">Qty Pcs (tambahan)</label>
                            <input type="number" class="form-control form-control-sm sub-unit-qty-pcs" 
                                   name="items[${itemIndex}][sub_unit_quantity]" min="0" value="0">
                        </div>
                    </div>
                    
                    <div class="conversion-info mb-2 alert alert-info py-1 px-2" style="display:none;"></div>
                    <input type="hidden" class="calculated-qty" name="items[${itemIndex}][calculated_quantity]" value="0">
                </div>
            </td>
            <td>
                <input type="number" class="form-control form-control-sm item-price" 
                       name="items[${itemIndex}][price]" min="0" value="0" required readonly>
            </td>
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

function updateConversionInfo(row) {
    const unitType = row.find('.unit-type-select').val();
    
    if (unitType === 'box') {
        const boxQty = parseInt(row.find('.box-qty').val()) || 0;
        const subUnitType = row.find('.sub-unit-select').val();
        
        let totalPcsInBoxes = 0;
        let subUnitInPcs = 0;
        let infoText = `<strong>Konversi:</strong><br>`;
        
        if (subUnitType === 'dozen') {
            const dozenPerBox = parseInt(row.find('.dozen-per-box').val()) || 1;
            const pcsPerDozen = parseInt(row.find('.pcs-per-dozen').val()) || 12;
            const subUnitQty = parseInt(row.find('.sub-unit-qty').val()) || 0;
            
            const totalDozenInBoxes = boxQty * dozenPerBox;
            totalPcsInBoxes = totalDozenInBoxes * pcsPerDozen;
            subUnitInPcs = subUnitQty * pcsPerDozen;
            
            infoText += `${boxQty} box × ${dozenPerBox} lusin × ${pcsPerDozen} pcs = ${totalPcsInBoxes} pcs`;
            if (subUnitQty > 0) {
                infoText += `<br>+ ${subUnitQty} lusin × ${pcsPerDozen} pcs = ${subUnitInPcs} pcs`;
            }
        } else {
            const pcsPerBox = parseInt(row.find('.pcs-per-box').val()) || 1;
            const subUnitQtyPcs = parseInt(row.find('.sub-unit-qty-pcs').val()) || 0;
            
            totalPcsInBoxes = boxQty * pcsPerBox;
            subUnitInPcs = subUnitQtyPcs;
            
            infoText += `${boxQty} box × ${pcsPerBox} pcs = ${totalPcsInBoxes} pcs`;
            if (subUnitQtyPcs > 0) {
                infoText += `<br>+ ${subUnitQtyPcs} pcs`;
            }
        }
        
        const totalPcs = totalPcsInBoxes + subUnitInPcs;
        infoText += `<br><strong>Total: ${totalPcs} pcs</strong>`;
        
        row.find('.conversion-info').html(infoText).show();
    }
}

function updateRowQuantity(row) {
    const unitType = row.find('.unit-type-select').val();
    let totalPcs = 0;
    
    if (unitType === 'box') {
        const boxQty = parseInt(row.find('.box-qty').val()) || 0;
        const subUnitType = row.find('.sub-unit-select').val();
        
        let pcsFromBoxes = 0;
        let pcsFromSubUnit = 0;
        
        if (subUnitType === 'dozen') {
            const dozenPerBox = parseInt(row.find('.dozen-per-box').val()) || 1;
            const pcsPerDozen = parseInt(row.find('.pcs-per-dozen').val()) || 12;
            const subUnitQty = parseInt(row.find('.sub-unit-qty').val()) || 0;
            
            pcsFromBoxes = boxQty * dozenPerBox * pcsPerDozen;
            pcsFromSubUnit = subUnitQty * pcsPerDozen;
        } else {
            const pcsPerBox = parseInt(row.find('.pcs-per-box').val()) || 1;
            const subUnitQtyPcs = parseInt(row.find('.sub-unit-qty-pcs').val()) || 0;
            
            pcsFromBoxes = boxQty * pcsPerBox;
            pcsFromSubUnit = subUnitQtyPcs;
        }
        
        totalPcs = pcsFromBoxes + pcsFromSubUnit;
        row.find('.calculated-qty').val(totalPcs);
        row.find('.item-quantity').val(totalPcs);
        
    } else if (unitType === 'dozen') {
        const dozenQty = parseInt(row.find('.item-quantity').val()) || 0;
        const pcsPerDozen = 12; // default for dozen without box
        totalPcs = dozenQty * pcsPerDozen;
        
    } else { // pcs
        totalPcs = parseInt(row.find('.item-quantity').val()) || 0;
    }
    
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
        
        const unitType = row.find('.unit-type-select').val();
        let qty = 0;
        
        if (unitType === 'box') {
            qty = parseInt(row.find('.calculated-qty').val()) || 0;
        } else if (unitType === 'dozen') {
            const dozenQty = parseInt(row.find('.item-quantity').val()) || 0;
            qty = dozenQty * 12; // default 12 pcs per dozen
        } else {
            qty = parseInt(row.find('.item-quantity').val()) || 0;
        }
        
        const price = parseFloat(row.find('.item-price').val()) || 0;
        
        if (qty > 0 && price > 0) {
            totalItems++;
            totalQuantity += qty;
            totalAmount += (qty * price);
        }
    });

    $('#totalItems').text(totalItems);
    $('#totalQuantity').text(totalQuantity + ' pcs');
    $('#subtotalAmount').text('Rp ' + new Intl.NumberFormat('id-ID').format(totalAmount));
    
    // Calculate final total with discount and bonus
    const discount = parseFloat($('#discount').val()) || 0;
    const bonus = parseFloat($('#bonus').val()) || 0;
    const finalTotal = totalAmount - discount - bonus;
    
    $('#totalAmount').text('Rp ' + new Intl.NumberFormat('id-ID').format(Math.max(0, finalTotal)));
}

// Update summary when discount or bonus changes
$(document).on('input', '#discount, #bonus', function() {
    updateSummary();
});
</script>
@endpush
@endsection
