@props([
    'logoUrl',
    'namaOrganisasi',
    'alamat',
    'telepon',
    'email'
])

<div class="kop-surat" style="width: 100%; border-bottom: 2px solid black; padding-bottom: 10px; margin-bottom: 20px;">
    <table style="width: 100%;">
        <tr>
            <td style="width: 20%; text-align: center;">
                {{-- Cek jika file logo ada, jika tidak, jangan tampilkan gambar --}}
                @if(file_exists($logoUrl))
                    <img src="{{ $logoUrl }}" alt="Logo" style="width: 80px; height: auto;">
                @endif
            </td>
            <td style="width: 80%; text-align: center;">
                <h1 style="margin: 0; font-size: 18px; font-weight: bold;">{{ $namaOrganisasi }}</h1>
                <p style="margin: 2px 0; font-size: 12px;">{{ $alamat }}</p>
                <p style="margin: 2px 0; font-size: 12px;">Telp: {{ $telepon }} | Email: {{ $email }}</p>
            </td>
        </tr>
    </table>
</div>
