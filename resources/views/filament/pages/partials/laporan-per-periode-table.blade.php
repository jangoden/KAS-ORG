<div class="overflow-x-auto rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
    <table class="fi-table w-full min-w-full table-auto divide-y divide-gray-200 text-sm dark:divide-gray-700">
        <thead class="bg-gray-50 dark:bg-gray-800">
            <tr>
                <th class="fi-table-header-cell px-3 py-3.5 sm:px-6 text-left w-12"><span class="text-sm font-semibold text-gray-950 dark:text-white">No.</span></th>
                <th class="fi-table-header-cell px-3 py-3.5 sm:px-6 text-left"><span class="text-sm font-semibold text-gray-950 dark:text-white">Anggota</span></th>
                <th class="fi-table-header-cell px-3 py-3.5 sm:px-6 text-left"><span class="text-sm font-semibold text-gray-950 dark:text-white">Status</span></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200 whitespace-nowrap dark:divide-gray-700">
            @forelse ($reportData as $data)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/40">
                    <td class="fi-table-cell px-3 py-4 sm:px-6">{{ $loop->iteration }}</td>
                    <td class="fi-table-cell px-3 py-4 sm:px-6">
                        <div class="flex flex-col">
                            <span class="font-semibold text-gray-950 dark:text-white">{{ $data['nama'] }}</span>
                            <span class="text-xs text-gray-500 dark:text-gray-400">NIA: {{ $data['nia'] ?? 'N/A' }}</span>
                        </div>
                    </td>
                    <td class="fi-table-cell px-3 py-4 sm:px-6">
                        <x-filament::badge :color="$data['status'] === 'Sudah Bayar' ? 'success' : 'danger'">
                            {{ $data['status'] }}
                        </x-filament::badge>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" class="px-6 py-12 text-center">
                        <span class="text-gray-500">Tidak ada data untuk ditampilkan.</span>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>