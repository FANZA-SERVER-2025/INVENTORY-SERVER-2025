<?php

namespace App\Exports;

use App\Models\Vehicle;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class VehiclesExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    public function collection()
    {
        return Vehicle::all();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Nama Kendaraan',
            'Plat Nomor',
            'Tipe',
            'Merk',
            'Tahun',
            'Deskripsi',
            'Status',
            'Dibuat Pada',
            'Diperbarui Pada'
        ];
    }

    public function map($vehicle): array
    {
        return [
            $vehicle->id,
            $vehicle->name,
            $vehicle->plate_number,
            $vehicle->type,
            $vehicle->brand,
            $vehicle->year,
            $vehicle->description,
            $vehicle->is_active ? 'Aktif' : 'Tidak Aktif',
            $vehicle->created_at->format('d-m-Y H:i:s'),
            $vehicle->updated_at->format('d-m-Y H:i:s'),
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
