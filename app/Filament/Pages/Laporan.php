<?php

namespace App\Filament\Pages;

use App\Exports\TransactionsExport;
use App\Models\DuesTransaction;
use App\Models\Member;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Blade;
use Maatwebsite\Excel\Facades\Excel;

class Laporan extends Page implements Tables\Contracts\HasTable
{
    use Tables\Concerns\InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-document-chart-bar';
    protected static string $view            = 'filament.pages.laporan';
    protected static ?string $title = 'Laporan Kas';
    protected static ?string $navigationGroup = 'Laporan';

    public ?array $data = [];
    public array $periodSummary = [];
    public array $memberSummary = [];

    public static function canAccess(): bool
    {
        return auth()->user()->can('page_Laporan');
    }

    public function member()
    {
        return $this->belongsTo(\App\Models\Member::class);
    }

    public function mount(): void
    {
        $this->form->fill([
            'filterType'  => 'periode',
            'filterMode'  => 'year',
            'filterYear'  => now()->year,
            'filterMonth' => now()->month,
            'startDate'   => now()->startOfYear()->format('Y-m-d'),
            'endDate'     => now()->format('Y-m-d'),
            'member_id'   => null,
        ]);
        $this->recalculateData();
    }
    
    public function form(Form $form): Form
    {
        return $form
            ->statePath('data')
            ->schema([
                Radio::make('filterType')
                    ->label('Filter Laporan Berdasarkan')
                    ->options(['periode' => 'Periode', 'anggota' => 'Anggota'])
                    ->live()
                    ->inline()
                    ->default('periode'),
                
                Grid::make(3)
                    ->schema([
                        Select::make('filterMode')->label('Mode Waktu')
                            ->options(['year' => 'Tahunan', 'month' => 'Bulanan', 'range' => 'Rentang Tanggal'])
                            ->live(),
                        Select::make('filterMonth')->label('Bulan')
                            ->options(fn() => collect(range(1, 12))->mapWithKeys(fn($m) => [$m => Carbon::create(null, $m)->translatedFormat('F')]))
                            ->visible(fn ($get) => $get('filterMode') === 'month')->live(),
                        Select::make('filterYear')->label('Tahun')
                            ->options(fn() => array_combine($years = range(now()->year + 1, 2020), $years))
                            ->visible(fn ($get) => in_array($get('filterMode'), ['year', 'month']))->live(),
                        DatePicker::make('startDate')->label('Dari Tanggal')
                            ->visible(fn ($get) => $get('filterMode') === 'range')->live(),
                        DatePicker::make('endDate')->label('Sampai Tanggal')
                            ->visible(fn ($get) => $get('filterMode') === 'range')->live(),
                    ])
                    ->visible(fn ($get) => $get('filterType') === 'periode'),
                
                Select::make('member_id')->label('Pilih Anggota')
                    ->options(Member::pluck('name', 'id'))
                    ->searchable()
                    ->live()
                    ->visible(fn ($get) => $get('filterType') === 'anggota'),
            ]);
    }
    
    public function updated($name, $value): void
    {
        if (str_starts_with($name, 'data.')) {
            $this->recalculateData();
        }
    }

    public function recalculateData(): void
    {
        $filterType = $this->form->getState()['filterType'];
        if ($filterType === 'periode') {
            $this->calculatePeriodSummary();
        } elseif ($filterType === 'anggota') {
            $this->calculateMemberSummary();
        }
    }
    
    public function calculatePeriodSummary(): void
    {
        $query = $this->getFilteredQuery();
        $income  = (clone $query)->where('type', 'masuk')->sum('amount');
        $expense = (clone $query)->where('type', 'keluar')->sum('amount');
        $this->periodSummary = ['total_income'  => $income, 'total_expense' => $expense, 'balance' => $income - $expense];
    }
    
    public function calculateMemberSummary(): void
    {
        $memberId = $this->form->getState()['member_id'];
        if (!$memberId) {
            $this->memberSummary = ['total_paid' => 0, 'months_paid' => 0, 'months_due' => 0];
            return;
        }

        $member = Member::find($memberId);
        $totalPaid = DuesTransaction::where('member_id', $memberId)->where('type', 'masuk')->sum('amount');
        $monthsPaid = DuesTransaction::where('member_id', $memberId)->where('type', 'masuk')->count();

        $monthsSinceActive = 0;
        if ($member?->tanggal_aktif) {
            $startPeriod = $member->tanggal_aktif->copy()->startOfMonth();
            $endPeriod = now()->startOfMonth();
            $monthsSinceActive = $startPeriod->diffInMonths($endPeriod) + 1;
        }
        
        $monthsDue = $monthsSinceActive - $monthsPaid;

        $this->memberSummary = [
            'total_paid' => $totalPaid,
            'months_paid' => $monthsPaid,
            'months_due' => max(0, (int) $monthsDue),
        ];
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(fn(): Builder => $this->getFilteredQuery())
            ->columns([
                TextColumn::make('date')->label('Tanggal')->date('d/m/Y')->sortable(),
                TextColumn::make('type')
                    ->label('Jenis')
                    ->badge()
                    ->colors(['success' => 'masuk', 'danger'  => 'keluar'])
                    ->formatStateUsing(fn($state) => $state === 'masuk' ? 'Pemasukan' : 'Pengeluaran'),
                TextColumn::make('amount')->label('Jumlah')->money('IDR')->sortable(),
                TextColumn::make('id')
                    ->label('Anggota / Penerima')
                    ->formatStateUsing(function (DuesTransaction $record) {
                        if ($record->type === 'masuk') {
                            return $record->member?->name;
                        }
                        return $record->recipient_name;
                    })
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        return $query->where(function ($q) use ($search) {
                            $q->whereHas('member', fn ($subQuery) => $subQuery->where('name', 'like', "%{$search}%"))
                                ->orWhere('recipient_name', 'like', "%{$search}%");
                        });
                    })
                    ->visible(fn() => $this->form->getState()['filterType'] === 'periode')
                    ->wrap(),
                TextColumn::make('description')->label('Keterangan')->wrap(),
            ])
            ->defaultSort('date', 'desc')
            ->paginated();
    }

    protected function getFilteredQuery(): Builder
    {
        $formData = $this->form->getState();
        $query = DuesTransaction::query()
            ->select('dues_transactions.*')
            ->with('member');

        if ($formData['filterType'] === 'anggota' && !empty($formData['member_id'])) {
            return $query->where('member_id', $formData['member_id']);
        }

        if ($formData['filterType'] === 'periode') {
            $mode = $formData['filterMode'] ?? 'year';
            if ($mode === 'month' && !empty($formData['filterMonth']) && !empty($formData['filterYear'])) {
                $month = $formData['filterMonth'];
                $year = $formData['filterYear'];
                return $query->where(function (Builder $q) use ($month, $year) {
                    $q->where(fn(Builder $subQ) => $subQ->where('type', 'masuk')->where('period_month', $month)->where('period_year', $year))
                    ->orWhere(fn(Builder $subQ) => $subQ->where('type', 'keluar')->whereYear('date', $year)->whereMonth('date', $month));
                });
            } elseif ($mode === 'year' && !empty($formData['filterYear'])) {
                $year = $formData['filterYear'];
                return $query->where(function (Builder $q) use ($year) {
                    $q->where(fn(Builder $subQ) => $subQ->where('type', 'masuk')->where('period_year', $year))
                    ->orWhere(fn(Builder $subQ) => $subQ->where('type', 'keluar')->whereYear('date', $year));
                });
            } elseif ($mode === 'range' && !empty($formData['startDate']) && !empty($formData['endDate'])) {
                return $query->whereBetween('date', [Carbon::parse($formData['startDate'])->startOfDay(), Carbon::parse($formData['endDate'])->endOfDay()]);
            }
        }
        
        if ($formData['filterType'] === 'anggota') {
            return $query->whereRaw('1 = 0');
        }

        return $query;
    }
    
    protected function getHeaderWidgets(): array
    {
        $filterType = $this->form->getState()['filterType'] ?? 'periode';
        return $filterType === 'periode' ? [\App\Filament\Widgets\KasBulananChart::class] : [];
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('exportPdf')->label('Export PDF')->color('success')->icon('heroicon-o-document-arrow-down')->action('exportToPdf'),
            Action::make('exportExcel')->label('Export Excel')->color('success')->icon('heroicon-o-table-cells')->action('exportToExcel'),
        ];
    }
    
    public function exportToPdf()
    {
        $formData = $this->form->getState();
        $viewName = 'exports.transaction-pdf';
        $data = [];
        $filename = 'laporan-kas-' . now()->format('Y-m-d') . '.pdf';

        // [BARU] Data untuk kop surat. Silakan sesuaikan dengan data organisasi Anda.
        $kopSuratData = [
            'logo_url' => public_path('images/logo.png'),
            'nama_organisasi' => 'PC IPNU KAB. CIAMIS',
            'alamat' => 'Jl. Raya Ciamis No.24, Dewasari, Kec. Cijeungjing, Kabupaten Ciamis, Jawa Barat 46271',
            'telepon' => '081380904271',
            'email' => 'ipnukabciamis@gmail.com',
        ];

        if ($formData['filterType'] === 'anggota' && !empty($formData['member_id'])) {
            $viewName = 'exports.member-kas-pdf';
            $member = Member::find($formData['member_id']);
            $data = [
                'member' => $member,
                'summary' => $this->memberSummary,
                'transactions' => $this->getFilteredQuery()->orderBy('date', 'asc')->get(),
            ];
            $filename = 'laporan-anggota-' . str($member->name)->slug() . '-' . now()->format('Y-m-d') . '.pdf';

        } else {
            $transactions = $this->getFilteredQuery()->get();
            $groupedTransactions = $transactions->groupBy(function ($transaction) {
                if ($transaction->type === 'masuk' && $transaction->period_year && $transaction->period_month) {
                    return $transaction->period_year . '-' . str_pad($transaction->period_month, 2, '0', STR_PAD_LEFT);
                } else {
                    return Carbon::parse($transaction->date)->format('Y-m');
                }
            })
            ->sortBy(fn($group, $key) => $key)
            ->map(function ($monthlyTransactions) {
                return [
                    'transactions'     => $monthlyTransactions->sortBy('date'),
                    'subtotal_income'  => $monthlyTransactions->where('type', 'masuk')->sum('amount'),
                    'subtotal_expense' => $monthlyTransactions->where('type', 'keluar')->sum('amount'),
                ];
            });
            
            $data = [
                'summary'             => $this->periodSummary,
                'title'               => 'LAPORAN KEUANGAN PC IPNU KAB. CIAMIS',
                'period'              => $this->getPeriodText(),
                'groupedTransactions' => $groupedTransactions,
            ];
        }

        // [MODIFIKASI] Gabungkan data laporan dengan data kop surat
        $finalData = array_merge($data, $kopSuratData);

        $pdf = Pdf::loadHTML(Blade::render($viewName, $finalData));
        return response()->streamDownload(fn() => print($pdf->output()), $filename);
    }

    public function exportToExcel()
    {
        $formData = $this->form->getState();
        $title = 'Laporan Kas ';
        $filename = 'laporan-kas-' . now()->format('Y-m-d') . '.xlsx';

        if ($formData['filterType'] === 'anggota') {
            $memberName = Member::find($formData['member_id'])->name ?? 'anggota';
            $title = 'Laporan Iuran Anggota';
            $filename = 'laporan-anggota-' . str($memberName)->slug() . '-' . now()->format('Y-m-d') . '.xlsx';
        }

        $exportClass = new TransactionsExport($this->getFilteredQuery(), $this->getPeriodText(), $title);
        
        return Excel::download($exportClass, $filename);
    }

    protected function getPeriodText(): string
    {
        $formData = $this->form->getState();
        if ($formData['filterType'] === 'anggota' && !empty($formData['member_id'])) {
            $member = Member::find($formData['member_id']);
            return 'Anggota: ' . ($member->name ?? 'Tidak Ditemukan');
        }
        
        $mode = $formData['filterMode'] ?? 'year';
        if ($mode === 'range' && !empty($formData['startDate']) && !empty($formData['endDate'])) {
            return Carbon::parse($formData['startDate'])->translatedFormat('d F Y') . ' - ' . Carbon::parse($formData['endDate'])->translatedFormat('d F Y');
        } elseif ($mode === 'month' && !empty($formData['filterMonth']) && !empty($formData['filterYear'])) {
            return Carbon::create()->month((int) $formData['filterMonth'])->translatedFormat('F') . ' ' . $formData['filterYear'];
        } elseif ($mode === 'year' && !empty($formData['filterYear'])) {
            return 'Tahun ' . $formData['filterYear'];
        }
        return 'Semua Periode';
    }
}
