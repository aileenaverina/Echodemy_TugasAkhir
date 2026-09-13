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
                <p class="text-xs font-semibold text-hitam2 mb-1">Soal {{ $detail->nomor }} &middot; {{ $detail->nilai }} poin</p>
                <p class="font-medium mb-3">{{ $detail->pertanyaan }}</p>

                @if ($detail->tipe_soal === 'pilihan_ganda')
                    <div class="space-y-2">
                        @foreach ($detail->pilihans as $pilihan)
                            <div class="flex items-center gap-2 text-sm px-3 py-2 rounded-lg {{ $pilihan->is_correct ? 'bg-teal/20 text-hitam font-medium' : 'bg-cream2 text-hitam2' }}">
                                <span class="font-semibold">{{ $pilihan->nomor_abjad }}.</span> {{ $pilihan->pilihan_jawaban }}
                                @if ($pilihan->is_correct) <span class="text-xs text-teal ml-auto">Benar</span> @endif
                            </div>
                        @endforeach
                    </div>
                @elseif ($detail->jawaban)
                    <p class="text-sm text-hitam2">Jawaban benar: <span class="font-medium text-ink">{{ $detail->jawaban }}</span></p>
                @else
                    <p class="text-sm text-hitam2 italic">Esai — dinilai manual</p>
                @endif
            </div>
        @endforeach
    </div>
</x-app-layout>