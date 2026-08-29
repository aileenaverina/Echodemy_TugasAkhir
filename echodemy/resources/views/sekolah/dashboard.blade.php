<x-app-layout>
    <x-slot name="header">
        <p class="text-xs font-semibold tracking-widest text-coral uppercase mb-1">Beranda</p>
        <h1 class="font-display text-2xl font-semibold">{{ auth()->user()->sekolah->nama }}</h1>
    </x-slot>

    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <div class="bg-putih rounded-2xl shadow-sm p-5">
            <div class="w-10 h-10 rounded-xl bg-periwinkle/60 flex items-center justify-center mb-4">
                <img src="{{ asset('images/school.svg') }}" alt="school" class="w-5 h-5">
            </div>
            <p class="text-3xl font-display font-semibold">{{ $stats['kelas_aktif'] }}</p>
            <p class="text-sm text-cream3 mt-1">Kelas Aktif</p>
        </div>

        <div class="bg-putih rounded-2xl shadow-sm p-5">
            <div class="w-10 h-10 rounded-xl bg-coral/60 flex items-center justify-center mb-4">
                <img src="{{ asset('images/sekolah/teacher.svg') }}" alt="teacher" class="w-5 h-5">
            </div>
            <p class="text-3xl font-display font-semibold">{{ $stats['guru_terdaftar'] }}</p>
            <p class="text-sm text-cream3 mt-1">Guru Terdaftar</p>
        </div>

        <div class="bg-putih rounded-2xl shadow-sm p-5">
            <div class="w-10 h-10 rounded-xl bg-teal/60 flex items-center justify-center mb-4">
                <img src="{{ asset('images/users.svg') }}" alt="users" class="w-5 h-5">
            </div>
            <p class="text-3xl font-display font-semibold">{{ $stats['siswa_aktif'] }}</p>
            <p class="text-sm text-cream3 mt-1">Siswa Aktif</p>
        </div>

        <div class="bg-putih rounded-2xl shadow-sm p-5">
            <div class="w-10 h-10 rounded-xl bg-amber/60 flex items-center justify-center mb-4">
                <img src="{{ asset('images/document.svg') }}" alt="document" class="w-5 h-5">
            </div>
            <p class="text-3xl font-display font-semibold">{{ $stats['mata_pelajaran'] }}</p>
            <p class="text-sm text-cream3 mt-1">Mata Pelajaran</p>
        </div>
    </div>

    <div class="flex items-center justify-between mb-3">
        <h2 class="font-display text-lg font-semibold">Daftar Kelas</h2>
        <a href="{{ route('sekolah.kelas.index') }}" class="text-sm font-semibold text-coral hover:underline">Kelola Kelas</a>
    </div>
    <div class="bg-putih rounded-2xl shadow-sm overflow-hidden mb-8">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-border text-left text-[11px] tracking-widest uppercase text-hitam2">
                    <th class="px-5 py-3 font-semibold">Kelas</th>
                    <th class="px-5 py-3 font-semibold">Wali Kelas</th>
                    <th class="px-5 py-3 font-semibold">Jumlah Siswa</th>
                    <th class="px-5 py-3 font-semibold">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($daftarKelas as $kelas)
                    <tr class="border-b border-border last:border-0">
                        <td class="px-5 py-4 font-medium">{{ $kelas->nama }}</td>
                        <td class="px-5 py-4 text-hitam2">{{ $kelas->waliKelas->first()?->nama_lengkap ?? '-' }}</td>
                        <td class="px-5 py-4 text-hitam2">{{ $kelas->jumlah_siswa }} Siswa</td>
                        <td class="px-5 py-4">
                            <span class="inline-block px-3 py-1 rounded-full text-xs font-medium {{ $kelas->status === 'aktif' ? 'bg-teal/60 text-hitam' : 'bg-cream2 text-hitam2' }}">
                                {{ ucfirst($kelas->status) }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-5 py-8 text-center text-hitam2">Belum ada kelas.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="flex items-center justify-between mb-3">
        <h2 class="font-display text-lg font-semibold">Log Aktivitas</h2>
        <a href="{{ route('sekolah.log.index') }}" class="text-sm font-semibold text-coral hover:underline">Lihat Semua</a>
    </div>
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
                        <td class="px-5 py-4 text-hitam2">{{ $log->user?->displayName() ?? 'Sistem' }}</td>
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