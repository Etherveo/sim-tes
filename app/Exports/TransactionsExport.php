<?php

namespace App\Exports;

use App\Models\Order;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TransactionsExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected $filterType;
    protected $date;
    protected $month;
    protected $year;

    public function __construct($filterType, $date, $month, $year)
    {
        $this->filterType = $filterType;
        $this->date = $date;
        $this->month = $month;
        $this->year = $year;
    }

    public function query()
    {
        $query = Order::query()
            ->with(['user', 'items.product'])
            ->where('status', 'completed');

        // Logika Filter Harian vs Bulanan
        if ($this->filterType == 'daily') {
            $query->whereDate('created_at', $this->date);
        } else {
            $query->whereMonth('created_at', $this->month)
                  ->whereYear('created_at', $this->year);
        }

        return $query;
    }

    public function headings(): array
    {
        return ['ID Pesanan', 'Tanggal', 'Pelanggan', 'Email Pelanggan', 'Item Produk', 'Total Harga'];
    }

    public function map($order): array
    {
        $itemList = $order->items->map(function($item) {
            $name = $item->product ? $item->product->nama_produk : 'Produk Dihapus';
            return $name . " (" . $item->quantity . "x)";
        })->implode(', ');

        return [
            '#' . $order->id,
            $order->created_at->format('d-m-Y H:i'),
            $order->user->nama_lengkap ?? 'Guest',
            $order->user->email ?? '-',
            $itemList,
            $order->total_price
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'size' => 12, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => 'CC5500']],
            ],
        ];
    }
}