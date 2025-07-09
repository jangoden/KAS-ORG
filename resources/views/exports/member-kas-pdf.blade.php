<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Kas Anggota - {{ $member->name }}</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 10px; color: #333; }
        .header { text-align: center; margin-bottom: 20px; }
        .title { font-size: 18px; font-weight: bold; }
        .period { font-size: 12px; color: #555; }
        .member-info { margin-bottom: 20px; padding: 10px; border: 1px solid #eee; background-color: #fcfcfc; }
        .member-info p { margin: 0; padding: 2px 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #ccc; padding: 6px; text-align: left; }
        th { background-color: #f2f2f2; font-weight: bold; }
        .footer { margin-top: 30px; text-align: right; font-size: 9px; color: #777; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .summary-container { margin-bottom: 20px; }
        .summary-table { width: 100%; border-collapse: collapse; }
        .summary-table td { padding: 10px; font-size: 12px; border: 1px solid #ccc; text-align: center; }
        .summary-label { font-weight: bold; }
        .total-paid { color: #16a34a; }
        .months-paid { color: #0d6efd; }
        .months-due { color: #dc2626; }
    </style>
</head>
<body>
    <div class="header">
        <div class="title">Laporan Kas Anggota</div>
    </div>

    <div class="member-info">
        <p><strong>Nama Anggota:</strong> {{ $member->name }}</p>
        <p><strong>Nomor Induk Anggota (NIA):</strong> {{ $member->nia }}</p>
        <p><strong>Aktif Sejak:</strong> {{ \Carbon\Carbon::parse($member->tanggal_aktif)->translatedFormat('d F Y') }}</p>
    </div>

    {{-- Ringkasan untuk Anggota --}}
    <div class="summary-container">
        <table class="summary-table">
            <tr>
                <td class="summary-label">Total Iuran Dibayar</td>
                <td class="summary-label">Jumlah Bulan Lunas</td>
                <td class="summary-label">Jumlah Tunggakan</td>
            </tr>
            <tr>
                <td class="total-paid"><strong>Rp {{ number_format($summary['total_paid'] ?? 0, 0, ',', '.') }}</strong></td>
                <td class="months-paid"><strong>{{ $summary['months_paid'] ?? 0 }} Bulan</strong></td>
                <td class="months-due"><strong>{{ $summary['months_due'] ?? 0 }} Bulan</strong></td>
            </tr>
        </table>
    </div>

    {{-- Tabel Riwayat Transaksi Anggota --}}
    <table>
        <thead>
            <tr>
                <th class="text-center" style="width: 5%;">No</th>
                <th style="width: 15%;">Tanggal</th>
                <th>Keterangan</th>
                <th style="width: 15%;">Jenis</th>
                <th class="text-right" style="width: 20%;">Jumlah</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($transactions as $index => $transaction)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ \Carbon\Carbon::parse($transaction->date)->translatedFormat('d M Y') }}</td>
                    <td>{{ $transaction->description }}</td>
                    <td>{{ $transaction->type === 'masuk' ? 'Pemasukan' : 'Pengeluaran' }}</td>
                    <td class="text-right">Rp {{ number_format($transaction->amount, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center">Tidak ada riwayat transaksi untuk anggota ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Dicetak pada: {{ now()->translatedFormat('d F Y H:i') }}
    </div>
</body>
</html>
