<x-app-layout>
    <x-slot name="header">
        <p class="text-xs font-semibold tracking-widest text-coral uppercase mb-1">
            <a href="{{ route('guru.rapor.kelas', $kelas) }}" class="hover:underline">Nilai</a>
        </p>
        <h1 class="font-display text-2xl font-semibold">Nilai {{ $siswa->nama_lengkap }}</h1>
    </x-slot>

    @include('partials.semester-tabs')

    @if ($mataPelajaranKelasList->isEmpty())
        <div class="bg-putih rounded-2xl shadow-sm p-10 text-center text-hitam2 text-sm">Belum ada mata pelajaran di kelas ini.</div>
    @else
        <div class="flex items-center gap-2 mb-5 flex-wrap">
            @foreach ($mataPelajaranKelasList as $mpk)
                <a href="{{ route('guru.rapor.siswa', [$kelas, $siswa, 'mapel' => $mpk->id, 'semester' => $semester]) }}"
                    class="px-4 py-2 rounded-full text-sm font-medium transition {{ $activeMpk?->id === $mpk->id ? 'bg-ink text-cream' : 'bg-putih text-hitam2 border border-border hover:bg-cream2' }}">
                    <span class="inline-block w-2 h-2 rounded-full mr-1.5 align-middle" style="background-color: {{ $mpk->mataPelajaran->warna }}"></span>
                    {{ $mpk->mataPelajaran->nama }}
                </a>
            @endforeach
        </div>

        @if ($rekap)
            @include('partials.rekap-nilai', ['tampilAlasan' => true])
        @endif
    @endif
</x-app-layout>