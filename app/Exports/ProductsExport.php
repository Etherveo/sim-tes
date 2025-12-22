<?php

namespace App\Exports;

use App\Models\OrderItem;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Support\Facades\DB;

class ProductsExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected $month;
    protected $year;

    public function __construct($month, $year)
    {
        $this->month = $month;
        $this->year = $year;
    }

    public function collection()
    {
        return OrderItem::whereHas('order', function($q) {
                $q->where('status', 'completed')
                  ->whereMonth('created_at', $this->month)
                  ->whereYear('created_at', $this->year);
            })
            ->select('product_id', DB::raw('SUM(quantity) as total_qty'), DB::raw('SUM(quantity * price) as total_income'))
            ->with('product')
            ->groupBy('product_id')
            ->orderByDesc('total_qty')
            ->get();
    }

    public function headings(): array
    {
        return ['Nama Produk', 'Kategori', 'Jumlah Terjual', 'Total Pendapatan'];
    }

    public function map($item): array
    {
        return [
            $item->product ? $item->product->nama_produk : 'Produk Dihapus',
            $item->product ? $item->product->kategori_produk : '-',
            $item->total_qty,
            $item->total_income
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style baris pertama (Header)
            1 => [
                'font' => [
                    'bold' => true, 
                    'size' => 12,
                    'color' => ['rgb' => 'FFFFFF'], // Teks Putih
                ],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'CC5500'], // Orange Gelap (Burnt Orange)
                ],
            ],
        ];
    }
}