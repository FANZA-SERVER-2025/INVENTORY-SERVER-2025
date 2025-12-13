<?php

namespace App\Exports;

use App\Models\Transaction;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TransactionsExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $typeFilter;
    protected $paymentStatusFilter;

    public function __construct($typeFilter = null, $paymentStatusFilter = null)
    {
        $this->typeFilter = $typeFilter;
        $this->paymentStatusFilter = $paymentStatusFilter;
    }

    public function collection()
    {
        $query = Transaction::with(['user', 'vehicle']);
        
        // Filter by transaction type
        if ($this->typeFilter) {
            $query->where('type', $this->typeFilter);
        }

        // Filter by payment status
        if ($this->paymentStatusFilter) {
            $query->where('payment_status', $this->paymentStatusFilter);
        }
        
        return $query->get();
    }

    public function headings(): array
    {
        $headings = [
            'ID',
            'No. Transaksi',
            'Tipe',
            'Tanggal',
            'User',
            'Kendaraan',
            'Total',
        ];

        // Add payment status column if filtering by 'out' type or no filter
        if (!$this->typeFilter || $this->typeFilter === 'out') {
            $headings[] = 'Status Pembayaran';
        }

        $headings[] = 'Catatan';
        $headings[] = 'Dibuat Pada';

        return $headings;
    }

    public function map($transaction): array
    {
        $data = [
            $transaction->id,
            $transaction->transaction_number,
            $transaction->type === 'in' ? 'Masuk' : 'Keluar',
            $transaction->transaction_date->format('d-m-Y'),
            $transaction->user->name,
            $transaction->vehicle ? $transaction->vehicle->name : '-',
            $transaction->total_amount,
        ];

        // Add payment status if not filtering by 'in' type
        if (!$this->typeFilter || $this->typeFilter === 'out') {
            if ($transaction->type === 'out') {
                $data[] = $transaction->payment_status === 'paid' ? 'Lunas' : 'Belum Lunas';
            } else {
                $data[] = '-';
            }
        }

        $data[] = $transaction->notes ?? '-';
        $data[] = $transaction->created_at->format('d-m-Y H:i:s');

        return $data;
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
