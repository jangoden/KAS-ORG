<?php

namespace App\Filament\Resources\DuesTransactionResource\Pages;

use App\Filament\Resources\DuesTransactionResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateDuesTransaction extends CreateRecord
{
    protected static string $resource = DuesTransactionResource::class;

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }

    protected function getCreateFormActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Buat'), // label tombol
        ];
    }
}
