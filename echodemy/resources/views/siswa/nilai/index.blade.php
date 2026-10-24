<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-xs font-semibold tracking-widest text-coral uppercase mb-1">Nilai</p>
            <h1 class="font-display text-2xl font-semibold">Nilai {{ auth()->user()->nama_lengkap }}</h1>
        </div>
    </x-slot>

    @if (!$kelas)
        <div class="bg-putih rounded-2xl shadow-sm p-10 text-center text-hitam2 text-sm">
            Kamu belum terdaftar di kelas manapun.
        </div>
    @elseif ($mataPelajaranKelasList->isEmpty())
        <div class="bg-putih rounded-2xl shadow-sm p-10 text-center text-hitam2 text-sm">
            Belum ada mata pelajaran di kelasmu.
        </div>
    @else
        @include('partials.daftar-raport')
        @include('partials.semester-tabs')

        <!-- Tab mata pelajaran -->
        <div class="flex items-center gap-2 mb-5 flex-wrap">
            @foreach ($mataPelajaranKelasList as $mpk)
                @php $isActive = $activeMpk?->id === $mpk->id; @endphp
                <a href="{{ route('siswa.nilai.index', ['mapel' => $mpk->id, 'semester' => $semester]) }}"
                    class="px-4 py-2 rounded-full text-sm font-medium transition {{ $isActive ? 'bg-ink text-cream' : 'bg-putih text-hitam2 border border-border hover:bg-cream2' }}">
                    <span class="inline-block w-2 h-2 rounded-full mr-1.5 align-middle"
                        style="background-color: {{ $mpk->mataPelajaran->warna }}"></span>
                    {{ $mpk->mataPelajaran->nama }}
                </a>
            @endforeach
        </div>

        @if ($rekap)
            @include('partials.rekap-nilai')
        @endif
    @endif
</x-app-layout>