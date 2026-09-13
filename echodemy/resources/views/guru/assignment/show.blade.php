<x-app-layout>
    <x-slot name="header">
        <p class="text-xs font-semibold tracking-widest text-coral uppercase mb-1">
            <a href="{{ route('guru.kelas.index') }}" class="hover:underline">Kelas Saya</a> &middot;
            <a href="{{ route('guru.mata-pelajaran-kelas.show', $assignment->bahanAjar->mataPelajaranKelas) }}"
                class="hover:underline">{{ $assignment->bahanAjar->mataPelajaranKelas->mataPelajaran->nama }}</a>
        </p>
        <h1 class="font-display text-2xl font-semibold">{{ $assignment->judul }}</h1>
    </x-slot>

    @if (session('success'))
        <div class="bg-teal/10 text-teal text-sm rounded-lg px-4 py-3 mb-4">{{ session('success') }}</div>
    @endif

    <div class="bg-putih rounded-2xl shadow-sm p-5 mb-6 inline-flex items-center gap-10">
        <div>
            <p class="text-[11px] font-semibold tracking-widest uppercase text-hitam2 mb-1">Terkumpul</p>
            <p class="font-medium">{{ $hasilList->count() }}/{{ $totalSiswa }} Siswa</p>
        </div>
        <div>
            <p class="text-[11px] font-semibold tracking-widest uppercase text-hitam2 mb-1">Batas Waktu</p>
            <p class="font-medium">{{ $assignment->batas_waktu?->format('d M Y H:i') ?? '-' }}</p>
        </div>
    </div>

    <div class="bg-putih rounded-2xl shadow-sm overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-border text-left text-[11px] tracking-widest uppercase text-hitam2">
                    <th class="px-5 py-3 font-semibold">Nama</th>
                    <th class="px-5 py-3 font-semibold">Pengumpulan</th>
                    <th class="px-5 py-3 font-semibold">Status</th>
                    <th class="px-5 py-3 font-semibold">Nilai</th>
                    <th class="px-5 py-3 font-semibold">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($hasilList as $hasil)
                    <tr class="border-b border-border last:border-0">
                        <td class="px-5 py-4 font-medium">{{ $hasil->siswa->nama_lengkap }}</td>
                        <td class="px-5 py-4 text-hitam2">{{ $hasil->created_at->format('d/m/y H.i.s') }}</td>
                        <td class="px-5 py-4">
                            <span
                                class="inline-block px-3 py-1 rounded-full text-xs font-medium {{ $hasil->status === 'dinilai' ? 'bg-teal/60 text-hitam' : 'bg-amber/60 text-hitam' }}">
                                {{ ucfirst($hasil->status) }}
                            </span>
                        </td>

                        <td class="px-5 py-4">
                            <div class="flex items-center gap-2">
                                @if ($hasil->file)
                                    <a href="{{ \Storage::url($hasil->file) }}" target="_blank"
                                        class="w-8 h-8 rounded-lg bg-teal/60 text-hitam flex items-center justify-center hover:bg-teal/70">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                            stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5" />
                                        </svg>
                                    </a>
                                @endif
                                <button type="button"
                                    class="mark-nilai-btn w-8 h-8 rounded-lg bg-amber/60 text-hitam flex items-center justify-center hover:bg-amber/70"
                                    data-id="{{ $hasil->id }}" data-nilai="{{ $hasil->nilai }}">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                        stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931z" />
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-5 py-8 text-center text-hitam2">Belum ada siswa yang mengumpulkan.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <form id="mark-nilai-form" method="POST" class="hidden">
        @csrf
        @method('PUT')
        <input type="hidden" name="nilai" value="100">
    </form>

    <div id="nilai-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center px-4 py-8 overflow-y-auto">
        <div class="modal-overlay absolute inset-0 bg-ink/40"></div>
        <div class="relative bg-putih rounded-2xl shadow-lg w-full max-w-sm p-6 my-auto">
            <p class="text-xs font-semibold tracking-widest text-coral uppercase mb-1">Beri Nilai</p>
            <h2 class="font-display font-semibold text-lg mb-4">Nilai Tugas</h2>

            <form id="nilai-form" method="POST" class="space-y-4">
                @csrf
                @method('PUT')
                <div>
                    <label class="text-xs font-semibold uppercase text-hitam2">Nilai (0-100)</label>
                    <input type="number" name="nilai" id="nilai-input" min="0" max="100" required
                        class="mt-2 w-full rounded-lg border-border bg-cream2 focus:border-coral focus:ring-coral text-sm">
                </div>
                <div class="flex justify-end gap-2 pt-2">
                    <button type="button"
                        class="modal-close-btn text-sm font-medium text-hitam2 px-4 py-2 hover:text-ink">Batal</button>
                    <button type="submit"
                        class="bg-ink text-cream text-sm font-medium rounded-lg px-4 py-2 hover:bg-hitam">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        $(function() {
            $('.mark-nilai-btn').on('click', function() {
                $('#nilai-input').val($(this).data('nilai'));
                $('#nilai-form').attr('action',
                    '{{ url('/guru/assignment/' . $assignment->id . '/hasil') }}/' + $(this).data('id'));
                $('#nilai-modal').removeClass('hidden');
            });

            $('.modal-close-btn, .modal-overlay').on('click', function() {
                $(this).closest('[id$="-modal"]').addClass('hidden');
            });
        });
    </script>
</x-app-layout>
