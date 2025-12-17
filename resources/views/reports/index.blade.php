@extends('layouts.app')

@section('title', 'Reports')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0"><i class="fas fa-chart-line me-2"></i>Reports</h1>
</div>

<div class="card mb-4">
    <div class="card-body">
        <form class="row g-3" method="GET" action="{{ route('reports.index') }}">
            <div class="col-md-4">
                <label class="form-label">From Date</label>
                <input type="date" name="from_date" class="form-control" value="{{ request('from_date', $from) }}">
            </div>
            <div class="col-md-4">
                <label class="form-label">To Date</label>
                <input type="date" name="to_date" class="form-control" value="{{ request('to_date', $to) }}">
            </div>
            <div class="col-md-4 d-flex align-items-end">
                <button class="btn btn-primary me-2" type="submit"><i class="fas fa-filter me-1"></i> Filter</button>
                <a class="btn btn-secondary me-2" href="{{ route('reports.index') }}"><i class="fas fa-undo me-1"></i> Reset</a>
                <a class="btn btn-success" href="{{ route('reports.export', request()->query()) }}"><i class="fas fa-download me-1"></i> Export Excel</a>
            </div>
        </form>
    </div>
</div>

<div class="row">
    <div class="col-md-3">
        <div class="card border-start-success border-3 mb-3">
            <div class="card-body">
                <div class="text-xs text-uppercase text-muted mb-1">Transaksi Masuk</div>
                <div class="h5 mb-0 fw-bold">{{ number_format($totals['in_transactions']) }}</div>
                <div class="small text-muted mt-1">Qty: {{ number_format($totals['in_qty']) }}</div>
                <div class="small text-muted">Amount: Rp {{ number_format($totals['in_amount'], 0, ',', '.') }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-start-danger border-3 mb-3">
            <div class="card-body">
                <div class="text-xs text-uppercase text-muted mb-1">Transaksi Keluar</div>
                <div class="h5 mb-0 fw-bold">{{ number_format($totals['out_transactions']) }}</div>
                <div class="small text-muted mt-1">Qty: {{ number_format($totals['out_qty']) }}</div>
                <div class="small text-muted">Amount: Rp {{ number_format($totals['out_amount'], 0, ',', '.') }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-start-primary border-3 mb-3">
            <div class="card-body">
                <div class="text-xs text-uppercase text-muted mb-1">Omset Mingguan</div>
                <div class="h5 mb-0 fw-bold">Rp {{ number_format($omset['weekly'], 0, ',', '.') }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-start-primary border-3 mb-3">
            <div class="card-body">
                <div class="text-xs text-uppercase text-muted mb-1">Omset Bulanan</div>
                <div class="h5 mb-0 fw-bold">Rp {{ number_format($omset['monthly'], 0, ',', '.') }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-start-primary border-3 mb-3">
            <div class="card-body">
                <div class="text-xs text-uppercase text-muted mb-1">Omset Tahunan</div>
                <div class="h5 mb-0 fw-bold">Rp {{ number_format($omset['yearly'], 0, ',', '.') }}</div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-6">
        <div class="card mb-4">
            <div class="card-header bg-success text-white">
                <i class="fas fa-arrow-down me-2"></i>Rekapan Barang Masuk ({{ $from }} - {{ $to }})
            </div>
            <div class="card-body table-responsive">
                <table class="table table-striped table-hover align-middle">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Item</th>
                            <th>Category</th>
                            <th class="text-end">Total Qty</th>
                            <th class="text-end">Total Value</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($inRecap as $idx => $row)
                        <tr>
                            <td>{{ $idx+1 }}</td>
                            <td>{{ $row->item?->name ?? '-' }}</td>
                            <td>{{ $row->item?->category?->name ?? '-' }}</td>
                            <td class="text-end">{{ number_format($row->total_qty) }}</td>
                            <td class="text-end">Rp {{ number_format($row->total_value, 0, ',', '.') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted">Tidak ada data</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card mb-4">
            <div class="card-header bg-danger text-white">
                <i class="fas fa-arrow-up me-2"></i>Rekapan Barang Keluar ({{ $from }} - {{ $to }})
            </div>
            <div class="card-body table-responsive">
                <table class="table table-striped table-hover align-middle">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Item</th>
                            <th>Category</th>
                            <th class="text-end">Total Qty</th>
                            <th class="text-end">Total Value</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($outRecap as $idx => $row)
                        <tr>
                            <td>{{ $idx+1 }}</td>
                            <td>{{ $row->item?->name ?? '-' }}</td>
                            <td>{{ $row->item?->category?->name ?? '-' }}</td>
                            <td class="text-end">{{ number_format($row->total_qty) }}</td>
                            <td class="text-end">Rp {{ number_format($row->total_value, 0, ',', '.') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted">Tidak ada data</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
