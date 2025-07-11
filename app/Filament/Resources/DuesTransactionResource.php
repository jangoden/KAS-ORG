<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DuesTransactionResource\Pages;
use App\Models\DuesTransaction;
use App\Models\Member;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions; // <-- [DITAMBAHKAN] Untuk Izin Kustom
use Carbon\Carbon;
use Closure;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action; // <-- [DITAMBAHKAN] Untuk Tombol Aksi Kustom
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class DuesTransactionResource extends Resource implements HasShieldPermissions // <-- [DIUBAH] Menambahkan 'implements'
{
    protected static ?string $model = DuesTransaction::class;
    protected static ?string $navigationIcon = 'heroicon-o-book-open';

    // Pengaturan Sidebar
    protected static ?string $navigationLabel = 'Transaksi Kas';
    protected static ?string $navigationGroup = 'Kas';
    protected static ?int $navigationSort = 1;

    // =========================================================================
    // [DIUBAH] PENGATURAN NAMA DAN URL UNTUK RESOURCE
    // =========================================================================
    protected static ?string $modelLabel = 'Transaksi Kas';
    protected static ?string $pluralModelLabel = 'Transaksi Kas';
    protected static ?string $slug = 'transaksi-kas';
    // =========================================================================

    // =========================================================================
    // [DITAMBAHKAN] FUNGSI UNTUK MENDAFTARKAN IZIN KUSTOM "PRINT"
    // =========================================================================
    public static function getPermissionPrefixes(): array
    {
        return [
            'view',
            'view_any',
            'create',
            'update',
            'restore',
            'restore_any',
            'replicate',
            'reorder',
            'delete',
            'delete_any',
            'force_delete',
            'force_delete_any',
            'print',
        ];
    }
    // =========================================================================

    // =========================================================================
    // FUNGSI MENGHITUNG SALDO KAS SAAT INI
    // =========================================================================
    /**
     * Menghitung saldo kas wajib saat ini.
     *
     * @return float Saldo kas terkini.
     */
    private static function getCurrentBalance(): float
    {
        $kasMasuk = DuesTransaction::where('type', 'masuk')->sum('amount');
        $kasKeluar = DuesTransaction::where('type', 'keluar')->sum('amount');
        return (float) ($kasMasuk - $kasKeluar);
    }

    // =========================================================================
    // FUNGSI PENCARI TUNGGAKAN
    // =========================================================================
    /**
     * Mencari periode iuran pertama yang belum dibayar oleh anggota.
     *
     * @param int $memberId ID dari anggota yang akan dicek.
     * @return \Carbon\Carbon|null Mengembalikan objek Carbon dari bulan tunggakan, atau null jika lunas.
     */
    private static function getTunggakanPertama(int $memberId): ?Carbon
    {
        $member = Member::find($memberId);

        if (!$member || !$member->tanggal_aktif) {
            return null;
        }

        $periodeAwal = $member->tanggal_aktif->startOfMonth();
        $periodeSekarang = Carbon::now()->startOfMonth();

        $iuranTerbayar = DuesTransaction::where('member_id', $memberId)
            ->where('type', 'masuk')
            ->get()
            ->mapWithKeys(function ($item) {
                return ["{$item->period_year}-{$item->period_month}" => true];
            });

        for ($date = $periodeAwal; $date->lte($periodeSekarang); $date->addMonth()) {
            $periodeCek = "{$date->year}-{$date->month}";

            if (!isset($iuranTerbayar[$periodeCek])) {
                return $date;
            }
        }

        return null;
    }


    // =========================================================================
    // FORM DENGAN VALIDASI KONDISIONAL
    // =========================================================================
    public static function form(Form $form): Form
    {
        return $form->schema([
            Select::make('type')
                ->label('Jenis Transaksi')
                ->options([
                    'masuk' => 'Kas Masuk (Iuran)',
                    'keluar' => 'Kas Keluar (Pengeluaran)',
                ])
                ->required()
                ->live(),

            TextInput::make('recipient_name')
                ->label('Penerima / P. Jawab')
                ->required(fn ($get) => $get('type') === 'keluar') // Wajib diisi hanya jika tipe 'keluar'
                ->visible(fn ($get) => $get('type') === 'keluar') // Muncul hanya jika tipe 'keluar'
                ->maxLength(255)
                ->placeholder('Contoh: Toko ATK Berkah, Budi (Panitia)'),

            // Form untuk KAS MASUK (IURAN)
            Select::make('member_id')
                ->label('Anggota')
                ->relationship('member', 'name')
                ->searchable()
                ->required()
                ->live()
                ->afterStateUpdated(function (Set $set, ?string $state) {
                    if (empty($state)) {
                        return;
                    }

                    $tunggakan = self::getTunggakanPertama((int) $state);

                    if ($tunggakan) {
                        $set('period_month', $tunggakan->month);
                        $set('period_year', $tunggakan->year);
                        $member = Member::find($state);
                        $namaBulan = $tunggakan->translatedFormat('F');
                        $set('description', "Kas {$member->name} periode {$namaBulan} {$tunggakan->year}");
                    } else {
                        $set('period_month', null);
                        $set('period_year', null);
                        $set('description', null);
                        Notification::make()
                            ->title('Lunas!')
                            ->body('Anggota ini tidak memiliki tunggakan iuran.')
                            ->success()
                            ->send();
                    }
                })
                ->visible(fn ($get) => $get('type') === 'masuk'),

            Select::make('period_month')
                ->label('Untuk Bulan')
                ->options(function () {
                    $months = [];
                    for ($m = 1; $m <= 12; $m++) {
                        $months[$m] = Carbon::create(null, $m)->translatedFormat('F');
                    }
                    return $months;
                })
                ->required()
                ->disabled()
                ->dehydrated()
                ->visible(fn ($get) => $get('type') === 'masuk'),

            Select::make('period_year')
                ->label('Untuk Tahun')
                ->options(function () {
                    $years = [];
                    for ($y = date('Y') + 1; $y >= date('Y') - 5; $y--) {
                        $years[$y] = $y;
                    }
                    return $years;
                })
                ->required()
                ->disabled()
                ->dehydrated()
                ->visible(fn ($get) => $get('type') === 'masuk'),

            // Form untuk KAS KELUAR
            TextInput::make('amount')
                ->label('Jumlah (Nominal)')
                ->required()
                ->numeric()
                ->prefix('Rp')
                // Menggunakan 'rules' dengan closure untuk validasi kustom
                ->rules([
                    function ($get, $record) {
                        return function (string $attribute, $value, Closure $fail) use ($get, $record) {
                            if ($get('type') === 'keluar') {
                                $saldoSaatIni = self::getCurrentBalance();
                                if ($record) {
                                    $saldoSaatIni += $record->amount;
                                }
                                if ((float) $value > $saldoSaatIni) {
                                    $fail("Nominal pengeluaran tidak boleh melebihi saldo kas saat ini. Saldo tersedia: Rp " . number_format($saldoSaatIni, 0, ',', '.'));
                                }
                            }
                        };
                    }
                ])
                ->visible(fn ($get) => $get('type') === 'keluar'),

            Textarea::make('description')
                ->label('Keterangan')
                ->required(),

            DatePicker::make('date')
                ->label('Tanggal Transaksi')
                ->default(now())
                ->required(),
        ]);
    }

    // =========================================================================
    // MENGISI NOMINAL IURAN OTOMATIS
    // =========================================================================
    protected static function mutateFormDataBeforeCreate(array $data): array
    {
        if ($data['type'] === 'masuk') {
            $data['amount'] = 10000;
        }

        return $data;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('date')
                    ->label('Tanggal')
                    ->date('d-M-Y')
                    ->sortable(),

                TextColumn::make('member.name')
                    ->label('Anggota / Penerima') // Label diubah agar lebih umum
                    ->formatStateUsing(function ($state, DuesTransaction $record) {
                        if ($record->type === 'masuk') {
                            return $record->member?->name; // Jika kas masuk, tampilkan nama anggota
                        }
                        return $record->recipient_name; // Jika kas keluar, tampilkan nama penerima
                    })
                    ->default('-')
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        // Membuat pencarian bisa untuk nama anggota DAN nama penerima
                        return $query->where(function ($q) use ($search) {
                            $q->whereHas('member', fn ($subQuery) => $subQuery->where('name', 'like', "%{$search}%"))
                                ->orWhere('recipient_name', 'like', "%{$search}%");
                        });
                    }),

                TextColumn::make('description')
                    ->label('Keterangan')
                    ->searchable()->wrap(),

                BadgeColumn::make('type')
                    ->label('Jenis')
                    ->colors([
                        'success' => 'masuk',
                        'danger' => 'keluar',
                    ])
                    ->formatStateUsing(fn (string $state): string => $state === 'masuk' ? 'Kas Masuk' : 'Kas Keluar'),

                TextColumn::make('amount')
                    ->label('Jumlah')
                    ->money('IDR')
                    ->sortable(),
            ])
            ->defaultSort('date', 'desc')
            ->filters([])
            ->actions([
                Tables\Actions\EditAction::make(),
                Action::make('printReceipt')
                    ->label('Cetak Bukti')
                    ->icon('heroicon-o-printer')
                    ->color('success')
                    ->url(fn (DuesTransaction $record): string => route('dues.receipt.download', $record), shouldOpenInNewTab: true)
                    ->visible(fn (DuesTransaction $record) => $record->type === 'masuk')
                    ->hidden(fn () => !auth()->user()->can('print_dues::transaction')),


                Action::make('printExpenseReceipt')
                    ->label('Cetak Bukti')
                    ->icon('heroicon-o-printer')
                    ->color('warning') // Warna beda agar mudah dikenali
                    ->url(fn (DuesTransaction $record): string => route('expense.receipt.download', $record), shouldOpenInNewTab: true)
                    ->visible(fn (DuesTransaction $record) => $record->type === 'keluar'), // Hanya muncul untuk kas keluar

                Tables\Actions\DeleteAction::make(),
            ])
            ->actionsColumnLabel('Aksi') // <-- [DITAMBAHKAN] Untuk memberi judul pada kolom

            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    // =========================================================================
    // [DITAMBAHKAN] MEMODIFIKASI QUERY BERDASARKAN HAK AKSES PENGGUNA
    // =========================================================================
    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        // Jika yang login adalah anggota, tampilkan hanya data iurannya sendiri
        if (auth()->user()?->hasRole('anggota') && auth()->user()->member_id) {
            $query->where('member_id', auth()->user()->member_id);
        }

        return $query;
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListDuesTransactions::route('/'),
            'create' => Pages\CreateDuesTransaction::route('/create'),
            'edit'   => Pages\EditDuesTransaction::route('/{record}/edit'),
        ];
    }
}
