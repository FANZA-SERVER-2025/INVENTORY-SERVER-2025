@extends('layouts.app')

@section('title', 'Edit Item')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Edit Item</h1>
        <a href="{{ route('items.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('items.update', $item) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="category_id" class="form-label">Kategori <span class="text-danger">*</span></label>
                                <select class="form-select select2 @error('category_id') is-invalid @enderror" 
                                        id="category_id" name="category_id" required>
                                    <option value="">Pilih Kategori</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ old('category_id', $item->category_id) == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('category_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="supplier_id" class="form-label">Supplier <span class="text-danger">*</span></label>
                                <select class="form-select select2 @error('supplier_id') is-invalid @enderror" 
                                        id="supplier_id" name="supplier_id" required>
                                    <option value="">Pilih Supplier</option>
                                    @foreach($suppliers as $supplier)
                                        <option value="{{ $supplier->id }}" {{ old('supplier_id', $item->supplier_id) == $supplier->id ? 'selected' : '' }}>
                                            {{ $supplier->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('supplier_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label">Nama Item <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                       id="name" name="name" value="{{ old('name', $item->name) }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="code" class="form-label">Kode Item <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('code') is-invalid @enderror" 
                                       id="code" name="code" value="{{ old('code', $item->code) }}" required>
                                @error('code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label for="stock" class="form-label">Stok Saat Ini</label>
                                <input type="text" class="form-control" value="{{ $item->stock }} pcs" readonly disabled>
                                <small class="text-muted">Stok tidak bisa diubah manual. Gunakan transaksi untuk mengubah stok.</small>
                            </div>
                        </div>

                        <input type="hidden" name="stock" value="{{ $item->stock }}">
                        <input type="hidden" name="unit" value="pcs">

                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Satuan Item <span class="text-danger">*</span></label>
                                <div class="card border">
                                    <div class="card-body">
                                        <p class="small text-info mb-3">
                                            <i class="fas fa-info-circle"></i> <strong>Pilih satuan untuk transaksi barang masuk dan keluar.</strong>
                                        </p>
                                        <div class="row">
                                            <div class="col-md-12 mb-3">
                                                <label for="unit_type" class="form-label">Pilih Satuan <span class="text-danger">*</span></label>
                                                <select class="form-select @error('unit_type') is-invalid @enderror" 
                                                        id="unit_type" name="unit_type" required>
                                                    <option value="pcs" {{ old('unit_type', $item->unit_type ?? 'pcs') == 'pcs' ? 'selected' : '' }}>PCS (Pcs)</option>
                                                    <option value="lusin" {{ old('unit_type', $item->unit_type) == 'lusin' ? 'selected' : '' }}>LUSIN (Lusin)</option>
                                                    <option value="dus" {{ old('unit_type', $item->unit_type) == 'dus' ? 'selected' : '' }}>DUS (Dus/Box)</option>
                                                </select>
                                                @error('unit_type')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                                <small class="text-muted">Satuan ini akan digunakan di barang masuk dan barang keluar</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label for="minimum_stock" class="form-label">Stok Minimum <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="number" class="form-control @error('minimum_stock') is-invalid @enderror" 
                                           id="minimum_stock_display" value="{{ old('minimum_stock', $item->minimum_stock) }}" min="0" required>
                                    <span class="input-group-text" id="stock_unit_label">pcs</span>
                                </div>
                                <input type="hidden" id="minimum_stock" name="minimum_stock" value="{{ old('minimum_stock', $item->minimum_stock) }}">
                                @error('minimum_stock')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted" id="minimum_stock_help">Stok untuk alert low stock</small>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="purchase_price" class="form-label">Harga Beli <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="text" class="form-control currency-input @error('purchase_price') is-invalid @enderror" 
                                           id="purchase_price_display" value="{{ old('purchase_price', $item->purchase_price) }}" required>
                                    <input type="hidden" id="purchase_price" name="purchase_price" value="{{ old('purchase_price', $item->purchase_price) }}">
                                </div>
                                @error('purchase_price')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="selling_price" class="form-label">Harga Jual <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="text" class="form-control currency-input @error('selling_price') is-invalid @enderror" 
                                           id="selling_price_display" value="{{ old('selling_price', $item->selling_price) }}" required>
                                    <input type="hidden" id="selling_price" name="selling_price" value="{{ old('selling_price', $item->selling_price) }}">
                                </div>
                                @error('selling_price')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="image" class="form-label">Gambar Item</label>
                            @if($item->image)
                                <div class="mb-2">
                                    <img src="{{ asset('storage/' . $item->image) }}" class="img-thumbnail" style="max-width: 150px;">
                                    <p class="small text-muted">Gambar saat ini</p>
                                </div>
                            @endif
                            <input type="file" class="form-control @error('image') is-invalid @enderror" 
                                   id="image" name="image" accept="image/jpeg,image/png,image/jpg">
                            @error('image')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Format: JPG, PNG (Max: 2MB). Kosongkan jika tidak ingin mengubah gambar.</small>
                            <div id="imagePreview" class="mt-2"></div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Deskripsi</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="3">{{ old('description', $item->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="is_active" 
                                       name="is_active" value="1" {{ old('is_active', $item->is_active) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">
                                    Status Aktif
                                </label>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('items.index') }}" class="btn btn-secondary">Batal</a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Update
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0"><i class="fas fa-info-circle"></i> Informasi Item</h5>
                </div>
                <div class="card-body">
                    <table class="table table-sm">
                        <tr>
                            <td class="fw-bold">ID:</td>
                            <td>{{ $item->id }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Status Stok:</td>
                            <td>
                                @if($item->isLowStock())
                                    <span class="badge bg-warning">Stok Rendah</span>
                                @else
                                    <span class="badge bg-success">Stok Normal</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Dibuat:</td>
                            <td>{{ $item->created_at->format('d M Y H:i') }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Diperbarui:</td>
                            <td>{{ $item->updated_at->format('d M Y H:i') }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header">
                    <h5 class="card-title mb-0"><i class="fas fa-lightbulb"></i> Tips</h5>
                </div>
                <div class="card-body">
                    <ul class="small mb-0">
                        <li>Stok tidak bisa diubah manual</li>
                        <li>Gunakan <strong>Transaksi Barang Masuk</strong> untuk menambah stok</li>
                        <li>Gunakan <strong>Transaksi Barang Keluar</strong> untuk mengurangi stok</li>
                        <li>Stok minimum untuk alert low stock</li>
                        <li>Satuan tetap: Pcs</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    // Currency formatter
    function formatCurrency(value) {
        return new Intl.NumberFormat('id-ID').format(value);
    }
    
    function parseCurrency(value) {
        return parseInt(value.toString().replace(/\./g, '')) || 0;
    }
    
    // Format purchase price
    $('#purchase_price_display').on('input', function() {
        let value = parseCurrency($(this).val());
        $(this).val(formatCurrency(value));
        $('#purchase_price').val(value);
    }).val(formatCurrency($('#purchase_price').val()));
    
    // Format selling price
    $('#selling_price_display').on('input', function() {
        let value = parseCurrency($(this).val());
        $(this).val(formatCurrency(value));
        $('#selling_price').val(value);
    }).val(formatCurrency($('#selling_price').val()));

    // Handle box type changes for minimum stock
    function updateMinimumStockField() {
        const boxType = $('#box_type').val();
        const boxQuantity = parseInt($('#box_quantity').val()) || 0;
        const currentMinStock = parseInt($('#minimum_stock').val()) || 0;
        
        if (boxType && boxQuantity > 0) {
            // Show box as unit
            $('#stock_unit_label').text('box');
            const pcsPerBox = boxType === 'dozen' ? boxQuantity * 12 : boxQuantity;
            const boxTypeText = boxType === 'dozen' ? boxQuantity + ' lusin' : boxQuantity + ' pcs';
            
            // Convert current minimum stock from pcs to box for display
            const minStockInBox = Math.ceil(currentMinStock / pcsPerBox);
            $('#minimum_stock_display').val(minStockInBox);
            
            $('#minimum_stock_help').html(
                '<strong class="text-info">📦 1 box = ' + boxTypeText + ' (' + pcsPerBox + ' pcs)</strong><br>' +
                'Masukkan jumlah box untuk stok minimum. <strong>Contoh:</strong> jika isi 1 box, saat box tersisa <strong>1 box</strong>, sistem akan memberikan alert low stock.'
            );
        } else {
            // Show pcs as unit
            $('#stock_unit_label').text('pcs');
            $('#minimum_stock_display').val(currentMinStock);
            $('#minimum_stock_help').text('Stok untuk alert low stock');
        }
    }
    
    $('#box_type, #box_quantity').on('change input', function() {
        updateMinimumStockField();
    });
    
    // Convert minimum stock from box to pcs when form is submitted
    $('form').on('submit', function(e) {
        const boxType = $('#box_type').val();
        const boxQuantity = parseInt($('#box_quantity').val()) || 0;
        const minStockDisplay = parseInt($('#minimum_stock_display').val()) || 0;
        
        if (boxType && boxQuantity > 0) {
            const pcsPerBox = boxType === 'dozen' ? boxQuantity * 12 : boxQuantity;
            const minStockInPcs = minStockDisplay * pcsPerBox;
            $('#minimum_stock').val(minStockInPcs);
        } else {
            $('#minimum_stock').val(minStockDisplay);
        }
    });
    
    updateMinimumStockField();

    // Image preview
    $('#image').on('change', function() {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                $('#imagePreview').html(`
                    <img src="${e.target.result}" class="img-thumbnail" style="max-width: 200px;">
                    <p class="small text-muted">Preview gambar baru</p>
                `);
            }
            reader.readAsDataURL(file);
        }
    });
});
</script>
@endpush
@endsection
