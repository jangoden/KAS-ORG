<?php

namespace App\Filament\Widgets;

use App\Models\DuesTransaction;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class DashboardReportWidget extends BaseWidget
{
    protected function getStats(): array
    {
        // Ambil data Pemasukan dan Pengeluaran
        $pemasukan = DuesTransaction::where('type', 'masuk')->sum('amount');
        $pengeluaran = DuesTransaction::where('type', 'keluar')->sum('amount');
        $saldo = $pemasukan - $pengeluaran;

        // Ambil data untuk bulan ini saja
        $pemasukanBulanIni = DuesTransaction::where('type', 'masuk')
                                ->whereYear('date', Carbon::now()->year)
                                ->whereMonth('date', Carbon::now()->month)
                                ->sum('amount');
                                
        $pengeluaranBulanIni = DuesTransaction::where('type', 'keluar')
                                ->whereYear('date', Carbon::now()->year)
                                ->whereMonth('date', Carbon::now()->month)
                                ->sum('amount');


        return [
            Stat::make('Saldo Total', 'Rp ' . number_format($saldo, 0, ',', '.'))
                ->description('Total kas keseluruhan')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success'),

            Stat::make('Pemasukan Bulan Ini', 'Rp ' . number_format($pemasukanBulanIni, 0, ',', '.'))
                ->description('Pemasukan di bulan ' . Carbon::now()->translatedFormat('F'))
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('primary'),

            Stat::make('Pengeluaran Bulan Ini', 'Rp ' . number_format($pengeluaranBulanIni, 0, ',', '.'))
                ->description('Pengeluaran di bulan ' . Carbon::now()->translatedFormat('F'))
                ->descriptionIcon('heroicon-m-arrow-trending-down')
                ->color('danger'),
        ];
    }
}
