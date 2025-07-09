<?php

namespace App\Filament\Resources\DuesValidationResource\Pages;

use App\Filament\Resources\DuesValidationResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDuesValidations extends ListRecords
{
    protected static string $resource = DuesValidationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
