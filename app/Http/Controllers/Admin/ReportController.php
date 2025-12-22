<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

// Import Library Excel dan Export Classes
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\TransactionsExport;
use App\Exports\ProductsExport;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        // 1. Ambil Parameter Filter dengan Default
        $filterType = $request->input('filter_type', 'monthly');
        $date = $request->input('date', date('Y-m-d'));
        $month = $request->input('month', now()->month);
        $year = $request->input('year', now()->year);

        // 2. Query Dasar
        $query = Order::with(['user', 'items.product'])
            ->where('status', 'completed');

        // 3. Terapkan Filter Waktu & Set Period Label
        // KITA DEKLARASIKAN VARIABEL INI SECARA EKSPLISIT
        $periodLabel = ""; 

        if ($filterType == 'daily') {
            $query->whereDate('created_at', $date);
            $periodLabel = "Harian: " . Carbon::parse($date)->isoFormat('D MMMM Y');
        } else {
            $query->whereMonth('created_at', $month)->whereYear('created_at', $year);
            $periodLabel = "Bulanan: " . Carbon::createFromDate($year, $month)->isoFormat('MMMM Y');
        }

        $orders = $query->latest()->get();

        // 4. Hitung Ringkasan
        $totalRevenue = $orders->sum('total_price');
        $totalTransactions = $orders->count();
        $totalItemsSold = $orders->sum(fn($o) => $o->items->sum('quantity'));

        // 5. Hitung Produk Terlaris
        $topProducts = OrderItem::whereHas('order', function($q) use ($filterType, $date, $month, $year) {
                $q->where('status', 'completed');
                if ($filterType == 'daily') {
                    $q->whereDate('created_at', $date);
                } else {
                    $q->whereMonth('created_at', $month)->whereYear('created_at', $year);
                }
            })
            ->select('product_id', DB::raw('SUM(quantity) as total_qty'), DB::raw('SUM(quantity * price) as total_income'))
            ->with('product')
            ->groupBy('product_id')
            ->orderByDesc('total_qty')
            ->get();

        // Kirim semua variabel ke view, termasuk periodLabel yang sudah pasti terdefinisi
        return view('admin.reports.index', compact(
            'orders', 'topProducts', 
            'totalRevenue', 'totalTransactions', 'totalItemsSold', 
            'filterType', 'date', 'month', 'year', 'periodLabel'
        ));
    }

    /**
     * Export Transaksi ke .xlsx
     */
    public function exportTransactions(Request $request)
    {
        $filterType = $request->input('filter_type', 'monthly');
        $date = $request->input('date', date('Y-m-d'));
        $month = $request->input('month', now()->month);
        $year = $request->input('year', now()->year);

        $name = $filterType == 'daily' ? 'Harian_' . $date : 'Bulanan_' . $month . '_' . $year;
        
        return Excel::download(
            new TransactionsExport($filterType, $date, $month, $year), 
            "Laporan_Transaksi_{$name}.xlsx"
        );
    }

    /**
     * Export Produk ke .xlsx
     */
    public function exportProducts(Request $request)
    {
        $filterType = $request->input('filter_type', 'monthly');
        $date = $request->input('date', date('Y-m-d'));
        $month = $request->input('month', now()->month);
        $year = $request->input('year', now()->year);

        $name = $filterType == 'daily' ? 'Harian_' . $date : 'Bulanan_' . $month . '_' . $year;

        return Excel::download(
            new ProductsExport($filterType, $date, $month, $year), 
            "Laporan_Produk_Terlaris_{$name}.xlsx"
        );
    }
}