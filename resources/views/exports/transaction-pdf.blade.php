<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $title }} - {{ $period }}</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 10px; color: #333; }
        .header { text-align: center; margin-bottom: 25px; }
        .title { font-size: 18px; font-weight: bold; }
        .period { font-size: 12px; color: #555; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #ccc; padding: 6px; text-align: left; }
        th { background-color: #f2f2f2; font-weight: bold; }
        .footer { margin-top: 30px; text-align: right; font-size: 9px; color: #777; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .summary-container { margin-bottom: 20px; }
        .summary-table { width: 100%; border-collapse: collapse; }
        .summary-table td { padding: 10px; font-size: 12px; border: 1px solid #ccc; }
        .summary-label { font-weight: bold; }
        .total-income { color: #16a34a; }
        .total-expense { color: #dc2626; }
        .balance-positive { color: #166534; }
        .balance-negative { color: #991b1b; }
        
        /* [BARU] Styling untuk pengelompokan bulan */
        .month-header-row th {
            background-color: #e0e0e0;
            font-size: 12px;
            text-align: center;
            padding: 8px;
        }
        .month-footer-row td {
            background-color: #f7f7f7;
            font-weight: bold;
            text-align: right;
            padding: 8px;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="title">{{ $title ?? 'Laporan Kas Wajib' }}</div>
        <div class="period">Periode: {{ $period }}</div>
    </div>

    {{-- Ringkasan Total untuk Seluruh Periode --}}
    <div class="summary-container">
        <table class="summary-table">
            <tr>
                <td class="summary-label">Total Pemasukan</td>
                <td class="summary-label">Total Pengeluaran</td>
                <td class="summary-label">Saldo Akhir</td>
            </tr>
            <tr>
                <td class="total-income"><strong>Rp {{ number_format($summary['total_income'] ?? 0, 0, ',', '.') }}</strong></td>
                <td class="total-expense"><strong>Rp {{ number_format($summary['total_expense'] ?? 0, 0, ',', '.') }}</strong></td>
                <td class="{{ ($summary['balance'] ?? 0) >= 0 ? 'balance-positive' : 'balance-negative' }}">
                    <strong>Rp {{ number_format($summary['balance'] ?? 0, 0, ',', '.') }}</strong>
                </td>
            </tr>
        </table>
    </div>

    {{-- Tabel Utama dengan Pengelompokan per Bulan --}}
    <table>
        <thead>
            <tr>
                <th class="text-center" style="width: 5%;">No</th>
                <th style="width: 15%;">Tanggal</th>
                <th>Anggota</th>
                <th>Keterangan</th>
                <th style="width: 15%;">Jenis</th>
                <th class="text-right" style="width: 20%;">Jumlah</th>
            </tr>
        </thead>
        <tbody>
            {{-- [DIUBAH TOTAL] Logika perulangan untuk menampilkan data per bulan --}}
            @forelse ($groupedTransactions as $periodKey => $data)
                {{-- Baris Judul Bulan --}}
                <tr class="month-header-row">
                    <th colspan="6">
                        Laporan Bulan: {{ \Carbon\Carbon::createFromFormat('Y-m', $periodKey)->translatedFormat('F Y') }}
                    </th>
                </tr>

                {{-- Daftar Transaksi untuk Bulan Ini --}}
                @foreach($data['transactions'] as $index => $transaction)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>{{ \Carbon\Carbon::parse($transaction->date)->translatedFormat('d M Y') }}</td>
                        <td>{{ $transaction->member->name ?? 'Anggota tidak ditemukan' }}</td>
                        <td>{{ $transaction->description }}</td>
                        <td>{{ $transaction->type === 'masuk' ? 'Pemasukan' : 'Pengeluaran' }}</td>
                        <td class="text-right">Rp {{ number_format($transaction->amount, 0, ',', '.') }}</td>
                    </tr>
                @endforeach

                {{-- Baris Subtotal untuk Bulan Ini --}}
                <tr class="month-footer-row">
                    <td colspan="5">
                        <strong>Subtotal Bulan Ini:</strong> &nbsp;&nbsp;
                        <span class="total-income">Pemasukan: Rp {{ number_format($data['subtotal_income'], 0, ',', '.') }}</span> &nbsp;|&nbsp;
                        <span class="total-expense">Pengeluaran: Rp {{ number_format($data['subtotal_expense'], 0, ',', '.') }}</span>
                    </td>
                    <td class="text-right">
                        <strong>Perubahan Kas: Rp {{ number_format($data['subtotal_income'] - $data['subtotal_expense'], 0, ',', '.') }}</strong>
                    </td>
                </tr>

            @empty
                <tr>
                    <td colspan="6" class="text-center">Tidak ada data untuk periode ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Dicetak pada: {{ now()->translatedFormat('d F Y H:i') }}
    </div>
</body>
</html>
