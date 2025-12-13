<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Supplier;
use App\Models\Item;
use App\Models\Vehicle;
use App\Models\User;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Statistics Cards
        $stats = [
            'categories' => Category::count(),
            'suppliers' => Supplier::count(),
            'items' => Item::count(),
            'vehicles' => Vehicle::count(),
            'users' => User::count(),
            'transactions_out' => Transaction::where('type', 'out')->count(),
            'transactions_out_this_month' => Transaction::where('type', 'out')
                ->thisMonth()
                ->count(),
        ];

        // Low Stock Items (stock < minimum_stock)
        $lowStockItems = Item::lowStock()
            ->with(['category', 'supplier'])
            ->limit(10)
            ->get();

        // Popular Items (most sold)
        $popularItems = TransactionDetail::select('item_id', DB::raw('SUM(quantity) as total_sold'))
            ->groupBy('item_id')
            ->orderByDesc('total_sold')
            ->with('item.category')
            ->limit(5)
            ->get();

        // Recent Transactions
        $recentTransactions = Transaction::with(['user', 'details.item'])
            ->latest()
            ->limit(5)
            ->get();

        // Chart Data
        $chartData = $this->getChartData();

        return view('dashboard', compact(
            'stats',
            'lowStockItems',
            'popularItems',
            'recentTransactions',
            'chartData'
        ));
    }

    private function getChartData()
    {
        // 1. Transaction Trend (Last 7 Days) - Line Chart
        $last7Days = collect();
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $last7Days->push([
                'date' => $date->format('Y-m-d'),
                'label' => $date->format('d M'),
                'in' => Transaction::whereDate('transaction_date', $date)
                    ->where('type', 'in')
                    ->count(),
                'out' => Transaction::whereDate('transaction_date', $date)
                    ->where('type', 'out')
                    ->count(),
            ]);
        }

        // 2. Stock Status Distribution - Doughnut Chart
        $totalItems = Item::count();
        $lowStockCount = Item::whereRaw('stock < minimum_stock')->count();
        $outOfStockCount = Item::where('stock', 0)->count();
        $normalStockCount = Item::whereRaw('stock >= minimum_stock')
            ->where('stock', '>', 0)
            ->count();

        // 3. Top 5 Categories by Item Count - Bar Chart
        $topCategories = Category::withCount('items')
            ->orderByDesc('items_count')
            ->limit(5)
            ->get();

        // 4. Monthly Transaction Comparison (Last 6 Months) - Bar Chart
        $monthlyData = collect();
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $monthlyData->push([
                'month' => $month->format('M Y'),
                'in' => Transaction::whereYear('transaction_date', $month->year)
                    ->whereMonth('transaction_date', $month->month)
                    ->where('type', 'in')
                    ->count(),
                'out' => Transaction::whereYear('transaction_date', $month->year)
                    ->whereMonth('transaction_date', $month->month)
                    ->where('type', 'out')
                    ->count(),
            ]);
        }

        // 5. Item Value Distribution - Get total value by category
        $categoryValues = Category::with('items')
            ->get()
            ->map(function($category) {
                $totalValue = $category->items->sum(function($item) {
                    return $item->stock * $item->purchase_price;
                });
                return [
                    'name' => $category->name,
                    'value' => $totalValue
                ];
            })
            ->sortByDesc('value')
            ->take(5)
            ->values();

        return [
            // Transaction Trend
            'transaction_dates' => $last7Days->pluck('label'),
            'transactions_in' => $last7Days->pluck('in'),
            'transactions_out' => $last7Days->pluck('out'),
            
            // Stock Status
            'normal_stock' => $normalStockCount,
            'low_stock' => $lowStockCount,
            'out_of_stock' => $outOfStockCount,
            
            // Top Categories
            'category_names' => $topCategories->pluck('name'),
            'category_counts' => $topCategories->pluck('items_count'),
            
            // Monthly Comparison
            'monthly_labels' => $monthlyData->pluck('month'),
            'monthly_in' => $monthlyData->pluck('in'),
            'monthly_out' => $monthlyData->pluck('out'),
            
            // Category Values
            'value_category_names' => $categoryValues->pluck('name'),
            'value_amounts' => $categoryValues->pluck('value'),
        ];
    }
}
