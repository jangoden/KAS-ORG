<x-filament-panels::page>
    {{-- Filter Tahun dan Bulan --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 p-4 bg-white dark:bg-gray-800 rounded-xl shadow">
        <div>
            <label for="year" class="block text-sm font-medium text-gray-700 dark:text-gray-200">
                Pilih Tahun
            </label>
            <select
                wire:model.live="selectedYear"
                id="year"
                class="block w-full mt-1 rounded-lg shadow-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 focus:border-primary-500 focus:ring-primary-500"
            >
                @for ($year = date('Y'); $year >= 2020; $year--)
                    <option value="{{ $year }}">{{ $year }}</option>
                @endfor
            </select>
        </div>

        <div>
            <label for="month" class="block text-sm font-medium text-gray-700 dark:text-gray-200">
                Pilih Bulan
            </label>
            <select
                wire:model.live="selectedMonth"
                id="month"
                class="block w-full mt-1 rounded-lg shadow-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 focus:border-primary-500 focus:ring-primary-500"
            >
                @foreach (range(1, 12) as $month)
                    <option value="{{ $month }}">
                        {{ \Carbon\Carbon::create(null, $month)->isoFormat('MMMM') }}
                    </option>
                @endforeach
            </select>
        </div>
    </div>

    {{-- Tabel Status Iuran Anggota --}}
    <div class="overflow-hidden bg-white dark:bg-gray-800 rounded-xl shadow mt-6">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
                        <th scope="col" class="px-6 py-3">No</th>
                        <th scope="col" class="px-6 py-3">Nama Anggota</th>
                        <th scope="col" class="px-6 py-3">NIA</th>
                        <th scope="col" class="px-6 py-3">Status Pembayaran</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($membersWithStatus as $member)
                        <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                            <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                {{ $loop->iteration }}
                            </td>
                            <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                {{ $member['name'] }}
                            </td>
                            <td class="px-6 py-4">
                                {{ $member['nia'] }}
                            </td>
                            <td class="px-6 py-4">
                                @if ($member['status'])
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-800/30 dark:text-green-300">
                                        Sudah Bayar
                                    </span>
                                @else
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800 dark:bg-red-800/30 dark:text-red-300">
                                        Belum Bayar
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                                Tidak ada data anggota untuk ditampilkan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-filament-panels::page>