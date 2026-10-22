<x-app-layout>
    <x-slot name="header">
        <p class="text-xs font-semibold tracking-widest text-coral uppercase mb-1">
            <a href="{{ route('guru.latihan.show', $latihan) }}" class="hover:underline">{{ $latihan->judul }}</a>
        </p>
        <h1 class="font-display text-2xl font-semibold">Daftar Soal</h1>
    </x-slot>

    <div class="space-y-4">
        @foreach ($latihan->detailLatihans as $detail)
            <div class="bg-putih rounded-2xl shadow-sm p-5">
                <p class="text-xs font-semibold text-hitam2 mb-1">Soal {{ $detail->nomor }} &middot; {{ $detail->nilai }}
                    poin</p>
                <p class="font-medium mb-3">{{ $detail->pertanyaan }}</p>

                @if ($detail->tipe_soal === 'pilihan_ganda')
                    <div class="space-y-2">
                        @foreach ($detail->pilihans as $pilihan)
                            @php $p = $pilihan->persentase_correct; @endphp
                            <div
                                class="flex items-center gap-2 text-sm px-3 py-2 rounded-lg {{ $p > 0 ? 'bg-teal/20 text-hitam font-medium' : ($p < 0 ? 'bg-coral/10 text-hitam' : 'bg-cream2 text-hitam2') }}">
                                <span class="font-semibold">{{ $pilihan->nomor_abjad }}.</span>
                                {{ $pilihan->pilihan_jawaban }}
                                @if ($p != 0)
                                    <span
                                        class="text-xs ml-auto {{ $p > 0 ? 'text-teal' : 'text-coral' }}">{{ $p > 0 ? '+' : '' }}{{ $p }}%</span>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @elseif ($detail->tipe_soal === 'isian')
                    <p class="text-sm text-hitam2">Variasi jawaban benar: <span
                            class="font-medium text-ink">{{ implode(', ', $detail->jawaban ?? []) }}</span></p>
                @else
                    <p class="text-sm text-hitam2 italic">Esai (jawaban panjang) — dinilai manual</p>
                @endif
            </div>
        @endforeach
    </div>
</x-app-layout>
