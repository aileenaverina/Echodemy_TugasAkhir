<x-app-layout>
    <x-slot name="header">
        <p class="text-xs font-semibold tracking-widest text-coral uppercase mb-1">Kelas Saya</p>
        <h1 class="font-display text-2xl font-semibold">Hasil {{ $latihan->judul }}</h1>
    </x-slot>

    <div class="bg-teal/10 rounded-2xl px-5 py-4 flex items-center justify-between mb-6">
        <p class="font-display font-semibold text-teal">Nilai</p>
        <p class="font-display font-semibold text-2xl">{{ $answers->sum('nilai') }}/100</p>
    </div>

    <div class="space-y-4">
        @foreach ($answers as $answer)
            @php $detail = $answer->detailLatihan; @endphp
            <div class="bg-putih rounded-2xl shadow-sm p-5">
                <p class="text-xs font-semibold text-hitam2 mb-1">Soal {{ $detail->nomor }} &middot; Nilai: {{ $answer->nilai }}/{{ $detail->nilai }}</p>
                <p class="font-medium mb-3">{{ $detail->pertanyaan }}</p>

                <div class="bg-cream2 rounded-lg p-4">
                    <p class="text-xs font-semibold uppercase text-hitam2 mb-1">Jawabanmu</p>
                    <p class="text-sm">{{ $answer->teksJawaban() ?: '(tidak dijawab)' }}</p>
                </div>
            </div>
        @endforeach
    </div>
</x-app-layout>