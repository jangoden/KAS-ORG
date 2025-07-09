<?php

namespace App\Filament\Resources\DuesTransactionResource\Pages;

use App\Filament\Resources\DuesTransactionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDuesTransaction extends EditRecord
{
    protected static string $resource = DuesTransactionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
