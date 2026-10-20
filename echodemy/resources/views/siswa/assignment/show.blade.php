<x-app-layout>
    <x-slot name="header">
        <p class="text-xs font-semibold tracking-widest text-coral uppercase mb-1">Kelas Saya</p>
        <h1 class="font-display text-2xl font-semibold">{{ $assignment->judul }}</h1>
    </x-slot>

    @if (session('success'))
        <div class="bg-teal/10 text-teal text-sm rounded-lg px-4 py-3 mb-4">{{ session('success') }}</div>
    @endif
    @if ($errors->any())
        <div class="bg-coral/10 text-coral text-sm rounded-lg px-4 py-3 mb-4">{{ $errors->first() }}</div>
    @endif

    @if ($assignment->batas_waktu)
        <p class="text-sm font-semibold text-coral mb-4">Deadline: {{ $assignment->batas_waktu->translatedFormat('l, d M Y H:i') }}</p>
    @endif

    @php
        $status = $hasil->status ?? 'belum dikerjakan';
        $badgeClass = match ($status) {
            'dikumpulkan' => 'bg-teal/60 text-hitam',
            'telat' => 'bg-amber/60 text-hitam',
            'dinilai' => 'bg-periwinkle/60 text-hitam',
            default => 'bg-cream2 text-hitam2',
        };
    @endphp

    <div class="flex flex-col sm:flex-row gap-4 mb-6">
        <div class="bg-putih rounded-2xl shadow-sm p-5 flex-1">
            <p class="text-[11px] font-semibold tracking-widest uppercase text-hitam2 mb-1">Status</p>
            <span class="inline-block px-3 py-1 rounded-full text-xs font-medium {{ $badgeClass }}">{{ ucfirst($status) }}</span>
        </div>
        <div class="bg-putih rounded-2xl shadow-sm p-5">
            <p class="text-[11px] font-semibold tracking-widest uppercase text-hitam2 mb-1">Nilai</p>
            <p class="font-display font-semibold text-lg">{{ $hasil?->nilai ?? '-' }}/100</p>
        </div>
    </div>

    @if ($assignment->file)
        <div class="bg-putih rounded-2xl shadow-sm p-5 mb-6">
            <p class="text-xs font-semibold tracking-widest uppercase text-hitam2 mb-2">Lampiran dari Guru</p>
            <a href="{{ \Storage::url($assignment->file) }}" download class="inline-flex items-center gap-2 text-sm font-medium text-coral hover:underline">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5" /></svg>
                Unduh Lampiran
            </a>
        </div>
    @endif

    @if ($status === 'dinilai')
        <div class="bg-putih rounded-2xl shadow-sm p-6">
            <p class="text-xs font-semibold tracking-widest uppercase text-hitam2 mb-3">Jawaban Terkumpul</p>
            @if ($hasil->file)
                <a href="{{ \Storage::url($hasil->file) }}" download class="inline-flex items-center gap-2 text-sm font-medium text-coral hover:underline mb-3">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5" /></svg>
                    Lihat File yang Dikumpulkan
                </a>
            @endif
            @if ($hasil->text)
                <div class="bg-cream2 rounded-lg p-4 text-sm whitespace-pre-line">{{ $hasil->text }}</div>
            @endif
            <p class="text-sm text-hitam2 mt-4">Tugas ini sudah dinilai dan tidak bisa diubah lagi.</p>
        </div>
    @else
        <div class="bg-putih rounded-2xl shadow-sm p-6">
            <p class="text-xs font-semibold tracking-widest uppercase text-hitam2 mb-4">
                {{ $hasil ? 'Kumpulkan Ulang' : 'Kumpulkan Tugas' }}
            </p>

            <form method="POST" action="{{ route('siswa.assignment.kumpul', $assignment) }}" enctype="multipart/form-data" class="space-y-4">
                @csrf

                @if (in_array($assignment->tipe_pengumpulan, ['file', 'keduanya']))
                    <div>
                        <label class="text-xs font-semibold uppercase text-hitam2">File</label>
                        <input type="file" name="file" class="mt-2 w-full text-sm text-hitam2 file:mr-3 file:py-2 file:px-3 file:rounded-lg file:border-0 file:bg-cream2 file:text-ink file:text-sm">
                        @if ($hasil?->file)
                            <p class="text-xs text-hitam2 mt-1">File sebelumnya: <a href="{{ \Storage::url($hasil->file) }}" class="text-coral hover:underline" download>unduh</a></p>
                        @endif
                    </div>
                @endif

                @if (in_array($assignment->tipe_pengumpulan, ['text', 'keduanya']))
                    <div>
                        <label class="text-xs font-semibold uppercase text-hitam2">Teks Jawaban</label>
                        <textarea name="text" rows="8" placeholder="Tulis jawabanmu di sini..."
                            class="mt-2 w-full rounded-lg border-border bg-cream2 focus:border-coral focus:ring-coral text-sm">{{ old('text', $hasil->text ?? '') }}</textarea>
                    </div>
                @endif

                <button type="submit" class="bg-ink text-cream text-sm font-medium rounded-lg px-5 py-2.5 hover:bg-hitam">
                    {{ $hasil ? 'Kumpulkan Ulang' : 'Kumpulkan' }}
                </button>
            </form>
        </div>
    @endif
</x-app-layout>