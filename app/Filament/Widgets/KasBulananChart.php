<?php

namespace App\Filament\Widgets;

use App\Models\DuesTransaction;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;
use Filament\Support\RawJs; // <-- [DITAMBAHKAN] Untuk format Javascript

class KasBulananChart extends ChartWidget
{
    // [DIUBAH] Judul chart disesuaikan dengan gambar
    protected static ?string $heading = 'Grafik Laporan Kas Wajib';

    protected static ?int $sort = 2;

    protected int | string | array $columnSpan = 'full';

    protected function getType(): string
    {
        return 'bar';
    }
    
    // [DITAMBAHKAN] Fungsi untuk memformat sumbu Y dengan 'Rp'
    protected function getOptions(): RawJs
    {
        return RawJs::make(<<<JS
        {
            scales: {
                y: {
                    ticks: {
                        callback: (value) => 'Rp ' + new Intl.NumberFormat('id-ID').format(value),
                    },
                },
            },
        }
        JS);
    }

    // [DIUBAH TOTAL] Fungsi utama untuk mengambil dan memformat data
    protected function getData(): array
    {
        // 1. Ambil semua data dalam satu query yang efisien
        $transactions = DuesTransaction::query()
            ->select(
                DB::raw('YEAR(date) as year'),
                DB::raw('MONTH(date) as month'),
                'type',
                DB::raw('SUM(amount) as total')
            )
            ->groupBy('year', 'month', 'type')
            ->orderBy('year')
            ->orderBy('month')
            ->get();

        // 2. Proses data untuk chart
        // Mengelompokkan data berdasarkan periode (contoh: "2025-6" untuk Juni 2025)
        $groupedData = $transactions->groupBy(function ($item) {
            return $item->year . '-' . $item->month;
        });

        $labels = [];
        $pemasukanData = [];
        $pengeluaranData = [];

        // 3. Siapkan semua label bulan yang unik dan urutkan
        $uniquePeriods = $transactions->map(function ($item) {
            return Carbon::create($item->year, $item->month, 1);
        })->unique()->sort();

        // 4. Isi data pemasukan dan pengeluaran untuk setiap label bulan
        foreach ($uniquePeriods as $period) {
            $periodKey = $period->year . '-' . $period->month;
            $labels[] = $period->translatedFormat('M'); // Label sumbu X: "Feb", "Jun"

            $monthlyData = $groupedData->get($periodKey);

            // Cari total pemasukan untuk bulan ini, jika tidak ada, nilainya 0
            $pemasukanData[] = $monthlyData?->firstWhere('type', 'masuk')?->total ?? 0;
            
            // Cari total pengeluaran untuk bulan ini, jika tidak ada, nilainya 0
            $pengeluaranData[] = $monthlyData?->firstWhere('type', 'keluar')?->total ?? 0;
        }

        // 5. Kembalikan data dalam format yang dimengerti Chart.js
        return [
            'datasets' => [
                [
                    'label' => 'Masuk', // <-- [DIUBAH] Label legenda
                    'data' => $pemasukanData,
                    'backgroundColor' => 'rgba(75, 192, 192, 0.5)',
                    'borderColor' => 'rgb(75, 192, 192)',
                    'borderWidth' => 1,
                ],
                [
                    'label' => 'Keluar', // <-- [DIUBAH] Label legenda
                    'data' => $pengeluaranData,
                    'backgroundColor' => 'rgba(255, 99, 132, 0.5)',
                    'borderColor' => 'rgb(255, 99, 132)',
                    'borderWidth' => 1,
                ],
            ],
            'labels' => $labels,
        ];
    }
}
