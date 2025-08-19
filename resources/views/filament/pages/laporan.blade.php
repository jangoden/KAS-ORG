<x-filament-panels::page>

    {{-- Filter dinamis akan dirender di sini --}}
    <div class="mb-6 p-4 bg-white rounded-lg shadow dark:bg-gray-800">
        {{ $this->form }}
    </div>

    {{-- Konten dinamis berdasarkan mode filter --}}
    @if ($this->data['filterType'] === 'periode')
        {{-- Tampilan untuk Mode Periode --}}
        {{-- [MODIFIKASI] Menggunakan Flexbox untuk layout horizontal responsif --}}
        <div class="flex flex-col md:flex-row gap-6 mb-6">
            <x-filament::section class="flex-1">
                <x-slot name="heading">Total Pemasukan</x-slot>
                <p class="text-3xl font-bold text-green-600">Rp {{ number_format($periodSummary['total_income'] ?? 0, 0, ',', '.') }}</p>
            </x-filament::section>
            <x-filament::section class="flex-1">
                <x-slot name="heading">Total Pengeluaran</x-slot>
                <p class="text-3xl font-bold text-red-600">Rp {{ number_format($periodSummary['total_expense'] ?? 0, 0, ',', '.') }}</p>
            </x-filament::section>
            <x-filament::section class="flex-1">
                <x-slot name="heading">Saldo Akhir Periode</x-slot>
                <p class="text-3xl font-bold {{ ($periodSummary['balance'] ?? 0) >= 0 ? 'text-primary-600' : 'text-red-600' }}">
                    Rp {{ number_format($periodSummary['balance'] ?? 0, 0, ',', '.') }}
                </p>
            </x-filament::section>
        </div>

    @elseif ($this->data['filterType'] === 'anggota' && !empty($this->data['member_id']))
        {{-- Tampilan untuk Mode Anggota --}}
        {{-- [MODIFIKASI] Menggunakan Flexbox untuk layout horizontal responsif --}}
        <div class="flex flex-col md:flex-row gap-6 mb-6">
            <x-filament::section class="flex-1">
                <x-slot name="heading">Total Iuran Dibayar</x-slot>
                <p class="text-3xl font-bold text-green-600">Rp {{ number_format($memberSummary['total_paid'] ?? 0, 0, ',', '.') }}</p>
            </x-filament::section>
            <x-filament::section class="flex-1">
                <x-slot name="heading">Jumlah Bulan Lunas</x-slot>
                <p class="text-3xl font-bold text-primary-600">{{ $memberSummary['months_paid'] ?? 0 }} Bulan</p>
            </x-filament::section>
            <x-filament::section class="flex-1">
                <x-slot name="heading">Jumlah Tunggakan</x-slot>
                <p class="text-3xl font-bold text-red-600">{{ $memberSummary['months_due'] ?? 0 }} Bulan</p>
            </x-filament::section>
        </div>
    @endif
    
    {{-- Tabel data akan selalu ditampilkan --}}
    <div>
        {{ $this->table }}
    </div>

</x-filament-panels::page>