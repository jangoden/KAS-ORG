<?php

namespace App\Filament\Widgets;

use App\Filament\Pages\LaporanStatusPembayaran;
use App\Filament\Resources\DuesTransactionResource;
use App\Filament\Resources\MemberResource;
use App\Models\DuesTransaction;
use App\Models\Member;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Number;

class DuesStatsWidget extends BaseWidget
{
    protected ?string $heading  = 'Ringkasan Kas Saat Ini';
    protected static ?int $sort = 1;

    protected static bool $isLazy = false;

    protected function getStats(): array
    {
        // --- Inisialisasi Tanggal ---
        $now       = Carbon::now();
        $month     = $now->month;
        $year      = $now->year;
        $monthName = $now->translatedFormat('F');

        // --- 1. Kalkulasi untuk Total Saldo Kas ---
        $totalIncome = DuesTransaction::where('type', 'masuk')->sum('amount');
        $totalExpenses = DuesTransaction::where('type', 'keluar')->sum('amount');
        $totalBalance = $totalIncome - $totalExpenses;
        
        // Persiapan deskripsi untuk widget Total Saldo
        $totalExpensesText = '<span style="color:red;">' . Number::currency($totalExpenses, 'IDR', 'id') . '</span>';
        $deskripsiTotalSaldo = new HtmlString('Pemasukan: ' . Number::currency($totalIncome, 'IDR', 'id') . ' • Pengeluaran: ' . $totalExpensesText);


        // --- 2. Kalkulasi untuk Saldo Bulan Ini (berdasarkan PERIODE IURAN) ---
        $incomeForThisMonthPeriod = DuesTransaction::where('type', 'masuk')->where('period_month', $month)->where('period_year', $year)->sum('amount');
        $expensesThisMonth = DuesTransaction::where('type', 'keluar')->whereYear('date', $year)->whereMonth('date', $month)->sum('amount');
        $balanceThisMonth = $incomeForThisMonthPeriod - $expensesThisMonth;

        // Persiapan deskripsi untuk widget Saldo Bulan Ini
        $pemasukanBulanIniStr = Number::currency($incomeForThisMonthPeriod, 'IDR', 'id');
        $pengeluaranBulanIniStr = '<span style="color:red;">' . Number::currency($expensesThisMonth, 'IDR', 'id') . '</span>';
        $deskripsiSaldoBulanIni = new HtmlString('Iuran Terkumpul: ' . $pemasukanBulanIniStr . ' • Pengeluaran: ' . $pengeluaranBulanIniStr);


        // --- 3. Kalkulasi Akurat untuk Status Iuran Bulan Ini ---
        $activeMembersForDues = Member::where('tanggal_aktif', '<=', $now->endOfMonth())->count();
        $paidMembers = DuesTransaction::where('type', 'masuk')
            ->where('period_month', $month)
            ->where('period_year', $year)
            ->distinct()
            ->count('member_id');
        $paymentPercentage = $activeMembersForDues > 0 ? round(($paidMembers / $activeMembersForDues) * 100) : 0;


        // --- 4. Kalkulasi untuk Total Anggota Aktif ---
        $totalActiveMembers = Member::count();


        // --- Tampilan Semua Widget ---
        return [
            // Widget untuk Total Saldo Keseluruhan
            Stat::make('Total Saldo Kas', Number::currency($totalBalance, 'IDR', 'id'))
                ->description($deskripsiTotalSaldo)
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('primary')
                ->url(DuesTransactionResource::getUrl('index')),

            // Widget untuk Saldo Bulan Ini
            Stat::make('Saldo Bulan Ini (' . $monthName . ')', Number::currency($balanceThisMonth, 'IDR', 'id'))
                ->description($deskripsiSaldoBulanIni)
                ->descriptionIcon($balanceThisMonth >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($balanceThisMonth >= 0 ? 'success' : 'danger'),

            // Widget untuk Status Iuran Bulan Ini
            Stat::make("Iuran {$monthName}", "{$paidMembers} / {$activeMembersForDues} Lunas")
                ->description("$paymentPercentage% Anggota telah membayar")
                ->descriptionIcon('heroicon-m-chart-pie')
                ->color('info')
                ->url(LaporanStatusPembayaran::getUrl()),
                
            // Widget untuk Total Anggota Aktif
            Stat::make('Total Anggota Aktif', $totalActiveMembers)
                ->description('Jumlah seluruh anggota terdaftar')
                ->descriptionIcon('heroicon-m-users')
                ->color('warning')
                ->url(MemberResource::getUrl('index')),
        ];
    }
}
