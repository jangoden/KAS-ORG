<?php
namespace App\Http\Livewire;

use Livewire\Component;
use Filament\Tables\Table;
use Illuminate\Support\Carbon;
use App\Models\DuesTransaction;
use Illuminate\Contracts\View\View;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Tables\Concerns\InteractsWithTable;

class LaporanDuesTable extends Component implements HasForms, HasTable
{
    use InteractsWithTable;
    use InteractsWithForms;
    public ?string $startDate = null;
    public ?string $endDate   = null;
    public ?string $type      = null;


    public function table(Table $table) : Table
    {
        return $table
            ->query(fn(): Builder => DuesTransaction::query()
                    ->when($this->startDate && $this->endDate, fn($q) => $q->whereBetween('date', [
                        Carbon::parse($this->startDate),
                        Carbon::parse($this->endDate),
                    ]))
                    ->when($this->type, fn($q) => $q->where('type', $this->type === 'pemasukan' ? 'masuk' : 'keluar'))
            )
            ->columns([
                TextColumn::make('date')->label('Tanggal')->date('d/m/Y')->sortable(),
                TextColumn::make('type')->label('Jenis')->badge()->colors([
                    'success' => 'masuk',
                    'danger'  => 'keluar',
                ])->formatStateUsing(fn($state) => $state === 'masuk' ? 'Pemasukan' : 'Pengeluaran'),
                TextColumn::make('amount')->label('Jumlah')->money('IDR')->sortable(),
                TextColumn::make('description')->label('Keterangan')->wrap(),
            ])
            ->defaultSort('date', 'desc')
            ->paginated();
    }

    public function render(): View
    {
        return view('livewire.laporan-dues-table');
    }
}
