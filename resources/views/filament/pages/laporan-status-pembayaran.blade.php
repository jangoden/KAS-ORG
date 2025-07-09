<x-filament::page>

    {{-- Navigasi Tab untuk memilih mode laporan --}}
    <div class="mb-4 border-b border-gray-200 dark:border-gray-700">
        <nav class="flex -mb-px space-x-6" aria-label="Tabs">
            <button
                wire:click="setMode('per_periode')"
                @class([
                    'px-3 py-3 font-medium text-sm border-b-2',
                    'border-primary-500 text-primary-600 dark:text-primary-400' => $activeTab === 'per_periode',
                    'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-200' => $activeTab !== 'per_periode',
                ])>
                Status per Periode
            </button>
            <button
                wire:click="setMode('per_anggota')"
                @class([
                    'px-3 py-3 font-medium text-sm border-b-2',
                    'border-primary-500 text-primary-600 dark:text-primary-400' => $activeTab === 'per_anggota',
                    'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-200' => $activeTab !== 'per_anggota',
                ])>
                Status per Anggota
            </button>
        </nav>
    </div>

    {{-- Filter akan dirender di sini --}}
    <div class="mb-6">
        {{ $this->form }}
    </div>

    {{-- Konten dinamis berdasarkan Tab yang aktif --}}
    <div>
        @if ($activeTab === 'per_periode')
            {{-- Tabel untuk Laporan per Periode --}}
            @include('filament.pages.partials.laporan-per-periode-table', ['reportData' => $this->periodReport])

        @elseif ($activeTab === 'per_anggota')
            @php
                $selectedMember = $this->selectedMember;
                $memberReport = $this->memberReport;
            @endphp

            @if ($selectedMember)
                {{-- Kartu Info Anggota --}}
                <div class="p-6 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 mb-8">
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white">{{ $selectedMember->name }}</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        NIA: {{ $selectedMember->nia }} | Aktif Sejak: {{ $selectedMember->tanggal_aktif->translatedFormat('d F Y') }}
                    </p>
                </div>

                {{-- Tabel untuk Laporan per Anggota --}}
                @include('filament.pages.partials.laporan-per-anggota-table', ['memberReport' => $memberReport])
            @else
                {{-- Pesan jika belum ada anggota yang dipilih --}}
                <div class="flex items-center justify-center p-12 text-center bg-white dark:bg-gray-800 rounded-lg shadow-sm">
                    <div>
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5M7.188 2.239l.777 2.897M5.136 7.965l-2.898-.777M13.95 4.05l-2.122 2.122m-5.657 5.656l-2.12 2.122" />
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">Pilih Anggota</h3>
                        <p class="mt-1 text-sm text-gray-500">Silakan pilih anggota untuk melihat riwayat iurannya.</p>
                    </div>
                </div>
            @endif
        @endif
    </div>

</x-filament::page>
