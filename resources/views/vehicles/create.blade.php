@extends('layouts.app')

@section('title', 'Tambah Kendaraan')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Tambah Kendaraan</h1>
        <a href="{{ route('vehicles.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('vehicles.store') }}" method="POST">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label">Nama Kendaraan <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                       id="name" name="name" value="{{ old('name') }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="plate_number" class="form-label">Plat Nomor <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('plate_number') is-invalid @enderror" 
                                       id="plate_number" name="plate_number" value="{{ old('plate_number') }}" required>
                                @error('plate_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Contoh: B 1234 XYZ</small>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="type" class="form-label">Tipe <span class="text-danger">*</span></label>
                                <select class="form-select @error('type') is-invalid @enderror" id="type" name="type" required>
                                    <option value="">Pilih Tipe</option>
                                    <option value="Mobil" {{ old('type') == 'Mobil' ? 'selected' : '' }}>Mobil</option>
                                    <option value="Truk" {{ old('type') == 'Truk' ? 'selected' : '' }}>Truk</option>
                                    <option value="Motor" {{ old('type') == 'Motor' ? 'selected' : '' }}>Motor</option>
                                    <option value="Pick Up" {{ old('type') == 'Pick Up' ? 'selected' : '' }}>Pick Up</option>
                                </select>
                                @error('type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="brand" class="form-label">Merk</label>
                                <input type="text" class="form-control @error('brand') is-invalid @enderror" 
                                       id="brand" name="brand" value="{{ old('brand') }}">
                                @error('brand')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="year" class="form-label">Tahun</label>
                                <input type="number" class="form-control @error('year') is-invalid @enderror" 
                                       id="year" name="year" value="{{ old('year') }}" min="1900" max="{{ date('Y') + 1 }}">
                                @error('year')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Deskripsi</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="3">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="is_active" 
                                       name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">
                                    Status Aktif
                                </label>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('vehicles.index') }}" class="btn btn-secondary">Batal</a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Simpan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0"><i class="fas fa-info-circle"></i> Informasi</h5>
                </div>
                <div class="card-body">
                    <p class="mb-2"><strong>Tips:</strong></p>
                    <ul class="small">
                        <li>Plat nomor harus unik</li>
                        <li>Pastikan format plat nomor sesuai (contoh: B 1234 XYZ)</li>
                        <li>Tahun harus dalam rentang {{ 1900 }} - {{ date('Y') + 1 }}</li>
                        <li>Kendaraan akan digunakan untuk tracking pengiriman</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
