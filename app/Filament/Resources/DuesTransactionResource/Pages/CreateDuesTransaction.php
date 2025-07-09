<?php

namespace App\Filament\Resources\DuesTransactionResource\Pages;

use App\Filament\Resources\DuesTransactionResource;
use Filament\Resources\Pages\CreateRecord;

class CreateDuesTransaction extends CreateRecord
{
    protected static string $resource = DuesTransactionResource::class;

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }
}
