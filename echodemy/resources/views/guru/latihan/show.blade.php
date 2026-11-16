<x-app-layout>
    <x-slot name="header">
        <p class="text-xs font-semibold tracking-widest text-coral uppercase mb-1">
            <a href="{{ route('guru.kelas.index') }}" class="hover:underline">Kelas Saya</a> &middot;
            <a href="{{ route('guru.mata-pelajaran-kelas.show', $latihan->bahanAjar->mataPelajaranKelas) }}"
                class="hover:underline">{{ $latihan->bahanAjar->mataPelajaranKelas->mataPelajaran->nama }}</a>
        </p>
        <div class="flex items-center justify-between">
            <h1 class="font-display text-2xl font-semibold">{{ $latihan->judul }}</h1>
        </div>
    </x-slot>

    @if (session('success'))
        <div class="bg-teal/10 text-teal text-sm rounded-lg px-4 py-3 mb-4">{{ session('success') }}</div>
    @endif
    @if ($errors->any())
        <div class="bg-coral/10 text-coral text-sm rounded-lg px-4 py-3 mb-4">{{ $errors->first() }}</div>
    @endif

    <div class="flex items-center justify-between mb-6">
        <div class="bg-putih rounded-2xl shadow-sm p-5 flex items-center gap-10">
            <div>
                <p class="text-[11px] font-semibold tracking-widest uppercase text-hitam2 mb-1">Terkumpul</p>
                <p class="font-medium">{{ $submissions->count() }}/{{ $totalSiswa }} Siswa</p>
            </div>
            <div>
                <p class="text-[11px] font-semibold tracking-widest uppercase text-hitam2 mb-1">Nilai Maksimal</p>
                <p class="font-medium">{{ $nilaiMaksimal }}</p>
            </div>
            <a href="{{ route('guru.latihan.soal', $latihan) }}"
                class="bg-cream2 text-ink text-sm font-medium rounded-lg px-4 py-2.5 hover:bg-border">
                Lihat Soal
            </a>
            @if (!$adaYangMulai)
                <a href="{{ route('guru.latihan.edit-soal', $latihan) }}"
                    class="bg-amber/60 text-hitam text-sm font-medium rounded-lg px-4 py-2.5 hover:bg-amber/70">
                    Edit Soal
                </a>
            @endif
            <button type="button" id="pengaturan-btn"
                class="bg-cream2 text-ink text-sm font-medium rounded-lg px-4 py-2.5 hover:bg-border">
                Pengaturan
            </button>
            <button type="button" id="edit-pengaturan-btn"
                class="bg-cream2 text-ink text-sm font-medium rounded-lg px-4 py-2.5 hover:bg-border">
                Edit Pengaturan
            </button>
            @if (!$adaYangMulai)
                <a href="{{ route('guru.latihan.edit-soal', $latihan) }}"
                    class="bg-cream2 text-ink text-sm font-medium rounded-lg px-4 py-2.5 hover:bg-border">
                    Edit Soal
                </a>
            @endif
        </div>

        <div class="bg-putih rounded-2xl shadow-sm p-5 text-right">
            <p class="text-[11px] font-semibold tracking-widest uppercase text-hitam2 mb-1">Bobot Persentase dari Nilai
                Akhir</p>
            <p class="font-display font-semibold text-lg">{{ $latihan->persen_nilai_akhir ?? 0 }}%</p>
        </div>
    </div>

    <div class="bg-putih rounded-2xl shadow-sm overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-border text-left text-[11px] tracking-widest uppercase text-hitam2">
                    <th class="px-5 py-3 font-semibold">Nama</th>
                    <th class="px-5 py-3 font-semibold">Percobaan</th>
                    <th class="px-5 py-3 font-semibold">Dimulai</th>
                    <th class="px-5 py-3 font-semibold">Nilai</th>
                    <th class="px-5 py-3 font-semibold">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($submissions as $item)
                    @php
                        $nilai = $item['terakhir']->nilai_akhir ?? $item['terakhir']->answers->sum('nilai');
                    @endphp
                    <tr class="border-b border-border last:border-0">
                        <td class="px-5 py-4 font-medium">{{ $item['siswa']->nama_lengkap }}</td>
                        <td class="px-5 py-4 text-hitam2">{{ $item['percobaan'] }}/{{ $latihan->maksimal_percobaan }}
                        </td>
                        <td class="px-5 py-4 text-hitam2">{{ $item['terakhir']->created_at->format('d/m/y H.i.s') }}
                        </td>
                        <td class="px-5 py-4">{{ $nilai }}</td>
                        <td class="px-5 py-4">
                            <a href="{{ route('guru.koreksi.show', [$latihan, $item['terakhir']]) }}"
                                class="inline-flex w-8 h-8 rounded-lg bg-amber/60 text-hitam items-center justify-center hover:bg-amber/70">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                    stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931z" />
                                </svg>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-5 py-8 text-center text-hitam2">Belum ada siswa yang mengerjakan.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Modal Edit Pengaturan Latihan -->
    <div id="edit-pengaturan-modal"
        class="hidden fixed inset-0 z-50 flex items-center justify-center px-4 py-8 overflow-y-auto">
        <div class="modal-overlay absolute inset-0 bg-ink/40"></div>
        <div class="relative bg-putih rounded-2xl shadow-lg w-full max-w-lg p-6 my-auto">
            <p class="text-xs font-semibold tracking-widest text-coral uppercase mb-1">Edit</p>
            <h2 class="font-display font-semibold text-lg mb-4">Pengaturan Latihan</h2>

            <form method="POST" action="{{ route('guru.latihan.pengaturan.update', $latihan) }}" class="space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label class="text-xs font-semibold uppercase text-hitam2">Judul</label>
                    <input type="text" name="judul" value="{{ $latihan->judul }}" required
                        class="mt-2 w-full rounded-lg border-border bg-cream2 focus:border-coral focus:ring-coral text-sm">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="text-xs font-semibold uppercase text-hitam2">Kategori</label>
                        <select name="kategori"
                            class="mt-2 w-full rounded-lg border-border bg-cream2 focus:border-coral focus:ring-coral text-sm">
                            @foreach (['harian' => 'Harian', 'tengah_semester' => 'Tengah Semester', 'akhir_semester' => 'Akhir Semester'] as $val => $label)
                                <option value="{{ $val }}"
                                    {{ $latihan->kategori === $val ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="text-xs font-semibold uppercase text-hitam2">Penilaian Percobaan</label>
                        <select name="penilaian_percobaan"
                            class="mt-2 w-full rounded-lg border-border bg-cream2 focus:border-coral focus:ring-coral text-sm">
                            <option value="tertinggi"
                                {{ $latihan->penilaian_percobaan === 'tertinggi' ? 'selected' : '' }}>Tertinggi
                            </option>
                            <option value="terakhir"
                                {{ $latihan->penilaian_percobaan === 'terakhir' ? 'selected' : '' }}>Terakhir</option>
                        </select>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="text-xs font-semibold uppercase text-hitam2">Maksimal Percobaan</label>
                        <input type="number" name="maksimal_percobaan" value="{{ $latihan->maksimal_percobaan }}"
                            min="1" required
                            class="mt-2 w-full rounded-lg border-border bg-cream2 focus:border-coral focus:ring-coral text-sm">
                    </div>
                    <div>
                        <label class="text-xs font-semibold uppercase text-hitam2">Waktu Kerja (menit)</label>
                        <input type="number" name="waktu_kerja" value="{{ $latihan->waktu_kerja }}" min="1"
                            required
                            class="mt-2 w-full rounded-lg border-border bg-cream2 focus:border-coral focus:ring-coral text-sm">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="text-xs font-semibold uppercase text-hitam2">Batas Waktu</label>
                        <input type="datetime-local" name="batas_waktu"
                            value="{{ $latihan->batas_waktu?->format('Y-m-d\TH:i') }}"
                            class="mt-2 w-full rounded-lg border-border bg-cream2 focus:border-coral focus:ring-coral text-sm">
                    </div>
                    <div>
                        <label class="text-xs font-semibold uppercase text-hitam2">Bobot (%)</label>
                        <input type="number" name="persen_nilai_akhir"
                            value="{{ $latihan->persen_nilai_akhir ?? 0 }}" min="0" max="100"
                            class="mt-2 w-full rounded-lg border-border bg-cream2 focus:border-coral focus:ring-coral text-sm">
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <input type="hidden" name="is_random" value="0">
                    <input type="checkbox" name="is_random" id="is_random" value="1"
                        {{ $latihan->is_random ? 'checked' : '' }}
                        class="rounded border-border text-coral focus:ring-coral">
                    <label for="is_random" class="text-sm">Acak urutan soal</label>
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
    <!-- Modal Pengaturan -->
    <div id="pengaturan-modal"
        class="hidden fixed inset-0 z-50 flex items-center justify-center px-4 py-8 overflow-y-auto">
        <div class="modal-overlay absolute inset-0 bg-ink/40"></div>
        <div class="relative bg-putih rounded-2xl shadow-lg w-full max-w-lg p-6 my-auto">
            <p class="text-xs font-semibold tracking-widest text-coral uppercase mb-1">Pengaturan</p>
            <h2 class="font-display font-semibold text-lg mb-4">Pengaturan Latihan</h2>

            <form method="POST" action="{{ route('guru.latihan.pengaturan.update', $latihan) }}" class="space-y-4">
                @csrf
                @method('PUT')
                <div>
                    <label class="text-xs font-semibold uppercase text-hitam2">Judul</label>
                    <input type="text" name="judul" required value="{{ old('judul', $latihan->judul) }}"
                        class="mt-2 w-full rounded-lg border-border bg-cream2 focus:border-coral focus:ring-coral text-sm">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="text-xs font-semibold uppercase text-hitam2">Kategori</label>
                        <select name="kategori"
                            class="mt-2 w-full rounded-lg border-border bg-cream2 focus:border-coral focus:ring-coral text-sm">
                            @foreach (['harian' => 'Harian', 'tengah_semester' => 'Tengah Semester (UTS)', 'akhir_semester' => 'Akhir Semester (UAS)'] as $v => $l)
                                <option value="{{ $v }}" @selected(old('kategori', $latihan->kategori) === $v)>{{ $l }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="text-xs font-semibold uppercase text-hitam2">Nilai yang Diambil</label>
                        <select name="penilaian_percobaan"
                            class="mt-2 w-full rounded-lg border-border bg-cream2 focus:border-coral focus:ring-coral text-sm">
                            @foreach (['tertinggi' => 'Percobaan tertinggi', 'terakhir' => 'Percobaan terakhir'] as $v => $l)
                                <option value="{{ $v }}" @selected(old('penilaian_percobaan', $latihan->penilaian_percobaan) === $v)>{{ $l }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="text-xs font-semibold uppercase text-hitam2">Batas Waktu</label>
                        <input type="datetime-local" name="batas_waktu" required
                            value="{{ old('batas_waktu', $latihan->batas_waktu?->format('Y-m-d\TH:i')) }}"
                            class="mt-2 w-full rounded-lg border-border bg-cream2 focus:border-coral focus:ring-coral text-sm">
                    </div>
                    <div>
                        <label class="text-xs font-semibold uppercase text-hitam2">Bobot Nilai Akhir (%)</label>
                        <input type="number" name="persen_nilai_akhir" min="0" max="100"
                            value="{{ old('persen_nilai_akhir', $latihan->persen_nilai_akhir ?? 0) }}"
                            class="mt-2 w-full rounded-lg border-border bg-cream2 focus:border-coral focus:ring-coral text-sm">
                    </div>
                    <div>
                        <label class="text-xs font-semibold uppercase text-hitam2">Maks. Percobaan</label>
                        <input type="number" name="maksimal_percobaan" min="1" required
                            value="{{ old('maksimal_percobaan', $latihan->maksimal_percobaan) }}"
                            class="mt-2 w-full rounded-lg border-border bg-cream2 focus:border-coral focus:ring-coral text-sm">
                    </div>
                    <div>
                        <label class="text-xs font-semibold uppercase text-hitam2">Waktu Kerja (menit)</label>
                        <input type="number" name="waktu_kerja" min="1" required
                            value="{{ old('waktu_kerja', $latihan->waktu_kerja) }}" @readonly($adaYangMulai)
                            class="mt-2 w-full rounded-lg border-border bg-cream2 focus:border-coral focus:ring-coral text-sm {{ $adaYangMulai ? 'opacity-60' : '' }}">
                        @if ($adaYangMulai)
                            <p class="text-[11px] text-hitam2 mt-1">Dikunci karena sudah ada siswa yang mulai.</p>
                        @endif
                    </div>
                </div>
                <label class="flex items-center gap-2 text-sm">
                    <input type="hidden" name="is_random" value="0">
                    <input type="checkbox" name="is_random" value="1" @checked(old('is_random', $latihan->is_random))
                        class="rounded border-border text-coral focus:ring-coral">
                    Acak urutan nomor soal
                </label>
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

            $('#edit-pengaturan-btn').on('click', function() {
                $('#edit-pengaturan-modal').removeClass('hidden');
            });
            $('.modal-close-btn, .modal-overlay').on('click', function() {
                $(this).closest('[id$="-modal"]').addClass('hidden');
            });

            $('#pengaturan-btn').on('click', function() {
                $('#pengaturan-modal').removeClass('hidden');
            });

            $('.modal-close-btn, .modal-overlay').on('click', function() {
                $(this).closest('[id$="-modal"]').addClass('hidden');
            });

            @if ($errors->any())
                $('#pengaturan-modal').removeClass('hidden');
            @endif
        });
    </script>
</x-app-layout>
