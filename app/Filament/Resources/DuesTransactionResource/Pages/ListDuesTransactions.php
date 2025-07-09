<?php

namespace App\Filament\Resources\DuesTransactionResource\Pages;

use App\Filament\Resources\DuesTransactionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

// --- TAMBAHKAN USE STATEMENT INI ---
use App\Filament\Widgets\DuesStatsWidget;

class ListDuesTransactions extends ListRecords
{
    protected static string $resource = DuesTransactionResource::class;

    // --- TAMBAHKAN METHOD INI UNTUK MENAMPILKAN WIDGET ---
    protected function getHeaderWidgets(): array
    {
        return [
            DuesStatsWidget::class,
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
