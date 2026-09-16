<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold tracking-widest text-coral uppercase mb-1">Raport</p>
                <h1 class="font-display text-2xl font-semibold">Rekap Nilai Siswa</h1>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('guru.rapor.index', ['filter' => 'aktif']) }}"
                    class="px-4 py-2 rounded-full text-sm font-medium transition {{ $filter === 'aktif' ? 'bg-ink text-cream' : 'bg-putih text-hitam2 border border-border hover:bg-cream2' }}">
                    Aktif
                </a>
                <a href="{{ route('guru.rapor.index', ['filter' => 'selesai']) }}"
                    class="px-4 py-2 rounded-full text-sm font-medium transition {{ $filter === 'selesai' ? 'bg-ink text-cream' : 'bg-putih text-hitam2 border border-border hover:bg-cream2' }}">
                    Selesai
                </a>
            </div>
        </div>
    </x-slot>

    @if ($kelasList->isEmpty())
        <div class="bg-putih rounded-2xl shadow-sm p-8 text-center text-hitam2">
            Kamu belum jadi wali kelas manapun.
        </div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            @foreach ($kelasList as $kelas)
                <a href="{{ route('guru.rapor.kelas', $kelas) }}" class="block bg-putih rounded-2xl shadow-sm p-5 hover:shadow-md transition">
                    <div class="flex items-start justify-between mb-2">
                        <p class="font-display font-semibold text-lg">{{ $kelas->nama }}</p>
                        <span class="inline-block px-3 py-1 rounded-full text-xs font-medium bg-periwinkle/60 text-hitam">Wali Kelas</span>
                    </div>
                    <p class="text-sm text-hitam2">{{ $kelas->jumlah_siswa }} Siswa</p>
                </a>
            @endforeach
        </div>
    @endif
</x-app-layout>