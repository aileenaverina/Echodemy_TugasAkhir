<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold tracking-widest text-coral uppercase mb-1">
                    <a href="{{ route('guru.kelas.index') }}" class="hover:underline">Kelas Saya</a> &middot; {{ $kelas->nama }}
                </p>
                <div class="flex items-center gap-3">
                    <h1 class="font-display text-2xl font-semibold">{{ $kelas->nama }}</h1>
                    @if ($isWali)
                        <span class="inline-block px-3 py-1 rounded-full text-xs font-medium bg-periwinkle/60 text-hitam">Wali Kelas</span>
                    @endif
                </div>
            </div>

            @if ($isWali)
                <div class="flex items-center gap-2">
                    <a href="{{ route('guru.kelas.mata-pelajaran.index', $kelas) }}"
                        class="bg-cream2 text-ink text-sm font-medium rounded-lg px-4 py-2.5 hover:bg-border flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        Manajemen Mata Pelajaran
                    </a>
                    <a href="{{ route('guru.kelas.siswa.index', $kelas) }}"
                        class="bg-ink text-cream text-sm font-medium rounded-lg px-4 py-2.5 hover:bg-hitam flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        Manajemen Siswa
                    </a>
                </div>
            @endif
        </div>
    </x-slot>

    @if (session('success'))
        <div class="bg-teal/10 text-teal text-sm rounded-lg px-4 py-3 mb-4">{{ session('success') }}</div>
    @endif

    <!-- Info Card -->
    <div class="bg-putih rounded-2xl shadow-sm p-5 mb-6 flex items-center gap-10">
        <div>
            <p class="text-[11px] font-semibold tracking-widest uppercase text-hitam2 mb-1">Siswa</p>
            <p class="font-medium">{{ $jumlahSiswa }} Siswa</p>
        </div>
        <div>
            <p class="text-[11px] font-semibold tracking-widest uppercase text-hitam2 mb-1">Wali Kelas</p>
            <p class="font-medium">{{ $waliKelas?->nama_lengkap ?? '-' }}</p>
        </div>
    </div>

    <h2 class="font-display text-lg font-semibold mb-3">Daftar Mata Pelajaran</h2>

    @if ($mataPelajaranKelas->isEmpty())
        <div class="bg-putih rounded-2xl shadow-sm p-8 text-center text-hitam2">
            Belum ada mata pelajaran di kelas ini.
        </div>
    @else
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
            @foreach ($mataPelajaranKelas as $mpk)
                @php
                    $isImage = $mpk->mataPelajaran->file && preg_match('/\.(jpg|jpeg|png|webp)$/i', $mpk->mataPelajaran->file);
                @endphp
                <div class="bg-putih rounded-2xl shadow-sm overflow-hidden">
                    <div class="h-24 bg-cream2 bg-cover bg-center"
                        @if ($isImage) style="background-image: url('{{ \Storage::url($mpk->mataPelajaran->file) }}')" @endif>
                    </div>
                    <div class="px-4 py-3" style="background-color: {{ $mpk->mataPelajaran->warna }}">
                        <p class="font-display font-semibold text-sm"
                            style="color: #FFFFFF; text-shadow: -0.5px -0.5px 0 rgba(0,0,0,0.25), 0.5px -0.5px 0 rgba(0,0,0,0.25), -0.5px 0.5px 0 rgba(0,0,0,0.25), 0.5px 0.5px 0 rgba(0,0,0,0.25);">
                            {{ $mpk->mataPelajaran->nama }}
                        </p>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</x-app-layout>