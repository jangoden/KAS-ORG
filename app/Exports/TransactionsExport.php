<?php

namespace App\Exports;

use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeSheet;
use Maatwebsite\Excel\Concerns\WithStyles; // <-- [DITAMBAHKAN] Untuk styling
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet; // <-- [DITAMBAHKAN] Untuk styling

// [DIUBAH] WithHeadings dihapus untuk menghindari konflik
class TransactionsExport implements FromQuery, WithMapping, ShouldAutoSize, WithEvents, WithStyles
{
    protected Builder $query;
    protected string $periodText;
    protected string $title; // <-- [DITAMBAHKAN] Properti untuk judul dinamis

    /**
     * [DIUBAH] Constructor sekarang menerima judul laporan.
     */
    public function __construct(Builder $query, string $periodText, string $title)
    {
        $this->query = $query;
        $this->periodText = $periodText;
        $this->title = $title;
    }

    public function query(): Builder
    {
        // [DIUBAH] Mengurutkan berdasarkan tanggal dari yang terlama (asc) agar di Excel lebih natural
        return $this->query->orderBy('date', 'asc');
    }

    /**
     * [DIUBAH] Metode ini tidak lagi bagian dari WithHeadings, tapi tetap dipakai untuk event.
     */
    public function headings(): array
    {
        return [
            'Tanggal',
            'Jenis',
            'Jumlah',
            'Anggota / Penerima',
            'Keterangan',
        ];
    }

    public function map($transaction): array
    {
        return [
            \Carbon\Carbon::parse($transaction->date)->format('d-m-Y'),
            $transaction->type === 'masuk' ? 'Pemasukan' : 'Pengeluaran',
            $transaction->amount,
            // [DIUBAH] Logika untuk menampilkan nama anggota atau penerima
            $transaction->type === 'masuk' ? ($transaction->member->name ?? '-') : ($transaction->recipient_name ?? '-'),
            $transaction->description,
        ];
    }

    public function registerEvents(): array
    {
        return [
            BeforeSheet::class => function(BeforeSheet $event) {
                // Atur 3 baris di atas untuk judul
                $event->sheet->getDelegate()->insertNewRowBefore(1, 3);

                // Tulis judul utama (dinamis)
                $event->sheet->getDelegate()->setCellValue('A1', $this->title);
                $event->sheet->getDelegate()->mergeCells('A1:E1');
                $event->sheet->getDelegate()->getStyle('A1')->getFont()->setBold(true)->setSize(16);
                $event->sheet->getDelegate()->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

                // Tulis judul periode
                $event->sheet->getDelegate()->setCellValue('A2', $this->periodText);
                $event->sheet->getDelegate()->mergeCells('A2:E2');
                $event->sheet->getDelegate()->getStyle('A2')->getFont()->setItalic(true);
                $event->sheet->getDelegate()->getStyle('A2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

                // Tulis judul kolom secara manual di baris ke-4
                // Data dari query akan otomatis dimulai di baris 5
                $event->sheet->getDelegate()->fromArray($this->headings(), null, 'A4');
            },
        ];
    }
    
    /**
     * [DITAMBAHKAN] Memberikan style pada sheet.
     */
    public function styles(Worksheet $sheet)
    {
        // Berikan style bold pada baris header (A4 sampai E4)
        return [
            4 => ['font' => ['bold' => true]],
        ];
    }
}