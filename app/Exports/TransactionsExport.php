<?php

namespace App\Exports;

use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents; // <-- [DITAMBAHKAN] Untuk event handling
use Maatwebsite\Excel\Events\BeforeSheet;  // <-- [DITAMBAHKAN] Event sebelum sheet dibuat

class TransactionsExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize, WithEvents
{
    protected Builder $query;
    protected string $periodText; // <-- [DITAMBAHKAN] Properti untuk menyimpan teks periode

    /**
     * [DIUBAH] Constructor sekarang menerima query dan teks periode.
     */
    public function __construct(Builder $query, string $periodText)
    {
        $this->query = $query;
        $this->periodText = $periodText;
    }

    /**
     * Metode ini akan dieksekusi oleh library Excel untuk mendapatkan
     * data berdasarkan query yang kita berikan.
     */
    public function query(): Builder
    {
        return $this->query->orderBy('date', 'desc');
    }

    /**
     * Mendefinisikan judul untuk setiap kolom di file Excel.
     */
    public function headings(): array
    {
        return [
            'Tanggal',
            'Jenis',
            'Jumlah',
            'Anggota',
            'Keterangan',
        ];
    }

    /**
     * Memetakan setiap baris data dari database ke format array
     * yang akan ditulis di file Excel.
     *
     * @param \App\Models\DuesTransaction $transaction
     */
    public function map($transaction): array
    {
        return [
            \Carbon\Carbon::parse($transaction->date)->format('d-m-Y'),
            $transaction->type === 'masuk' ? 'Pemasukan' : 'Pengeluaran',
            $transaction->amount,
            $transaction->member->name ?? '-',
            $transaction->description,
        ];
    }

    /**
     * [DITAMBAHKAN] Mendaftarkan event untuk menambahkan judul sebelum tabel.
     */
    public function registerEvents(): array
    {
        return [
            BeforeSheet::class => function(BeforeSheet $event) {
                // Tambahkan 3 baris kosong di atas untuk judul
                $event->sheet->getDelegate()->insertNewRowBefore(1, 3);

                // Tulis judul utama
                $event->sheet->getDelegate()->setCellValue('A1', 'Laporan Kas Wajib');
                $event->sheet->getDelegate()->mergeCells('A1:E1'); // Gabungkan sel untuk judul
                $event->sheet->getDelegate()->getStyle('A1')->getFont()->setBold(true)->setSize(16);
                $event->sheet->getDelegate()->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

                // Tulis judul periode
                $event->sheet->getDelegate()->setCellValue('A2', 'Periode: ' . $this->periodText);
                $event->sheet->getDelegate()->mergeCells('A2:E2');
                $event->sheet->getDelegate()->getStyle('A2')->getFont()->setItalic(true);
                $event->sheet->getDelegate()->getStyle('A2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

                // Pindahkan judul kolom ke baris ke-4
                $event->sheet->getDelegate()->fromArray($this->headings(), null, 'A4');
            },
        ];
    }

    /**
     * [DIUBAH] Kita perlu memberitahu library bahwa headings sekarang
     * akan ditangani oleh event, bukan secara otomatis.
     */
    public function startCell(): string
    {
        return 'A5'; // Data tabel akan dimulai dari baris ke-5
    }
}
