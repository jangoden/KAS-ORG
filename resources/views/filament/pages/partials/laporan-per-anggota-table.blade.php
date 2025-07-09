<div class="overflow-x-auto rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
    <table class="fi-table w-full min-w-full table-auto divide-y divide-gray-200 text-sm dark:divide-gray-700">
        <thead class="bg-gray-50 dark:bg-gray-800">
            <tr>
                <th class="fi-table-header-cell px-3 py-3.5 sm:px-6 text-left"><span class="text-sm font-semibold text-gray-950 dark:text-white">Periode</span></th>
                <th class="fi-table-header-cell px-3 py-3.5 sm:px-6 text-left"><span class="text-sm font-semibold text-gray-950 dark:text-white">Tanggal Bayar</span></th>
                <th class="fi-table-header-cell px-3 py-3.5 sm:px-6 text-left"><span class="text-sm font-semibold text-gray-950 dark:text-white">Jumlah</span></th>
                <th class="fi-table-header-cell px-3 py-3.5 sm:px-6 text-left"><span class="text-sm font-semibold text-gray-950 dark:text-white">Status</span></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200 whitespace-nowrap dark:divide-gray-700">
            @forelse ($memberReport as $data)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/40">
                    <td class="fi-table-cell px-3 py-4 sm:px-6 font-semibold text-gray-950 dark:text-white">{{ $data['periode'] }}</td>
                    <td class="fi-table-cell px-3 py-4 sm:px-6">{{ $data['tanggal_bayar'] }}</td>
                    <td class="fi-table-cell px-3 py-4 sm:px-6">Rp {{ number_format($data['jumlah'], 0, ',', '.') }}</td>
                    <td class="fi-table-cell px-3 py-4 sm:px-6">
                        <x-filament::badge :color="$data['status'] === 'Sudah Bayar' ? 'success' : 'danger'">
                            {{ $data['status'] }}
                        </x-filament::badge>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="px-6 py-12 text-center">
                        <span class="text-gray-500">Anggota ini belum memiliki riwayat transaksi.</span>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>