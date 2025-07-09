<?php
namespace App\Filament\Resources;

use App\Filament\Resources\MemberResource\Pages;
use App\Models\Member;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\DatePicker; // Jangan lupa import
use Filament\Tables\Columns\TextColumn;



class MemberResource extends Resource
{
    protected static ?string $model = Member::class;

    protected static ?string $navigationIcon  = 'heroicon-o-users';
    protected static ?string $navigationGroup = 'Kas';
    protected static ?string $navigationLabel = 'Data Anggota';
    protected static ?int $navigationSort     = 2;
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')
                ->required()
                ->maxLength(255),

            Forms\Components\TextInput::make('nia')
                ->label('Nomor Induk Anggota (NIA)')
                ->required()
                ->unique(ignoreRecord: true)
                ->maxLength(255),

                // add date picker for tanggal_aktif
                DatePicker::make('tanggal_aktif')
                ->label('Tanggal Aktif Keanggotaan')
                ->required()
                ->native(false) // Menggunakan datepicker yang lebih modern
                ->displayFormat('d/m/Y'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable(),

                Tables\Columns\TextColumn::make('nia')
                    ->label('NIA')
                    ->searchable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('tanggal_aktif')
                ->label('Anggota Aktif Sejak')
                ->date('d F Y') // Format tanggal agar mudah dibaca (cth: 26 Juni 2025)
                ->sortable()
                ->placeholder('Belum diatur'), // Teks jika tanggalnya kosong
            ])
            ->filters([
                // Tambahkan filter jika diperlukan
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            // Tambahkan relasi jika ada
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListMembers::route('/'),
            'create' => Pages\CreateMember::route('/create'),
            'edit'   => Pages\EditMember::route('/{record}/edit'),
        ];
    }
}
