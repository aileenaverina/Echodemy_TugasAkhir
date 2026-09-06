<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold tracking-widest text-coral uppercase mb-1">Kelas Saya</p>
                <h1 class="font-display text-2xl font-semibold">Kelas yang Saya Ampu</h1>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('guru.kelas.index', ['filter' => 'aktif']) }}"
                    class="px-4 py-2 rounded-full text-sm font-medium transition {{ $filter === 'aktif' ? 'bg-ink text-cream' : 'bg-putih text-hitam2 border border-border hover:bg-cream2' }}">
                    Aktif
                </a>
                <a href="{{ route('guru.kelas.index', ['filter' => 'selesai']) }}"
                    class="px-4 py-2 rounded-full text-sm font-medium transition {{ $filter === 'selesai' ? 'bg-ink text-cream' : 'bg-putih text-hitam2 border border-border hover:bg-cream2' }}">
                    Selesai
                </a>
            </div>
        </div>
    </x-slot>

    @if ($items->isEmpty())
        <div class="bg-putih rounded-2xl shadow-sm p-8 text-center text-hitam2">
            Belum ada kelas yang kamu ampu.
        </div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            @foreach ($items as $item)
                @php
                    $isWali = $item->label === 'Wali Kelas';
                    $badgeClass = $isWali ? 'bg-periwinkle/60 text-hitam' : 'bg-amber/60 text-hitam';
                @endphp
                <a href="{{ route('guru.kelas.show', $item->kelas) }}"
                    class="block bg-putih rounded-2xl shadow-sm p-5 hover:shadow-md transition">
                    <div class="flex items-start justify-between mb-2">
                        @if ($item->mata_pelajaran)
                            <span class="text-xs font-semibold uppercase"
                                style="color: {{ $item->mata_pelajaran->warna }}; text-shadow: -0.5px -0.5px 0 #E7E0D2, 0.5px -0.5px 0 #E7E0D2, -0.5px 0.5px 0 #E7E0D2, 0.5px 0.5px 0 #E7E0D2;">
                                {{ $item->mata_pelajaran->nama }}
                            </span>
                        @else
                            <span></span>
                        @endif
                        <span
                            class="inline-block px-3 py-1 rounded-full text-xs font-medium {{ $badgeClass }}">{{ $item->label }}</span>
                    </div>
                    <p class="font-display font-semibold text-lg">{{ $item->kelas->nama }}</p>
                    <p class="text-sm text-hitam2 mt-1">{{ $item->kelas->jumlah_siswa }} Siswa</p>
                </a>
            @endforeach
        </div>
    @endif
</x-app-layout>
