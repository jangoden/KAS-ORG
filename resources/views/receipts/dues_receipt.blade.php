<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Bukti Pembayaran - #{{ $transaction->id }}</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; color: #333; font-size: 14px; position: relative; }
        .container { width: 700px; margin: 0 auto; padding: 20px; border: 1px solid #ddd; background: #fff; position: relative; }
        .header { text-align: center; border-bottom: 2px solid #333; padding-bottom: 10px; margin-bottom: 30px; }
        .header h1 { margin: 0; font-size: 22px; }
        .header p { margin: 5px 0 0; font-size: 14px; }
        .details-table { width: 100%; border-collapse: collapse; }
        .details-table td { padding: 8px 0; vertical-align: top; }
        .details-table td:first-child { width: 35%; font-weight: bold; }
        .details-table td:nth-child(2) { width: 2%; }
        .footer { margin-top: 50px; text-align: right; }
        .signature { margin-top: 70px; }
        .paid-stamp {
            position: absolute;
            top: 180px;
            left: 50%;
            font-size: 60px;
            font-weight: bold;
            color: #4caf50;
            opacity: 0.12;
            transform: rotate(-35deg) translateX(-50%);
            border: 4px solid #4caf50;
            padding: 5px 15px;
            border-radius: 8px;
            z-index: 0;
        }
    </style>
</head>
<body>
    <div class="paid-stamp">LUNAS</div>
    <div class="container">
        <div class="header">
            <h1>BUKTI PEMBAYARAN IURAN KAS IPNU</h1>
            <p>PC IPNU Kabupaten Ciamis</p>
        </div>

        <table class="details-table">
            <tr>
                <td>No. Transaksi</td>
                <td>:</td>
                <td>PYM-{{ \Carbon\Carbon::parse($transaction->date)->format('Ymd') }}-{{ str_pad($transaction->id, 4, '0', STR_PAD_LEFT) }}</td>
            </tr>
            <tr>
                <td>Ref Kode</td>
                <td>:</td>
                <td>{{ strtoupper(substr(md5($transaction->id . $transaction->date), 0, 8)) }}</td>
            </tr>
            <tr>
                <td>Tanggal Pembayaran</td>
                <td>:</td>
                <td>{{ \Carbon\Carbon::parse($transaction->date)->translatedFormat('d F Y') }}</td>
            </tr>
            <tr>
                <td>Telah Diterima Dari</td>
                <td>:</td>
                <td>{{ $transaction->member->name }}</td>
            </tr>
            <tr>
                <td>Nomor Induk Anggota (NIA)</td>
                <td>:</td>
                <td>{{ $transaction->member->nia }}</td>
            </tr>
            <tr>
                <td>Untuk Pembayaran</td>
                <td>:</td>
                <td>{{ $transaction->description }}</td>
            </tr>
            <tr>
                <td>Jumlah</td>
                <td>:</td>
                <td><b>Rp {{ number_format($transaction->amount, 0, ',', '.') }},-</b></td>
            </tr>
        </table>

        <div class="footer">
            Ciamis, {{ \Carbon\Carbon::parse($transaction->date)->translatedFormat('d F Y') }}
            <div class="signature">
                Bendahara<br><br><br>
                ( {{ $transaction->treasurer_name ?? '____________________' }} )
            </div>
        </div>
    </div>
</body>
</html>
