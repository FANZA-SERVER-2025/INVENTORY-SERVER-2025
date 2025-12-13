@extends('layouts.app')

@section('title', 'Data Transaksi')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Data Transaksi</h1>
        <div>
            @can('export-transactions')
            <button id="exportBtn" class="btn btn-success">
                <i class="fas fa-file-excel"></i> Export Excel
            </button>
            @endcan
            @can('create-transactions')
            <a href="{{ route('transactions.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Buat Transaksi
            </a>
            @endcan
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <div class="row align-items-center">
                <div class="col-md-3">
                    <label for="filterType" class="form-label mb-0">Filter Tipe Transaksi:</label>
                    <select id="filterType" class="form-select form-select-sm">
                        <option value="">Semua Transaksi</option>
                        <option value="in">Barang Masuk</option>
                        <option value="out">Barang Keluar</option>
                    </select>
                </div>
                <div class="col-md-3" id="paymentStatusFilter" style="display: none;">
                    <label for="filterPaymentStatus" class="form-label mb-0">Status Pembayaran:</label>
                    <select id="filterPaymentStatus" class="form-select form-select-sm">
                        <option value="">Semua Status</option>
                        <option value="paid">Lunas</option>
                        <option value="unpaid">Belum Lunas</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="transactionsTable" class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>No. Transaksi</th>
                            <th>Tipe</th>
                            <th>Tanggal</th>
                            <th>User</th>
                            <th>Total</th>
                            <th>Status Pembayaran</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    const canViewTransactions = {{ auth()->user()->can('view-transactions') ? 'true' : 'false' }};
    const canEditTransactions = {{ auth()->user()->can('edit-transactions') ? 'true' : 'false' }};
    const canDeleteTransactions = {{ auth()->user()->can('delete-transactions') ? 'true' : 'false' }};
</script>
<script src="{{ asset('js/transactions.js') }}"></script>
@endpush
@endsection
