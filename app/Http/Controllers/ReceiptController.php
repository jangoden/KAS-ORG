<?php

namespace App\Http\Controllers;

use App\Models\DuesTransaction;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Gate;

class ReceiptController extends Controller
{
    public function download(DuesTransaction $transaction)
    {
        // Otorisasi: Cek apakah user punya izin 'print_dues::transaction'
        if (! Gate::allows('print_dues::transaction', $transaction)) {
            abort(403, 'AKSES DITOLAK');
        }

        // Siapkan data untuk dikirim ke view PDF
        $data = [
            'transaction' => $transaction,
        ];

        // Buat PDF dari view 'receipts.dues_receipt'
        $pdf = Pdf::loadView('receipts.dues_receipt', $data);

        // Atur nama file PDF
        $filename = 'bukti-pembayaran-' . str_replace(' ', '-', strtolower($transaction->member->name)) . '-' . $transaction->id . '.pdf';

        // Tampilkan PDF di browser
        return $pdf->stream($filename);
    }

    public function downloadExpense(DuesTransaction $transaction)
    {
        // Pastikan hanya transaksi 'keluar' yang bisa diakses melalui route ini
        if ($transaction->type !== 'keluar') {
            abort(404, 'Transaksi tidak ditemukan');
        }

        // Buat PDF dari view 'receipts.expense_receipt' yang baru kita buat
        $pdf = Pdf::loadView('receipts.expense_receipt', ['transaction' => $transaction]);

        // Atur nama file
        $filename = 'bukti-pengeluaran-' . $transaction->id . '.pdf';

        // Tampilkan PDF di browser
        return $pdf->stream($filename);
    }
}