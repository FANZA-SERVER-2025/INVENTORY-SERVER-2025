@extends('layouts.app')

@section('title', 'Detail Kendaraan')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Detail Kendaraan</h1>
        <div>
            <a href="{{ route('vehicles.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
            @can('edit-vehicles')
            <a href="{{ route('vehicles.edit', $vehicle) }}" class="btn btn-warning">
                <i class="fas fa-edit"></i> Edit
            </a>
            @endcan
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Informasi Kendaraan</h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <td class="fw-bold">Nama:</td>
                            <td>{{ $vehicle->name }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Plat Nomor:</td>
                            <td><span class="badge bg-primary">{{ $vehicle->plate_number }}</span></td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Tipe:</td>
                            <td>{{ $vehicle->type }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Merk:</td>
                            <td>{{ $vehicle->brand ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Tahun:</td>
                            <td>{{ $vehicle->year ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Status:</td>
                            <td>
                                @if($vehicle->is_active)
                                    <span class="badge bg-success">Aktif</span>
                                @else
                                    <span class="badge bg-secondary">Tidak Aktif</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Dibuat:</td>
                            <td>{{ $vehicle->created_at->format('d M Y H:i') }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Diperbarui:</td>
                            <td>{{ $vehicle->updated_at->format('d M Y H:i') }}</td>
                        </tr>
                    </table>

                    @if($vehicle->description)
                    <hr>
                    <p class="mb-0"><strong>Deskripsi:</strong></p>
                    <p class="text-muted">{{ $vehicle->description }}</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Riwayat Transaksi</h5>
                </div>
                <div class="card-body">
                    @if($vehicle->transactions->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>No. Transaksi</th>
                                    <th>Tanggal</th>
                                    <th>Tipe</th>
                                    <th>User</th>
                                    <th>Total</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($vehicle->transactions as $transaction)
                                <tr>
                                    <td>{{ $transaction->transaction_number }}</td>
                                    <td>{{ $transaction->transaction_date->format('d M Y') }}</td>
                                    <td>
                                        @if($transaction->type == 'in')
                                            <span class="badge bg-success">Masuk</span>
                                        @else
                                            <span class="badge bg-danger">Keluar</span>
                                        @endif
                                    </td>
                                    <td>{{ $transaction->user->name }}</td>
                                    <td>Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}</td>
                                    <td>
                                        @can('view-transactions')
                                        <a href="{{ route('transactions.show', $transaction) }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @endcan
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="text-center py-4">
                        <i class="fas fa-clipboard-list fa-3x text-muted mb-3"></i>
                        <p class="text-muted">Belum ada transaksi menggunakan kendaraan ini</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
