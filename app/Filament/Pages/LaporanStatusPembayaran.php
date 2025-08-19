<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\DuesStatusOverview;
use App\Models\DuesTransaction;
use App\Models\Member;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Pages\Page;
use Filament\Forms\Form;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Grid;
use Illuminate\Support\Collection; // <-- [DIUBAH] Menggunakan Support Collection

class LaporanStatusPembayaran extends Page implements Forms\Contracts\HasForms
{
    use Forms\Concerns\InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-check-circle';
    protected static ?string $navigationLabel = 'Status Iuran';
    protected static ?string $navigationGroup = 'Kas';
    protected static ?string $slug = 'laporan-status-pembayaran';
    protected static ?string $title = 'Status iuran';
    protected static string $view = 'filament.pages.laporan-status-pembayaran';

    public string $activeTab = 'per_periode';
    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'bulan' => now()->month,
            'tahun' => now()->year,
            'status_pembayaran' => 'semua',
            'member_id' => null,
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->statePath('data')
            ->schema([
                Grid::make(['default' => 1, 'md' => 3])
                    ->schema([
                        Select::make('bulan')
                            ->label('Periode Iuran')
                            ->options(fn() => collect(range(1, 12))->mapWithKeys(fn($m) => [$m => Carbon::create(null, $m)->translatedFormat('F')]))
                            ->live()
                            ->required()
                            ->visible(fn() => $this->activeTab === 'per_periode'),

                        Select::make('tahun')
                            ->label('Tahun')
                            ->options(function () {
                                $years = range(now()->year + 1, 2020);
                                return array_combine($years, $years);
                            })
                            ->live()
                            ->required()
                            ->visible(fn() => $this->activeTab === 'per_periode'),

                        Select::make('status_pembayaran')
                            ->label('Filter Status')
                            ->options([
                                'semua' => 'Semua',
                                'sudah_bayar' => 'Sudah Bayar',
                                'belum_bayar' => 'Belum Bayar',
                            ])
                            ->live()
                            ->required()
                            ->visible(fn() => $this->activeTab === 'per_periode'),

                        Select::make('member_id')
                            ->label('Pilih Anggota')
                            ->options(Member::pluck('name', 'id'))
                            ->searchable()
                            ->live()
                            ->placeholder('Cari nama anggota...')
                            ->visible(fn() => $this->activeTab === 'per_anggota'),
                    ]),
            ]);
    }

    public function setMode(string $mode): void
    {
        $this->activeTab = $mode;
    }


    protected function getHeaderWidgetsData(): array
    {
        return $this->activeTab === 'per_periode' ? ['reportData' => $this->periodReport->all()] : [];
    }

    // =========================================================================
    // [DIUBAH] Fungsi getPeriodReportProperty() dengan Filter Keaktifan
    // =========================================================================
    public function getPeriodReportProperty(): Collection // <-- Tipe data return sekarang sudah benar
    {
        $formData = $this->form->getState();
        $bulan = $formData['bulan'];
        $tahun = $formData['tahun'];
        $statusFilter = $formData['status_pembayaran'];

        // Membuat tanggal batas akhir dari periode yang dipilih
        $tanggalBatas = Carbon::create($tahun, $bulan)->endOfMonth();

        // 1. Mengambil anggota yang sudah aktif pada atau sebelum tanggal batas
        $members = Member::whereDate('tanggal_aktif', '<=', $tanggalBatas)
                         ->orderBy('name')
                         ->get();

        // 2. Memeriksa status pembayaran untuk anggota yang relevan saja
        $report = $members->map(function ($member) use ($bulan, $tahun) {
            $sudahBayar = DuesTransaction::where('member_id', $member->id)
                ->where('period_month', $bulan)
                ->where('period_year', $tahun)
                ->exists();

            return ['nama' => $member->name, 'nia' => $member->nia, 'status' => $sudahBayar ? 'Sudah Bayar' : 'Belum Bayar'];
        });

        // 3. Menerapkan filter status (Sudah/Belum Bayar) jika ada
        if ($statusFilter === 'sudah_bayar') {
            return $report->where('status', 'Sudah Bayar');
        }
        if ($statusFilter === 'belum_bayar') {
            return $report->where('status', 'Belum Bayar');
        }
        
        return $report;
    }
    // =========================================================================

    public function getMemberReportProperty(): Collection // <-- Tipe data return sekarang sudah benar
    {
        $memberId = $this->form->getState()['member_id'];
        if (!$memberId) return collect();

        $member = Member::find($memberId);
        if (!$member || !$member->tanggal_aktif) return collect();

        $paidDues = DuesTransaction::where('member_id', $memberId)->where('type', 'masuk')->get()->keyBy(fn($item) => "{$item->period_year}-{$item->period_month}");

        $report = [];
        for ($date = $member->tanggal_aktif->copy()->startOfMonth(); $date->lte(now()->startOfMonth()); $date->addMonth()) {
            $periodKey = "{$date->year}-{$date->month}";
            $transaction = $paidDues->get($periodKey);
            $report[] = [
                'periode' => $date->translatedFormat('F Y'),
                'tanggal_bayar' => $transaction ? Carbon::parse($transaction->date)->translatedFormat('d M Y') : '-',
                'jumlah' => $transaction ? $transaction->amount : 0,
                'keterangan' => $transaction ? $transaction->description : 'Belum ada pembayaran',
                'status' => $transaction ? 'Sudah Bayar' : 'Belum Bayar',
            ];
        }
        return collect($report)->reverse();
    }

    public function getSelectedMemberProperty(): ?Member
    {
        $memberId = $this->form->getState()['member_id'];
        return $memberId ? Member::find($memberId) : null;
    }
}
