<x-app-layout>
    <x-slot name="header">
        <p class="text-xs font-semibold tracking-widest text-hitam2 uppercase mb-1">Beranda</p>
        <h1 class="font-display text-2xl font-semibold">Ringkasan Platform</h1>
    </x-slot>

    <!-- Stat Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <div class="bg-putih rounded-2xl shadow-sm p-5 border border-border">
            <div class="w-10 h-10 rounded-xl bg-periwinkle/60 flex items-center justify-center mb-4">
                 <img src="{{ asset('images/school.svg') }}" alt="school" class="w-5 h-5">
            </div>
            <p class="text-3xl font-display font-semibold">{{ $stats['sekolah_terverifikasi'] }}</p>
            <p class="text-sm text-cream3 mt-1">Sekolah Terverifikasi</p>
        </div>

        <div class="bg-putih rounded-2xl shadow-sm p-5 border border-border">
            <div class="w-10 h-10 rounded-xl bg-coral/60 flex items-center justify-center mb-4">
                <img src="{{ asset('images/time.svg') }}" alt="clock" class="w-5 h-5">
            </div>
            <p class="text-3xl font-display font-semibold">{{ $stats['menunggu_verifikasi'] }}</p>
            <p class="text-sm text-cream3 mt-1">Menunggu Verifikasi</p>
        </div>

        <div class="bg-putih rounded-2xl shadow-sm p-5 border border-border">
            <div class="w-10 h-10 rounded-xl bg-amber/60 flex items-center justify-center mb-4">
                <img src="{{ asset('images/document.svg') }}" alt="document" class="w-5 h-5">
            </div>
            <p class="text-3xl font-display font-semibold">{{ $stats['mata_pelajaran'] }}</p>
            <p class="text-sm text-cream3 mt-1">Mata Pelajaran Global</p>
        </div>

        <div class="bg-putih rounded-2xl shadow-sm p-5 border border-border">
            <div class="w-10 h-10 rounded-xl bg-teal/60 flex items-center justify-center mb-4">
                <img src="{{ asset('images/users.svg') }}" alt="users" class="w-5 h-5">
            </div>
            <p class="text-3xl font-display font-semibold">{{ number_format($stats['total_pengguna']) }}</p>
            <p class="text-sm text-cream3 mt-1 font-semibold">Total Pengguna</p>
        </div>
    </div>

    <!-- Permintaan Registrasi Sekolah -->
    <h2 class="font-body text-md font-semibold mb-3">Permintaan Registrasi Sekolah</h2>
    <div class="bg-putih rounded-2xl shadow-sm overflow-hidden mb-8">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-border text-left text-[11px] tracking-widest uppercase text-hitam2">
                    <th class="px-5 py-3 font-semibold">Sekolah</th>
                    <th class="px-5 py-3 font-semibold">Kota/Kabupaten</th>
                    <th class="px-5 py-3 font-semibold">Diajukan</th>
                    <th class="px-5 py-3 font-semibold">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($permintaanRegistrasi as $sekolah)
                    <tr class="border-b border-border last:border-0">
                        <td class="px-5 py-4">{{ $sekolah->nama }}</td>
                        <td class="px-5 py-4 text-hitam2">{{ $sekolah->detail_alamat }}</td>
                        <td class="px-5 py-4 text-hitam2">{{ $sekolah->created_at->diffForHumans() }}</td>
                        <td class="px-5 py-4">
                            @if ($sekolah->status === \App\Models\Sekolah::STATUS_PENDING)
                                <span class="inline-block px-3 py-1 rounded-full text-xs font-medium bg-amber/60 text-hitam2">Menunggu</span>
                            @elseif ($sekolah->status === \App\Models\Sekolah::STATUS_VERIFIED)
                                <span class="inline-block px-3 py-1 rounded-full text-xs font-medium bg-teal/60 text-hitam2">Terverifikasi</span>
                            @else
                                <span class="inline-block px-3 py-1 rounded-full text-xs font-medium bg-coral/60 text-hitam2">Ditolak</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-5 py-8 text-center text-hitam2">Belum ada permintaan registrasi.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Log Aktivitas -->
    <h2 class="font-body text-md font-semibold mb-3">Log Aktivitas</h2>
    <div class="bg-putih rounded-2xl shadow-sm overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-border text-left text-[11px] tracking-widest uppercase text-hitam2">
                    <th class="px-5 py-3 font-semibold">Keterangan</th>
                    <th class="px-5 py-3 font-semibold">Oleh</th>
                    <th class="px-5 py-3 font-semibold">Waktu</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($logAktivitas as $log)
                    <tr class="border-b border-border last:border-0">
                        <td class="px-5 py-4">{{ $log->deskripsi }}</td>
                        <td class="px-5 py-4 text-hitam2">{{ $log->user->nama_lengkap ?? '-' }}</td>
                        <td class="px-5 py-4 text-hitam2">{{ $log->created_at->diffForHumans() }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="px-5 py-8 text-center text-hitam2">Belum ada aktivitas tercatat.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-app-layout>