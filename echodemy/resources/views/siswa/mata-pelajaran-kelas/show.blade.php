<x-app-layout>
    <x-slot name="header">
        <p class="text-xs font-semibold tracking-widest text-coral uppercase mb-1">
            <a href="{{ route('dashboard') }}" class="hover:underline">Kelas Saya</a>
        </p>
        <h1 class="font-display text-2xl font-semibold">{{ $mataPelajaranKelas->mataPelajaran->nama }}</h1>
    </x-slot>

    <!-- Info Card -->
    <div class="bg-putih rounded-2xl shadow-sm p-5 mb-6 flex items-center gap-10">
        <div>
            <p class="text-[11px] font-semibold tracking-widest uppercase text-hitam2 mb-1">Siswa</p>
            <p class="font-medium">{{ $jumlahSiswa }} Siswa</p>
        </div>
        <div>
            <p class="text-[11px] font-semibold tracking-widest uppercase text-hitam2 mb-1">Pengajar</p>
            <p class="font-medium">{{ $mataPelajaranKelas->gurus->pluck('nama_lengkap')->join(', ') }}</p>
        </div>
    </div>

    <!-- Sections -->
    <div class="space-y-3">
        @forelse ($bahanAjars as $bahanAjar)
            @php
                $hasContent =
                    $bahanAjar->materis->isNotEmpty() ||
                    $bahanAjar->assignments->isNotEmpty() ||
                    $bahanAjar->latihans->isNotEmpty();
            @endphp
            @if ($hasContent)
                <div class="bg-cream2 rounded-2xl overflow-hidden">
                    <div class="flex items-center justify-between px-5 py-4 cursor-pointer section-toggle"
                        data-target="section-{{ $bahanAjar->id }}">
                        <div class="flex items-center gap-3">
                            <svg class="w-4 h-4 text-hitam2 transition-transform section-chevron" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                            </svg>
                            <div>
                                <p class="font-display font-semibold">{{ $bahanAjar->judul }}</p>
                                @if ($bahanAjar->notes)
                                    <p class="text-sm text-hitam2 mt-0.5">{{ $bahanAjar->notes }}</p>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div id="section-{{ $bahanAjar->id }}" class="hidden bg-putih mx-2 mb-2 rounded-xl p-4">
                        @if ($bahanAjar->materis->isNotEmpty())
                            <p class="text-[11px] font-semibold tracking-widest uppercase text-hitam2 mb-2">Materi</p>
                            <div class="space-y-2 mb-4">
                                @foreach ($bahanAjar->materis as $materi)
                                    @php $isDone = in_array($materi->id, $progresSelesai); @endphp
                                    <div
                                        class="flex items-center justify-between px-4 py-2.5 rounded-lg border border-border">
                                        <div class="flex items-center gap-2">
                                            <svg class="w-4 h-4 text-hitam2" fill="none" viewBox="0 0 24 24"
                                                stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                                            </svg>
                                            <span class="text-sm">{{ $materi->nama }}</span>
                                        </div>
                                        <button type="button"
                                            class="toggle-progress-btn flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-medium border transition
                                            {{ $isDone ? 'border-teal text-teal bg-teal/10' : 'border-border text-hitam2 hover:bg-cream2' }}"
                                            data-id="{{ $materi->id }}" data-done="{{ $isDone ? '1' : '0' }}">
                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24"
                                                stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M4.5 12.75l6 6 9-13.5" />
                                            </svg>
                                            <span class="btn-label">{{ $isDone ? 'Done' : 'Tandai Selesai' }}</span>
                                        </button>
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        @if ($bahanAjar->assignments->isNotEmpty() || $bahanAjar->latihans->isNotEmpty())
                            <p class="text-[11px] font-semibold tracking-widest uppercase text-hitam2 mb-2">Tugas & Kuis
                            </p>
                            <div class="space-y-2">
                                @foreach ($bahanAjar->assignments as $assignment)
                                    <a href="{{ route('siswa.assignment.show', $assignment) }}"
                                        class="block px-4 py-2.5 rounded-lg border border-border hover:bg-cream2 transition"></a>
                                        <div class="flex items-center gap-2">
                                            <svg class="w-4 h-4 text-hitam2" fill="none" viewBox="0 0 24 24"
                                                stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M9 12h3.75M9 15h3.75M9 18h3.75M3.75 4.5h16.5M4.5 4.5v15a1.5 1.5 0 001.5 1.5h12a1.5 1.5 0 001.5-1.5v-15" />
                                            </svg>
                                            <p class="text-sm">{{ $assignment->judul }}</p>
                                        </div>
                                        @if ($assignment->batas_waktu)
                                            <p class="text-xs text-hitam2 mt-1 ml-6">Batas:
                                                {{ $assignment->batas_waktu->format('d M Y H:i') }}</p>
                                        @endif
                                    </a>
                                @endforeach

                                @foreach ($bahanAjar->latihans as $latihan)
                                    @php
                                        $menit = $latihan->waktu_kerja;
                                        $waktuLabel =
                                            $menit >= 60 && $menit % 60 === 0
                                                ? $menit / 60 . ' Jam'
                                                : $menit . ' Menit';
                                    @endphp
                                    <a href="{{ route('siswa.latihan.show', $latihan) }}"
                                        class="block px-4 py-2.5 rounded-lg border border-border hover:bg-cream2 transition">
                                        <div class="flex items-center gap-2">
                                            <svg class="w-4 h-4 text-hitam2" fill="none" viewBox="0 0 24 24"
                                                stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9 5.25h.008v.008H12v-.008z" />
                                            </svg>
                                            <p class="text-sm">{{ $latihan->judul }}</p>
                                        </div>
                                        <p class="text-xs text-hitam2 mt-1 ml-6">
                                            @if ($latihan->batas_waktu)
                                                Batas: {{ $latihan->batas_waktu->format('d M Y H:i') }} &middot;
                                            @endif
                                            {{ $latihan->maksimal_percobaan }}x Percobaan &middot; Waktu:
                                            {{ $waktuLabel }}
                                        </p>
                                    </a>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        @empty
            <div class="bg-putih rounded-2xl shadow-sm p-8 text-center text-hitam2">
                Belum ada materi di mata pelajaran ini.
            </div>
        @endforelse
    </div>

    <script>
        $(function() {
            $('.section-toggle').on('click', function() {
                const target = $(this).data('target');
                $('#' + target).toggleClass('hidden');
                $(this).find('.section-chevron').toggleClass('rotate-180');
            });

            $('.toggle-progress-btn').on('click', function(e) {
                e.stopPropagation();
                const $btn = $(this);
                const id = $btn.data('id');

                $.post('{{ url('/siswa/materi') }}/' + id + '/toggle-progress', {
                    _token: '{{ csrf_token() }}',
                }).done(function(data) {
                    if (data.is_done) {
                        $btn.removeClass('border-border text-hitam2 hover:bg-cream2').addClass(
                            'border-teal text-teal bg-teal/10');
                        $btn.find('.btn-label').text('Done');
                    } else {
                        $btn.removeClass('border-teal text-teal bg-teal/10').addClass(
                            'border-border text-hitam2 hover:bg-cream2');
                        $btn.find('.btn-label').text('Tandai Selesai');
                    }
                });
            });
        });
    </script>
</x-app-layout>
