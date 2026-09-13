<x-app-layout>
    <x-slot name="header">
        <p class="text-xs font-semibold tracking-widest text-coral uppercase mb-1">
            <a href="{{ route('guru.latihan.show', $latihan) }}" class="hover:underline">{{ $latihan->judul }}</a>
        </p>
        <h1 class="font-display text-2xl font-semibold">Koreksi Jawaban — {{ $submission->siswa->nama_lengkap }}</h1>
    </x-slot>

    <form method="POST" action="{{ route('guru.koreksi.update', [$latihan, $submission]) }}" class="space-y-4">
        @csrf
        @method('PUT')

        @foreach ($answers as $answer)
            @php $detail = $answer->detailLatihan; @endphp
            <div class="bg-putih rounded-2xl shadow-sm p-5">
                <p class="text-xs font-semibold text-hitam2 mb-1">Soal {{ $detail->nomor }} &middot; Maks {{ $detail->nilai }} poin</p>
                <p class="font-medium mb-3">{{ $detail->pertanyaan }}</p>

                <div class="bg-cream2 rounded-lg p-4 mb-3">
                    <p class="text-xs font-semibold uppercase text-hitam2 mb-1">Jawaban Siswa</p>
                    <p class="text-sm">{{ $answer->jawaban }}</p>
                </div>

                @if ($detail->tipe_soal === 'pilihan_ganda')
                    @php $benar = $detail->pilihans->firstWhere('is_correct', true); @endphp
                    <p class="text-xs text-hitam2 mb-3">Jawaban benar: <span class="font-medium text-ink">{{ $benar?->pilihan_jawaban }}</span></p>
                @elseif (is_array($detail->jawaban) && count($detail->jawaban))
                    <p class="text-xs text-hitam2 mb-3">Variasi jawaban benar: <span class="font-medium text-ink">{{ implode(', ', $detail->jawaban) }}</span></p>
                @else
                    <p class="text-xs text-hitam2 mb-3 italic">Esai — nilai manual</p>
                @endif

                <div class="max-w-[160px]">
                    <label class="text-xs font-semibold uppercase text-hitam2">Nilai untuk soal ini</label>
                    <input type="number" name="nilai[{{ $answer->id }}]" value="{{ $answer->nilai ?? 0 }}" min="0" max="{{ $detail->nilai }}"
                        class="mt-2 w-full rounded-lg border-border bg-cream2 focus:border-coral focus:ring-coral text-sm">
                </div>
            </div>
        @endforeach

        <button type="submit" class="bg-ink text-cream text-sm font-medium rounded-lg px-5 py-2.5 hover:bg-hitam">
            Simpan Semua Nilai
        </button>
    </form>
</x-app-layout>