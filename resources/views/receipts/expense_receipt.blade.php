<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Bukti Pengeluaran Kas</title>
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            color: #333;
            font-size: 14px;
        }
        .container {
            border: 1px solid #ccc;
            padding: 20px;
            width: 700px;
            margin: auto;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .header h2 {
            margin: 0;
            font-size: 18px;
        }
        .header h1 {
            margin: 5px 0;
            font-size: 24px;
        }
        .header img {
            height: 50px;
        }
        .content table {
            width: 100%;
            border-collapse: collapse;
        }
        .content td {
            padding: 8px 0;
        }
        .content .label {
            width: 220px;
            font-weight: bold;
        }
        .signatures {
            margin-top: 40px;
            width: 100%;
        }
        .signatures td {
            width: 50%;
            text-align: center;
            padding-top: 60px;
        }
    </style>
</head>
<body>
    <div class="container">

        <!-- Header dengan nama organisasi dan logo -->
        <div class="header">
            {{-- Ganti dengan logo asli jika ada --}}
            {{-- <img src="{{ asset('path/to/logo.png') }}" alt="Logo Organisasi"> --}}
            <h2>PC IPNU KABUPATEN CIAMIS</h2>
            <h1>BUKTI PENGELUARAN KAS</h1>
        </div>

        <div class="content">
            <table>
                <tr>
                    <td class="label">No. Transaksi</td>
                    <td>: TRX-{{ \Carbon\Carbon::parse($transaction->date)->format('Ymd') }}-{{ str_pad($transaction->id, 4, '0', STR_PAD_LEFT) }}</td>
                </tr>
                <tr>
                    <td class="label">Ref Kode</td>
                    <td>: {{ strtoupper(substr(md5($transaction->id . $transaction->date), 0, 8)) }}</td>
                </tr>
                <tr>
                    <td class="label">Tanggal Transaksi</td>
                    <td>: {{ \Carbon\Carbon::parse($transaction->date)->translatedFormat('d F Y') }}</td>
                </tr>
                <tr>
                    <td class="label">Telah Diserahkan Kepada</td>
                    <td>: {{ $transaction->recipient_name }}</td>
                </tr>
                <tr>
                    <td class="label">Untuk Keperluan</td>
                    <td>: {{ $transaction->description }}</td>
                </tr>
                <tr>
                    <td class="label">Jumlah</td>
                    <td>: <b>Rp {{ number_format($transaction->amount, 0, ',', '.') }},-</b></td>
                </tr>
            </table>
        </div>

        <table class="signatures">
            <tr>
                <td>
                    Ciamis, {{ \Carbon\Carbon::parse($transaction->date)->translatedFormat('d F Y') }}
                    <br>
                    Bendahara
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
