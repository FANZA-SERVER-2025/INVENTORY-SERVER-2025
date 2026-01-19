@extends('layouts.app')

@section('title', 'Detail Transaksi')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Detail Transaksi #{{ $transaction->transaction_number }}</h1>
        <div>
            <a href="{{ route('transactions.invoice', $transaction) }}" class="btn btn-secondary" target="_blank">
                <i class="fas fa-file-pdf"></i> Cetak Invoice
            </a>
            <a href="{{ route('transactions.index') }}" class="btn btn-primary">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Informasi Transaksi</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <td class="fw-bold" width="150">No. Transaksi:</td>
                                    <td>{{ $transaction->transaction_number }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Tipe:</td>
                                    <td>
                                        @if($transaction->type === 'in')
                                            <span class="badge bg-success"><i class="fas fa-arrow-down"></i> Barang Masuk</span>
                                        @else
                                            <span class="badge bg-danger"><i class="fas fa-arrow-up"></i> Barang Keluar</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Tanggal:</td>
                                    <td>{{ \Carbon\Carbon::parse($transaction->transaction_date)->format('d M Y') }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">User:</td>
                                    <td>{{ $transaction->user->name }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <td class="fw-bold" width="150">Kendaraan:</td>
                                    <td>{{ $transaction->vehicle ? $transaction->vehicle->name . ' (' . $transaction->vehicle->plate_number . ')' : '-' }}</td>
                                </tr>
                                @if($transaction->type === 'out' && $transaction->store_name)
                                <tr>
                                    <td class="fw-bold">Nama Toko:</td>
                                    <td><strong>{{ $transaction->store_name }}</strong></td>
                                </tr>
                                @endif
                                @if($transaction->type === 'out')
                                <tr>
                                    <td class="fw-bold">Status Pembayaran:</td>
                                    <td>
                                        @if($transaction->payment_status === 'paid')
                                            <span class="badge bg-success"><i class="fas fa-check-circle"></i> Lunas</span>
                                        @else
                                            <span class="badge bg-warning text-dark"><i class="fas fa-clock"></i> Belum Lunas</span>
                                        @endif
                                    </td>
                                </tr>
                                @endif
                                <tr>
                                    <td class="fw-bold">Total Amount:</td>
                                    <td class="text-primary fs-5 fw-bold">Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Dibuat:</td>
                                    <td>{{ $transaction->created_at->format('d M Y H:i') }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    @if($transaction->notes)
                    <div class="alert alert-info mb-0">
                        <strong><i class="fas fa-sticky-note"></i> Catatan:</strong><br>
                        {{ $transaction->notes }}
                    </div>
                    @endif
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header">
                    <h5 class="mb-0">Detail Item</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th width="5%">No</th>
                                    <th width="35%">Item</th>
                                    <th width="20%" class="text-center">Quantity</th>
                                    <th width="20%" class="text-end">Harga</th>
                                    <th width="20%" class="text-end">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($transaction->details as $index => $detail)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>
                                        <strong>{{ $detail->item->name }}</strong><br>
                                        <small class="text-muted">{{ $detail->item->code }}</small>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-primary">{{ number_format($detail->quantity, 0, ',', '.') }} {{ strtoupper($detail->unit_type) }}</span>
                                    </td>
                                    <td class="text-end">
                                        Rp {{ number_format($detail->price, 0, ',', '.') }}
                                    </td>
                                    <td class="text-end">
                                        Rp {{ number_format($detail->subtotal, 0, ',', '.') }}
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="table-light">
                                @php
                                    $subtotalItems = $transaction->details->sum('subtotal');
                                @endphp
                                
                                <tr>
                                    <th colspan="4" class="text-end">Subtotal Item:</th>
                                    <th class="text-end">
                                        Rp {{ number_format($subtotalItems, 0, ',', '.') }}
                                    </th>
                                </tr>
                                
                                @if($transaction->type === 'out' && ($transaction->discount > 0 || $transaction->bonus > 0))
                                    @if($transaction->discount > 0)
                                    <tr>
                                        <th colspan="4" class="text-end">Diskon:</th>
                                        <th class="text-end text-danger">
                                            - Rp {{ number_format($transaction->discount, 0, ',', '.') }}
                                        </th>
                                    </tr>
                                    @endif
                                    
                                    @if($transaction->bonus > 0)
                                    <tr>
                                        <th colspan="4" class="text-end">Bonus:</th>
                                        <th class="text-end text-danger">
                                            - Rp {{ number_format($transaction->bonus, 0, ',', '.') }}
                                        </th>
                                    </tr>
                                    @endif
                                @endif
                                
                                <tr class="border-top">
                                    <th colspan="4" class="text-end">Total:</th>
                                    <th class="text-end text-primary">
                                        Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}
                                    </th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Aksi</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('transactions.invoice', $transaction) }}" class="btn btn-secondary" target="_blank">
                            <i class="fas fa-file-pdf"></i> Download Invoice PDF
                        </a>
                        <a href="{{ route('transactions.index') }}" class="btn btn-outline-primary">
                            <i class="fas fa-list"></i> Lihat Semua Transaksi
                        </a>
                        @can('delete-transactions')
                        <form action="{{ route('transactions.destroy', $transaction) }}" method="POST" id="deleteForm">
                            @csrf
                            @method('DELETE')
                            <button type="button" class="btn btn-danger w-100" id="deleteBtn">
                                <i class="fas fa-trash"></i> Hapus Transaksi
                            </button>
                        </form>
                        @endcan
                    </div>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header">
                    <h5 class="mb-0">Ringkasan</h5>
                </div>
                <div class="card-body">
                    <table class="table table-sm table-borderless mb-0">
                        <tr>
                            <td class="fw-bold">Total Item:</td>
                            <td class="text-end">{{ $transaction->details->count() }} item</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Total Quantity:</td>
                            <td class="text-end">{{ $transaction->details->sum('quantity') }}</td>
                        </tr>
                        <tr class="border-top">
                            <td class="fw-bold">Total Amount:</td>
                            <td class="text-end text-primary fw-bold">
                                Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    $('#deleteBtn').on('click', function() {
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
                $('#deleteForm').submit();
            }
        });
    });
});
</script>
@endpush
@endsection
