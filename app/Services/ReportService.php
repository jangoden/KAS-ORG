<?php
namespace App\Services;

use App\Models\DuesTransaction;
use App\Models\Transaction;
use Illuminate\Support\Carbon;

class ReportService
{
    private function applyMonthFilter($query, ?string $filterMonth, ?string $filterYear)
    {
        if (! $filterMonth || ! $filterYear) {
            return;
        }

        // Konversi ke integer
        $month = (int) $filterMonth;
        $year  = (int) $filterYear;

        // Validasi range bulan (1-12)
        if ($month < 1 || $month > 12) {
            throw new \InvalidArgumentException('Bulan harus antara 1-12');
        }

        // Gunakan whereMonth dan whereYear secara terpisah
        $query->whereYear('date', $year)
            ->whereMonth('date', $month);
    }
    public function generateSummary(
        ?string $filterMode,
        ?string $filterMonth,
        ?string $filterYear,
        ?string $startDate,
        ?string $endDate,
        ?string $categoryId,
        ?string $type
    ): array {
        // Query untuk transaksi reguler
        $transactionQuery = $this->buildBaseQuery(
            Transaction::query(),
            $filterMode,
            $filterMonth,
            $filterYear,
            $startDate,
            $endDate,
            $categoryId,
            $type,
            'type'
        );

        // Query untuk transaksi iuran
        $duesQuery = $this->buildBaseQuery(
            DuesTransaction::query(),
            $filterMode,
            $filterMonth,
            $filterYear,
            $startDate,
            $endDate,
            null, // categoryId tidak berlaku untuk dues
            $type,
            'type',
            ['masuk' => 'pemasukan', 'keluar' => 'pengeluaran']
        );

        $totalIncome = $transactionQuery->clone()
            ->where('type', 'pemasukan')
            ->sum('amount')
         + $duesQuery->clone()
            ->where('type', 'masuk')
            ->sum('amount');

        $totalExpense = $transactionQuery->clone()
            ->where('type', 'pengeluaran')
            ->sum('amount')
         + $duesQuery->clone()
            ->where('type', 'keluar')
            ->sum('amount');

        return [
            'total_income'  => $totalIncome ?? 0,
            'total_expense' => $totalExpense ?? 0,
            'balance'       => ($totalIncome ?? 0) - ($totalExpense ?? 0),
        ];
    }

    private function buildBaseQuery(
        $query,
        ?string $filterMode,
        ?string $filterMonth,
        ?string $filterYear,
        ?string $startDate,
        ?string $endDate,
        ?string $categoryId,
        ?string $type,
        string $typeColumn,
        array $typeMapping = null
    ) {
        // Validasi bulan dan tahun
        $filterMonth = is_numeric($filterMonth) ? (int) $filterMonth : null;
        $filterYear  = is_numeric($filterYear) ? (int) $filterYear : null;

        // Apply date filters
        switch ($filterMode) {
            case 'month':
                if ($filterMonth && $filterYear) {
                    // Pastikan bulan valid (1-12)
                    if ($filterMonth >= 1 && $filterMonth <= 12) {
                        $query->whereYear('date', $filterYear)
                            ->whereMonth('date', $filterMonth);
                    }
                }
                break;

            case 'year':
                if ($filterYear) {
                    $query->whereYear('date', $filterYear);
                }
                break;

            case 'range':
                if ($startDate && $endDate) {
                    try {
                        $start = Carbon::parse($startDate);
                        $end   = Carbon::parse($endDate);
                        $query->whereBetween('date', [$start, $end]);
                    } catch (\Exception $e) {
                        // Tangani error parsing tanggal
                    }
                }
                break;
        }

        // Apply category filter
        if ($categoryId && property_exists($query->getModel(), 'category_id')) {
            $query->where('category_id', $categoryId);
        }

        // Apply type filter with mapping if needed
        if ($type) {
            if ($typeMapping) {
                $query->where($typeColumn, array_search($type, $typeMapping));
            } else {
                $query->where($typeColumn, $type);
            }
        }

        return $query;
    }
}
