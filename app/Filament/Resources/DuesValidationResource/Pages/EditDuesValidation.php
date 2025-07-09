<?php

namespace App\Filament\Resources\DuesValidationResource\Pages;

use App\Filament\Resources\DuesValidationResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDuesValidation extends EditRecord
{
    protected static string $resource = DuesValidationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
