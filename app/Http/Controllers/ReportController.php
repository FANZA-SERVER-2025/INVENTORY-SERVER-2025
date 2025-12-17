<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\TransactionDetail;
use App\Exports\ReportsExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class ReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view-reports');
    }

    public function index(Request $request)
    {
        $from = $request->input('from_date') ? Carbon::parse($request->input('from_date'))->startOfDay() : now()->subDays(30)->startOfDay();
        $to = $request->input('to_date') ? Carbon::parse($request->input('to_date'))->endOfDay() : now()->endOfDay();

        // Recap Barang Masuk (IN)
        $inRecap = TransactionDetail::select('item_id',
                DB::raw('SUM(quantity) as total_qty'),
                DB::raw('SUM(subtotal) as total_value')
            )
            ->whereHas('transaction', function ($q) use ($from, $to) {
                $q->where('type', 'in')
                  ->whereBetween('transaction_date', [$from->toDateString(), $to->toDateString()]);
            })
            ->with(['item.category'])
            ->groupBy('item_id')
            ->orderByDesc('total_qty')
            ->get();

        // Recap Barang Keluar (OUT)
        $outRecap = TransactionDetail::select('item_id',
                DB::raw('SUM(quantity) as total_qty'),
                DB::raw('SUM(subtotal) as total_value')
            )
            ->whereHas('transaction', function ($q) use ($from, $to) {
                $q->where('type', 'out')
                  ->whereBetween('transaction_date', [$from->toDateString(), $to->toDateString()]);
            })
            ->with(['item.category'])
            ->groupBy('item_id')
            ->orderByDesc('total_qty')
            ->get();

        // Totals & Counters
        $totals = [
            'in_transactions' => Transaction::where('type', 'in')
                ->whereBetween('transaction_date', [$from->toDateString(), $to->toDateString()])
                ->count(),
            'out_transactions' => Transaction::where('type', 'out')
                ->whereBetween('transaction_date', [$from->toDateString(), $to->toDateString()])
                ->count(),
            'in_qty' => (int) $inRecap->sum('total_qty'),
            'out_qty' => (int) $outRecap->sum('total_qty'),
            'in_amount' => (float) Transaction::where('type', 'in')
                ->whereBetween('transaction_date', [$from->toDateString(), $to->toDateString()])
                ->sum('total_amount'),
            'out_amount' => (float) Transaction::where('type', 'out')
                ->whereBetween('transaction_date', [$from->toDateString(), $to->toDateString()])
                ->sum('total_amount'),
        ];

        // Omset (Revenue from OUT transactions)
        $omset = [
            'weekly' => (float) Transaction::where('type', 'out')
                ->whereBetween('transaction_date', [now()->startOfWeek()->toDateString(), now()->endOfWeek()->toDateString()])
                ->sum('total_amount'),
            'monthly' => (float) Transaction::where('type', 'out')
                ->whereYear('transaction_date', now()->year)
                ->whereMonth('transaction_date', now()->month)
                ->sum('total_amount'),
            'yearly' => (float) Transaction::where('type', 'out')
                ->whereYear('transaction_date', now()->year)
                ->sum('total_amount'),
        ];

        return view('reports.index', [
            'from' => $from->toDateString(),
            'to' => $to->toDateString(),
            'inRecap' => $inRecap,
            'outRecap' => $outRecap,
            'totals' => $totals,
            'omset' => $omset,
        ]);
    }

    public function export(Request $request)
    {
        $from = $request->input('from_date') ? Carbon::parse($request->input('from_date'))->startOfDay() : now()->subDays(30)->startOfDay();
        $to = $request->input('to_date') ? Carbon::parse($request->input('to_date'))->endOfDay() : now()->endOfDay();

        return Excel::download(new ReportsExport($from->toDateString(), $to->toDateString()), 'reports_' . $from->format('Y-m-d') . '_to_' . $to->format('Y-m-d') . '.xlsx');
    }
}
