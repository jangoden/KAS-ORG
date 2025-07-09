<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DuesValidationResource\Pages;
use App\Models\DuesTransaction;
use App\Models\Member;
use Carbon\Carbon;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Actions\Action;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class DuesValidationResource extends Resource
{
    // [DIUBAH] Menunjuk ke model Member sebagai sumber data utama
    protected static ?string $model = Member::class;

    // [DIUBAH] Pengaturan navigasi sidebar
    protected static ?string $navigationIcon = 'heroicon-o-check-badge';
    protected static ?string $navigationLabel = 'Validasi Iuran';
    protected static ?string $navigationGroup = 'Kas Wajib';
    protected static ?int $navigationSort = 3;
    protected static ?string $slug = 'dues-validation'; // URL kustom

    /**
     * Kita tidak memerlukan form Create/Edit, jadi kita hapus isinya.
     */
    public static function form(Form $form): Form
    {
        return $form->schema([]);
    }

    /**
     * [BAGIAN UTAMA] Mendefinisikan tabel, kolom, filter, dan aksi.
     */
    public static function table(Table $table): Table
    {
        return $table
            // Mengurutkan berdasarkan nama secara default
            ->defaultSort('name', 'asc')
            ->columns([
                TextColumn::make('name')
                    ->label('Nama Anggota')
                    ->searchable(),

                TextColumn::make('nia')
                    ->label('NIA')
                    ->searchable(),

                // [KOLOM KUSTOM] Menampilkan status pembayaran dengan logika
                TextColumn::make('status_pembayaran')
                    ->label('Status Pembayaran')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Sudah Bayar' => 'success',
                        'Belum Bayar' => 'danger',
                        default => 'gray',
                    })
                    ->getStateUsing(function (Model $record, Table $table) {
                        $livewire = $table->getLivewire();
                        $year = $livewire->tableFilters['year']['value'] ?? now()->year;
                        $month = $livewire->tableFilters['month']['value'] ?? now()->month;

                        if ($record->tanggal_aktif && Carbon::create($year, $month)->startOfMonth()->lt($record->tanggal_aktif->startOfMonth())) {
                            return 'Belum Wajib';
                        }
                        
                        $isPaid = DuesTransaction::where('member_id', $record->id)
                            ->where('period_year', $year)
                            ->where('period_month', $month)
                            ->where('type', 'masuk')
                            ->exists();

                        return $isPaid ? 'Sudah Bayar' : 'Belum Bayar';
                    }),
            ])
            ->filters([
                // [FILTER BARU] Filter interaktif untuk memilih tahun dan bulan
                SelectFilter::make('year')
                    ->label('Pilih Tahun')
                    ->options(fn () => collect(range(now()->year, 2020))->mapWithKeys(fn ($y) => [$y => $y]))
                    ->default(now()->year)
                    // [PERBAIKAN] Menambahkan query kosong agar tidak memfilter tabel members
                    ->query(fn (Builder $query): Builder => $query),

                SelectFilter::make('month')
                    ->label('Pilih Bulan')
                    ->options(fn () => collect(range(1, 12))->mapWithKeys(fn ($m) => [$m => Carbon::create(null, $m)->isoFormat('MMMM')]))
                    ->default(now()->month)
                    // [PERBAIKAN] Menambahkan query kosong agar tidak memfilter tabel members
                    ->query(fn (Builder $query): Builder => $query),

            ], layout: \Filament\Tables\Enums\FiltersLayout::AboveContent) // Posisi filter di atas tabel
            ->actions([
                // [AKSI INTERAKTIF] Tombol untuk melihat detail riwayat
                Action::make('detail')
                    ->label('Lihat Detail')
                    ->icon('heroicon-o-eye')
                    ->color('gray')
                    ->modalContent(function (Model $record) {
                        $riwayat = [];
                        if ($record->tanggal_aktif) {
                            $periodeAwal = $record->tanggal_aktif->startOfMonth();
                            $periodeAkhir = Carbon::now()->startOfMonth();
                            $iuranTerbayar = DuesTransaction::where('member_id', $record->id)
                                ->where('type', 'masuk')
                                ->get()
                                ->mapWithKeys(fn ($item) => ["{$item->period_year}-{$item->period_month}" => true]);
                            for ($date = $periodeAwal; $date->lte($periodeAkhir); $date->addMonth()) {
                                $riwayat[] = [
                                    'periode' => $date->translatedFormat('F Y'),
                                    'status'  => isset($iuranTerbayar["{$date->year}-{$date->month}"]),
                                ];
                            }
                            $riwayat = array_reverse($riwayat);
                        } else {
                            $riwayat[] = ['periode' => 'Anggota belum memiliki tanggal aktif', 'status' => 'error'];
                        }
                        return view('filament.modals.riwayat-pembayaran', ['riwayat' => $riwayat]);
                    })
                    ->modalHeading(fn (Model $record) => 'Riwayat Iuran: ' . $record->name)
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Tutup'),
            ])
            // Menghapus aksi bawaan create/edit/delete
            ->bulkActions([]);
    }

    /**
     * Memastikan resource ini hanya memiliki halaman daftar (index).
     */
    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageDuesValidations::route('/'),
        ];
    }
}
