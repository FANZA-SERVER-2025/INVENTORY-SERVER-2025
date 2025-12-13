@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0 text-gray-800">
        <i class="fas fa-tachometer-alt me-2"></i>Dashboard
    </h1>
    <div class="text-muted">
        <i class="fas fa-calendar-alt me-2"></i>{{ now()->format('l, d F Y') }}
    </div>
</div>

<!-- Statistics Cards -->
<div class="row">
    <!-- Categories -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stats-card info">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Categories</div>
                        <div class="h5 mb-0 font-weight-bold">{{ $stats['categories'] }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-tags fa-2x text-info"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Suppliers -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stats-card success">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Suppliers</div>
                        <div class="h5 mb-0 font-weight-bold">{{ $stats['suppliers'] }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-truck fa-2x text-success"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Items -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stats-card">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Items</div>
                        <div class="h5 mb-0 font-weight-bold">{{ $stats['items'] }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-box fa-2x text-primary"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Vehicles -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stats-card warning">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Vehicles</div>
                        <div class="h5 mb-0 font-weight-bold">{{ $stats['vehicles'] }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-car fa-2x text-warning"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Users -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stats-card info">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Total Users</div>
                        <div class="h5 mb-0 font-weight-bold">{{ $stats['users'] }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-users fa-2x text-info"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Transactions Out -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stats-card danger">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col">
                        <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Items Out</div>
                        <div class="h5 mb-0 font-weight-bold">{{ $stats['transactions_out'] }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-arrow-circle-down fa-2x text-danger"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Transactions Out This Month -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stats-card success">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Items Out (This Month)</div>
                        <div class="h5 mb-0 font-weight-bold">{{ $stats['transactions_out_this_month'] }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-calendar-check fa-2x text-success"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Low Stock Items -->
    <div class="col-xl-6 mb-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="fas fa-exclamation-triangle me-2 text-danger"></i>Low Stock Items (Stock < 10)</span>
                @can('view-items')
                    <a href="{{ route('items.index') }}" class="btn btn-sm btn-primary">View All</a>
                @endcan
            </div>
            <div class="card-body">
                @if($lowStockItems->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-sm table-hover">
                            <thead>
                                <tr>
                                    <th>Item</th>
                                    <th>Category</th>
                                    <th>Stock</th>
                                    <th>Min. Stock</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($lowStockItems as $item)
                                <tr>
                                    <td>
                                        <strong>{{ $item->name }}</strong><br>
                                        <small class="text-muted">{{ $item->code }}</small>
                                    </td>
                                    <td>{{ $item->category ? $item->category->name : '-' }}</td>
                                    <td>
                                        <span class="badge bg-danger">{{ $item->stock }}</span>
                                    </td>
                                    <td>{{ $item->minimum_stock }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center text-muted py-4">
                        <i class="fas fa-check-circle fa-3x mb-3"></i>
                        <p>All items have sufficient stock!</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Popular Items -->
    <div class="col-xl-6 mb-4">
        <div class="card">
            <div class="card-header">
                <i class="fas fa-star me-2 text-warning"></i>Popular Items (Most Sold)
            </div>
            <div class="card-body">
                @if($popularItems->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-sm table-hover">
                            <thead>
                                <tr>
                                    <th>Item</th>
                                    <th>Category</th>
                                    <th>Total Sold</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($popularItems as $popular)
                                <tr>
                                    <td>
                                        <strong>{{ $popular->item->name }}</strong><br>
                                        <small class="text-muted">{{ $popular->item->code }}</small>
                                    </td>
                                    <td>{{ $popular->item->category->name }}</td>
                                    <td>
                                        <span class="badge bg-success">{{ $popular->total_sold }}</span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center text-muted py-4">
                        <i class="fas fa-chart-line fa-3x mb-3"></i>
                        <p>No sales data available yet.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Charts Section -->
<div class="row mb-4">
    <!-- Transaction Trend (Last 7 Days) -->
    <div class="col-xl-6 mb-4">
        <div class="card">
            <div class="card-header">
                <i class="fas fa-chart-line me-2"></i>Transaction Trend (Last 7 Days)
            </div>
            <div class="card-body">
                <canvas id="transactionTrendChart" height="250"></canvas>
            </div>
        </div>
    </div>

    <!-- Stock Status Distribution -->
    <div class="col-xl-6 mb-4">
        <div class="card">
            <div class="card-header">
                <i class="fas fa-chart-pie me-2"></i>Stock Status Distribution
            </div>
            <div class="card-body">
                <canvas id="stockStatusChart" height="250"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="row mb-4">
    <!-- Top Categories -->
    <div class="col-xl-6 mb-4">
        <div class="card">
            <div class="card-header">
                <i class="fas fa-chart-bar me-2"></i>Top 5 Categories by Items
            </div>
            <div class="card-body">
                <canvas id="topCategoriesChart" height="250"></canvas>
            </div>
        </div>
    </div>

    <!-- Monthly Transaction Comparison -->
    <div class="col-xl-6 mb-4">
        <div class="card">
            <div class="card-header">
                <i class="fas fa-chart-bar me-2"></i>Monthly Transaction Comparison (Last 6 Months)
            </div>
            <div class="card-body">
                <canvas id="monthlyComparisonChart" height="250"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="row mb-4">
    <!-- Category Value Distribution -->
    <div class="col-xl-12 mb-4">
        <div class="card">
            <div class="card-header">
                <i class="fas fa-chart-bar me-2"></i>Top 5 Categories by Inventory Value
            </div>
            <div class="card-body">
                <canvas id="categoryValueChart" height="120"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Recent Transactions -->
<div class="row">
    <!-- Recent Transactions -->
    <div class="col-xl-12 mb-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="fas fa-exchange-alt me-2"></i>Recent Transactions</span>
                @can('view-transactions')
                    <a href="{{ route('transactions.index') }}" class="btn btn-sm btn-primary">View All</a>
                @endcan
            </div>
            <div class="card-body">
                @if($recentTransactions->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-sm table-hover">
                            <thead>
                                <tr>
                                    <th>Transaction #</th>
                                    <th>Type</th>
                                    <th>User</th>
                                    <th>Amount</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentTransactions as $transaction)
                                <tr>
                                    <td><strong>{{ $transaction->transaction_number }}</strong></td>
                                    <td>
                                        @if($transaction->type == 'in')
                                            <span class="badge bg-success">IN</span>
                                        @else
                                            <span class="badge bg-danger">OUT</span>
                                        @endif
                                    </td>
                                    <td>{{ $transaction->user->name }}</td>
                                    <td>Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}</td>
                                    <td><small>{{ $transaction->transaction_date->format('d M Y') }}</small></td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center text-muted py-4">
                        <i class="fas fa-inbox fa-3x mb-3"></i>
                        <p>No transactions yet.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

<script>
// Chart Data from Backend
const chartData = @json($chartData);

// Chart.js Global Configuration
Chart.defaults.font.family = 'Nunito, sans-serif';
Chart.defaults.color = '#858796';

// 1. Transaction Trend Chart (Line Chart)
const transactionTrendCtx = document.getElementById('transactionTrendChart').getContext('2d');
new Chart(transactionTrendCtx, {
    type: 'line',
    data: {
        labels: chartData.transaction_dates,
        datasets: [
            {
                label: 'Transactions IN',
                data: chartData.transactions_in,
                borderColor: 'rgb(28, 200, 138)',
                backgroundColor: 'rgba(28, 200, 138, 0.1)',
                tension: 0.4,
                fill: true,
                pointBackgroundColor: 'rgb(28, 200, 138)',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointRadius: 4,
            },
            {
                label: 'Transactions OUT',
                data: chartData.transactions_out,
                borderColor: 'rgb(231, 74, 59)',
                backgroundColor: 'rgba(231, 74, 59, 0.1)',
                tension: 0.4,
                fill: true,
                pointBackgroundColor: 'rgb(231, 74, 59)',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointRadius: 4,
            }
        ]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: true,
                position: 'top',
            },
            tooltip: {
                mode: 'index',
                intersect: false,
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    precision: 0
                }
            }
        }
    }
});

// 2. Stock Status Chart (Doughnut Chart)
const stockStatusCtx = document.getElementById('stockStatusChart').getContext('2d');
new Chart(stockStatusCtx, {
    type: 'doughnut',
    data: {
        labels: ['Normal Stock', 'Low Stock', 'Out of Stock'],
        datasets: [{
            data: [
                chartData.normal_stock,
                chartData.low_stock,
                chartData.out_of_stock
            ],
            backgroundColor: [
                'rgb(28, 200, 138)',
                'rgb(246, 194, 62)',
                'rgb(231, 74, 59)'
            ],
            borderWidth: 2,
            borderColor: '#fff'
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: true,
                position: 'bottom',
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        let label = context.label || '';
                        if (label) {
                            label += ': ';
                        }
                        label += context.parsed + ' items';
                        return label;
                    }
                }
            }
        }
    }
});

// 3. Top Categories Chart (Bar Chart)
const topCategoriesCtx = document.getElementById('topCategoriesChart').getContext('2d');
new Chart(topCategoriesCtx, {
    type: 'bar',
    data: {
        labels: chartData.category_names,
        datasets: [{
            label: 'Number of Items',
            data: chartData.category_counts,
            backgroundColor: 'rgba(78, 115, 223, 0.8)',
            borderColor: 'rgb(78, 115, 223)',
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    precision: 0
                }
            }
        }
    }
});

// 4. Monthly Comparison Chart (Bar Chart)
const monthlyComparisonCtx = document.getElementById('monthlyComparisonChart').getContext('2d');
new Chart(monthlyComparisonCtx, {
    type: 'bar',
    data: {
        labels: chartData.monthly_labels,
        datasets: [
            {
                label: 'IN',
                data: chartData.monthly_in,
                backgroundColor: 'rgba(28, 200, 138, 0.8)',
                borderColor: 'rgb(28, 200, 138)',
                borderWidth: 1
            },
            {
                label: 'OUT',
                data: chartData.monthly_out,
                backgroundColor: 'rgba(231, 74, 59, 0.8)',
                borderColor: 'rgb(231, 74, 59)',
                borderWidth: 1
            }
        ]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: true,
                position: 'top'
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    precision: 0
                }
            }
        }
    }
});

// 5. Category Value Chart (Horizontal Bar Chart)
const categoryValueCtx = document.getElementById('categoryValueChart').getContext('2d');
new Chart(categoryValueCtx, {
    type: 'bar',
    data: {
        labels: chartData.value_category_names,
        datasets: [{
            label: 'Inventory Value (Rp)',
            data: chartData.value_amounts,
            backgroundColor: 'rgba(54, 185, 204, 0.8)',
            borderColor: 'rgb(54, 185, 204)',
            borderWidth: 1
        }]
    },
    options: {
        indexAxis: 'y',
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        let label = context.dataset.label || '';
                        if (label) {
                            label += ': ';
                        }
                        label += 'Rp ' + new Intl.NumberFormat('id-ID').format(context.parsed.x);
                        return label;
                    }
                }
            }
        },
        scales: {
            x: {
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        return 'Rp ' + new Intl.NumberFormat('id-ID', {
                            notation: 'compact',
                            compactDisplay: 'short'
                        }).format(value);
                    }
                }
            }
        }
    }
});
</script>
@endpush
