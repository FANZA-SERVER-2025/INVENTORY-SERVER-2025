@extends('layouts.app')

@section('title', 'Detail User')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Detail User</h1>
        <div>
            <a href="{{ route('users.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
            @can('edit-users')
            <a href="{{ route('users.edit', $user) }}" class="btn btn-warning">
                <i class="fas fa-edit"></i> Edit
            </a>
            @endcan
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Informasi User</h5>
                </div>
                <div class="card-body text-center">
                    @if($user->avatar)
                        <img src="{{ asset('storage/' . $user->avatar) }}" class="img-fluid rounded-circle mb-3" 
                             style="width: 150px; height: 150px; object-fit: cover;">
                    @else
                        <div class="bg-light rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center" 
                             style="width: 150px; height: 150px;">
                            <i class="fas fa-user fa-4x text-muted"></i>
                        </div>
                    @endif

                    <h4>{{ $user->name }}</h4>
                    <p class="text-muted">{{ $user->email }}</p>

                    @foreach($user->roles as $role)
                        <span class="badge bg-{{ $role->name === 'admin' ? 'danger' : ($role->name === 'operator' ? 'warning' : 'info') }} mb-2">
                            {{ ucfirst($role->name) }}
                        </span>
                    @endforeach

                    <hr>

                    <table class="table table-borderless text-start">
                        <tr>
                            <td class="fw-bold">Telepon:</td>
                            <td>{{ $user->phone ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Alamat:</td>
                            <td>{{ $user->address ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Status:</td>
                            <td>
                                @if($user->is_active)
                                    <span class="badge bg-success">Aktif</span>
                                @else
                                    <span class="badge bg-secondary">Tidak Aktif</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Terdaftar:</td>
                            <td>{{ $user->created_at->format('d M Y') }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Riwayat Transaksi Terbaru</h5>
                </div>
                <div class="card-body">
                    @if($user->transactions->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>No. Transaksi</th>
                                    <th>Tanggal</th>
                                    <th>Tipe</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($user->transactions->take(5) as $transaction)
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
                                    <td>Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="text-center py-4">
                        <i class="fas fa-clipboard-list fa-3x text-muted mb-3"></i>
                        <p class="text-muted">Belum ada riwayat transaksi</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
