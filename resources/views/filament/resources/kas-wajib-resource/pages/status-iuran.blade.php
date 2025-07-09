<x-filament-panels::page>

    {{-- Ini akan menampilkan filter yang kita buat di file PHP --}}
    <x-filament-panels::form wire:submit.prevent>
        {{ $this->form }}
    </x-filament-panels::form>

    {{-- Ini akan menampilkan tabel yang kita buat di file PHP --}}
    {{ $this->table }}

</x-filament-panels::page>