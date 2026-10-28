<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold tracking-widest text-coral uppercase mb-1">
                    <a href="{{ route('guru.kelas.index') }}" class="hover:underline">Kelas Saya</a> &middot;
                    {{ $mataPelajaranKelas->kelas->nama }}
                </p>
                <h1 class="font-display text-2xl font-semibold">{{ $mataPelajaranKelas->mataPelajaran->nama }}</h1>
            </div>
            <button type="button" id="manajemen-guru-btn"
                class="bg-ink text-cream text-sm font-medium rounded-lg px-4 py-2.5 hover:bg-hitam flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                Manajemen Guru
            </button>
        </div>
    </x-slot>

    @if (session('success'))
        <div class="bg-teal/10 text-teal text-sm rounded-lg px-4 py-3 mb-4">{{ session('success') }}</div>
    @endif

    <!-- Info Card -->
    <div class="bg-putih rounded-2xl shadow-sm p-5 mb-6 flex items-center justify-between">
        <div class="flex items-center gap-10">
            <div>
                <p class="text-[11px] font-semibold tracking-widest uppercase text-hitam2 mb-1">Siswa</p>
                <p class="font-medium">{{ $jumlahSiswa }} Siswa</p>
            </div>
            <div>
                <p class="text-[11px] font-semibold tracking-widest uppercase text-hitam2 mb-1">Pengajar</p>
                <p class="font-medium">{{ $mataPelajaranKelas->gurus->pluck('nama_lengkap')->join(', ') }}</p>
            </div>
        </div>
        <a href="{{ route('guru.mata-pelajaran-kelas.rekap', $mataPelajaranKelas) }}"
            class="bg-cream2 text-ink text-sm font-medium rounded-lg px-4 py-2.5 hover:bg-border">
            Lihat Rekap Nilai
        </a>
    </div>

    <div class="bg-putih rounded-2xl shadow-sm p-5 mb-6 flex flex-wrap items-center justify-between gap-4">
        <div class="flex items-center gap-8">
            @foreach ([1, 2] as $s)
                @php $b = $bobotPerSemester[$s]; @endphp
                <div>
                    <p class="text-[11px] font-semibold tracking-widest uppercase text-hitam2 mb-1">Bobot Semester
                        {{ $s }}</p>
                    <p
                        class="font-display font-semibold text-lg {{ $b === 100 ? 'text-teal' : ($b === 0 ? 'text-hitam2' : 'text-coral') }}">
                        {{ $b }}/100</p>
                </div>
            @endforeach
        </div>
        <a href="{{ route('guru.bobot.index', $mataPelajaranKelas) }}"
            class="bg-cream2 text-ink text-sm font-medium rounded-lg px-4 py-2.5 hover:bg-border">
            Atur Bobot
        </a>
    </div>

    <!-- Sections -->
    <div class="space-y-3" id="sections-wrapper">
        @forelse ($bahanAjars as $bahanAjar)
            <div class="bg-cream2 rounded-2xl overflow-hidden">
                <div class="flex items-center justify-between px-5 py-4 cursor-pointer section-toggle"
                    data-target="section-{{ $bahanAjar->id }}">
                    <div class="flex items-center gap-3">
                        <svg class="w-4 h-4 text-hitam2 transition-transform section-chevron" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                        </svg>
                        <div>
                            <p class="font-display font-semibold">{{ $bahanAjar->judul }}
                                <span
                                    class="ml-2 px-2 py-0.5 rounded-full text-[11px] font-medium bg-cream text-hitam2">Semester
                                    {{ $bahanAjar->semester }}</span>
                            </p>
                            @if ($bahanAjar->notes)
                                <p class="text-sm text-hitam2 mt-0.5">{{ $bahanAjar->notes }}</p>
                            @endif
                        </div>
                    </div>
                    <div class="flex items-center gap-2" onclick="event.stopPropagation()">
                        @if ($isPengajar)
                            <div class="relative group">
                                <button type="button"
                                    class="lock-section-btn w-8 h-8 rounded-lg {{ $bahanAjar->is_lock ? 'bg-amber/60' : 'bg-teal/60' }} text-hitam flex items-center justify-center hover:opacity-80"
                                    data-id="{{ $bahanAjar->id }}">
                                    @if ($bahanAjar->is_lock)
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                            stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                                        </svg>
                                    @else
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                            stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M13.5 10.5V6.75a4.5 4.5 0 119 0v3.75M3.75 21.75h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H3.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                                        </svg>
                                    @endif
                                </button>
                                <span
                                    class="pointer-events-none absolute -top-8 left-1/2 -translate-x-1/2 whitespace-nowrap bg-ink text-cream text-xs px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition">{{ $bahanAjar->is_lock ? 'Terkunci' : 'Kunci' }}</span>
                            </div>
                            <div class="relative group">
                                <button type="button"
                                    class="share-section-btn w-8 h-8 rounded-lg bg-periwinkle/60 text-hitam flex items-center justify-center hover:bg-periwinkle/70"
                                    data-id="{{ $bahanAjar->id }}">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                        stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M7.217 10.907a2.25 2.25 0 100 2.186m0-2.186c.18.324.283.696.283 1.093s-.103.77-.283 1.093m0-2.186l9.566-5.314m-9.566 7.5l9.566 5.314m0 0a2.25 2.25 0 103.935 2.186 2.25 2.25 0 00-3.935-2.186zm0-12.814a2.25 2.25 0 103.933-2.185 2.25 2.25 0 00-3.933 2.185z" />
                                    </svg>
                                </button>
                                <span
                                    class="pointer-events-none absolute -top-8 left-1/2 -translate-x-1/2 whitespace-nowrap bg-ink text-cream text-xs px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition">Share</span>
                            </div>
                            <div class="relative group">
                                <button type="button"
                                    class="edit-section-btn w-8 h-8 rounded-lg bg-amber/60 text-hitam flex items-center justify-center hover:bg-amber/70"
                                    data-id="{{ $bahanAjar->id }}" data-judul="{{ $bahanAjar->judul }}"
                                    data-notes="{{ $bahanAjar->notes }}" data-semester="{{ $bahanAjar->semester }}">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                        stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931z" />
                                    </svg>
                                </button>
                                <span
                                    class="pointer-events-none absolute -top-8 left-1/2 -translate-x-1/2 whitespace-nowrap bg-ink text-cream text-xs px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition">Edit</span>
                            </div>
                            <div class="relative group">
                                <button type="button"
                                    class="delete-section-btn w-8 h-8 rounded-lg bg-coral/60 text-hitam flex items-center justify-center hover:bg-coral/70"
                                    data-id="{{ $bahanAjar->id }}" data-judul="{{ $bahanAjar->judul }}">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                        stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                    </svg>
                                </button>
                                <span
                                    class="pointer-events-none absolute -top-8 left-1/2 -translate-x-1/2 whitespace-nowrap bg-ink text-cream text-xs px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition">Hapus</span>
                            </div>
                        @endif
                    </div>
                </div>

                <div id="section-{{ $bahanAjar->id }}" class="hidden bg-putih mx-2 mb-2 rounded-xl p-4">
                    @if ($bahanAjar->materis->isNotEmpty())
                        <p class="text-[11px] font-semibold tracking-widest uppercase text-hitam2 mb-2">Materi</p>
                        <div class="space-y-2 mb-4">
                            @foreach ($bahanAjar->materis as $materi)
                                <div
                                    class="flex items-center justify-between px-4 py-2.5 rounded-lg border border-border">
                                    <div class="flex items-center gap-2">
                                        <svg class="w-4 h-4 text-hitam2" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                                        </svg>
                                        <span class="text-sm">{{ $materi->nama }}</span>
                                        @if ($materi->is_lock)
                                            <span
                                                class="px-2 py-0.5 rounded-full text-[11px] font-medium bg-amber/60 text-hitam">Terkunci</span>
                                        @endif
                                    </div>
                                    @if ($isPengajar)
                                        <x-item-actions tipe="materi" :id="$materi->id" :nama="$materi->nama"
                                            :locked="(bool) $materi->is_lock" :edit-url="route('guru.materi.edit', $materi)" />
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endif

                    @if ($bahanAjar->assignments->isNotEmpty() || $bahanAjar->latihans->isNotEmpty())
                        <p class="text-[11px] font-semibold tracking-widest uppercase text-hitam2 mb-2">Tugas & Kuis
                        </p>
                        <div class="space-y-2 mb-4">
                            @foreach ($bahanAjar->assignments as $assignment)
                                <div
                                    class="flex items-center justify-between px-4 py-2.5 rounded-lg border border-border">
                                    <div class="flex items-center gap-2">
                                        <svg class="w-4 h-4 text-hitam2" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M9 12h3.75M9 15h3.75M9 18h3.75M3.75 4.5h16.5M4.5 4.5v15a1.5 1.5 0 001.5 1.5h12a1.5 1.5 0 001.5-1.5v-15" />
                                        </svg>
                                        <div>
                                            <div class="flex items-center gap-2">
                                                <p class="text-sm">{{ $assignment->judul }}</p>
                                                @if ($assignment->is_lock)
                                                    <span
                                                        class="px-2 py-0.5 rounded-full text-[11px] font-medium bg-amber/60 text-hitam">Terkunci</span>
                                                @endif
                                            </div>
                                            @if ($assignment->batas_waktu)
                                                <p class="text-xs text-hitam2">Batas:
                                                    {{ $assignment->batas_waktu->format('d M Y H:i') }}</p>
                                            @endif
                                        </div>
                                    </div>
                                    @if ($isPengajar)
                                        <x-item-actions tipe="assignment" :id="$assignment->id" :nama="$assignment->judul"
                                            :locked="(bool) $assignment->is_lock" :edit-url="route('guru.assignment.show', $assignment)" />
                                    @endif
                                </div>
                            @endforeach

                            @foreach ($bahanAjar->latihans as $latihan)
                                <div
                                    class="flex items-center justify-between px-4 py-2.5 rounded-lg border border-border">
                                    <div class="flex items-center gap-2">
                                        <svg class="w-4 h-4 text-hitam2" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9 5.25h.008v.008H12v-.008z" />
                                        </svg>
                                        <div>
                                            <p class="text-sm">{{ $latihan->judul }}</p>
                                            <p class="text-xs text-hitam2">
                                                @if ($latihan->batas_waktu)
                                                    Batas: {{ $latihan->batas_waktu->format('d M Y H:i') }} &middot;
                                                @endif
                                                {{ $latihan->maksimal_percobaan }}x Percobaan
                                            </p>
                                        </div>
                                    </div>
                                    @if ($isPengajar)
                                        <x-item-actions tipe="latihan" :id="$latihan->id" :nama="$latihan->judul"
                                            :locked="(bool) $latihan->is_lock" :edit-url="route('guru.latihan.show', $latihan)" />
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endif

                    @if ($isPengajar)
                        <a href="{{ route('guru.section.create', $bahanAjar) }}"
                            class="text-sm font-semibold text-coral hover:underline">
                            + Tambah
                        </a>
                    @endif
                </div>
            </div>
        @empty
            <div class="bg-putih rounded-2xl shadow-sm p-8 text-center text-hitam2">
                Belum ada section di mata pelajaran ini.
            </div>
        @endforelse
    </div>

    @if ($isPengajar)
        <button type="button" id="add-section-btn" class="mt-4 text-sm font-semibold text-coral hover:underline">
            + Tambah Section
        </button>
    @endif

    <!-- Modal Tambah Guru -->
    <div id="add-guru-modal"
        class="hidden fixed inset-0 z-50 flex items-center justify-center px-4 py-8 overflow-y-auto">
        <div class="modal-overlay absolute inset-0 bg-ink/40"></div>
        <div class="relative bg-putih rounded-2xl shadow-lg w-full max-w-md p-6 my-auto">
            <p class="text-xs font-semibold tracking-widest text-coral uppercase mb-1">Tambah Pengajar</p>
            <h2 class="font-display font-semibold text-lg mb-4">Tambahkan Guru Lain</h2>

            <form method="POST" action="{{ route('guru.mata-pelajaran-kelas.guru.store', $mataPelajaranKelas) }}"
                class="space-y-4">
                @csrf
                <div>
                    <label class="text-xs font-semibold uppercase text-hitam2">Pilih Guru</label>
                    <select name="guru_id" required
                        class="mt-2 w-full rounded-lg border-border bg-cream2 focus:border-coral focus:ring-coral text-sm">
                        <option value="">Pilih guru</option>
                        @foreach ($guruTersedia as $guru)
                            <option value="{{ $guru->id }}">{{ $guru->nama_lengkap }}</option>
                        @endforeach
                    </select>
                    @if ($guruTersedia->isEmpty())
                        <p class="text-xs text-hitam2 mt-2">Semua guru di sekolah ini sudah jadi pengajar.</p>
                    @endif
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

    <!-- Modal Tambah Section -->
    <div id="add-section-modal"
        class="hidden fixed inset-0 z-50 flex items-center justify-center px-4 py-8 overflow-y-auto">
        <div class="modal-overlay absolute inset-0 bg-ink/40"></div>
        <div class="relative bg-putih rounded-2xl shadow-lg w-full max-w-md p-6 my-auto">
            <p class="text-xs font-semibold tracking-widest text-coral uppercase mb-1">Section Baru</p>
            <h2 class="font-display font-semibold text-lg mb-4">Tambah Section</h2>

            <form method="POST" action="{{ route('guru.mata-pelajaran-kelas.section.store', $mataPelajaranKelas) }}"
                class="space-y-4">
                @csrf
                <div>
                    <label class="text-xs font-semibold uppercase text-hitam2">Judul Section</label>
                    <input type="text" name="judul" placeholder="cth. Pengenalan Ekosistem" required
                        class="mt-2 w-full rounded-lg border-border bg-cream2 focus:border-coral focus:ring-coral text-sm">
                </div>
                <div>
                    <label class="text-xs font-semibold uppercase text-hitam2">Deskripsi</label>
                    <textarea name="notes" rows="3"
                        class="mt-2 w-full rounded-lg border-border bg-cream2 focus:border-coral focus:ring-coral text-sm"></textarea>
                </div>
                <div>
                    <label class="text-xs font-semibold uppercase text-hitam2">Semester</label>
                    <select name="semester"
                        class="mt-2 w-full rounded-lg border-border bg-cream2 focus:border-coral focus:ring-coral text-sm">
                        <option value="1">Semester 1</option>
                        <option value="2">Semester 2</option>
                    </select>
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

    <!-- Modal Edit Section -->
    <div id="edit-section-modal"
        class="hidden fixed inset-0 z-50 flex items-center justify-center px-4 py-8 overflow-y-auto">
        <div class="modal-overlay absolute inset-0 bg-ink/40"></div>
        <div class="relative bg-putih rounded-2xl shadow-lg w-full max-w-md p-6 my-auto">
            <p class="text-xs font-semibold tracking-widest text-coral uppercase mb-1">Edit Section</p>
            <h2 class="font-display font-semibold text-lg mb-4">Ubah Section</h2>

            <form id="edit-section-form" method="POST" class="space-y-4">
                @csrf
                @method('PUT')
                <div>
                    <label class="text-xs font-semibold uppercase text-hitam2">Judul Section</label>
                    <input type="text" name="judul" id="edit-section-judul" required
                        class="mt-2 w-full rounded-lg border-border bg-cream2 focus:border-coral focus:ring-coral text-sm">
                </div>
                <div>
                    <label class="text-xs font-semibold uppercase text-hitam2">Deskripsi</label>
                    <textarea name="notes" id="edit-section-notes" rows="3"
                        class="mt-2 w-full rounded-lg border-border bg-cream2 focus:border-coral focus:ring-coral text-sm"></textarea>
                </div>
                <div>
                    <label class="text-xs font-semibold uppercase text-hitam2">Semester</label>
                    <select name="semester" id="edit-section-semester"
                        class="mt-2 w-full rounded-lg border-border bg-cream2 focus:border-coral focus:ring-coral text-sm">
                        <option value="1">Semester 1</option>
                        <option value="2">Semester 2</option>
                    </select>
                </div>
                <div class="flex justify-end gap-2 pt-2">
                    <button type="button"
                        class="modal-close-btn text-sm font-medium text-hitam2 px-4 py-2 hover:text-ink">Batal</button>
                    <button type="submit"
                        class="bg-ink text-cream text-sm font-medium rounded-lg px-4 py-2 hover:bg-hitam">Simpan
                        Perubahan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Share -->
    <div id="share-section-modal"
        class="hidden fixed inset-0 z-50 flex items-center justify-center px-4 py-8 overflow-y-auto">
        <div class="modal-overlay absolute inset-0 bg-ink/40"></div>
        <div class="relative bg-putih rounded-2xl shadow-lg w-full max-w-md p-6 my-auto">
            <p class="text-xs font-semibold tracking-widest text-coral uppercase mb-1">Share Section</p>
            <h2 class="font-display font-semibold text-lg mb-4">Salin ke Kelas Lain</h2>

            <form id="share-section-form" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="text-xs font-semibold uppercase text-hitam2">Kelas Tujuan</label>
                    <select name="target_id" id="share-target-select" required
                        class="mt-2 w-full rounded-lg border-border bg-cream2 focus:border-coral focus:ring-coral text-sm">
                        <option value="">Memuat...</option>
                    </select>
                </div>
                <p class="text-xs text-hitam2">Semua materi, tugas, dan kuis di section ini akan disalin. Data
                    pengumpulan siswa tidak ikut disalin.</p>
                <div class="flex justify-end gap-2 pt-2">
                    <button type="button"
                        class="modal-close-btn text-sm font-medium text-hitam2 px-4 py-2 hover:text-ink">Batal</button>
                    <button type="submit"
                        class="bg-ink text-cream text-sm font-medium rounded-lg px-4 py-2 hover:bg-hitam">Salin</button>
                </div>
            </form>
        </div>
    </div>

    <form id="delete-section-form" method="POST" class="hidden">
        @csrf
        @method('DELETE')
    </form>

    <!-- Modal Share Item -->
    <div id="share-item-modal"
        class="hidden fixed inset-0 z-50 flex items-center justify-center px-4 py-8 overflow-y-auto">
        <div class="modal-overlay absolute inset-0 bg-ink/40"></div>
        <div class="relative bg-putih rounded-2xl shadow-lg w-full max-w-md p-6 my-auto">
            <p class="text-xs font-semibold tracking-widest text-coral uppercase mb-1">Share</p>
            <h2 class="font-display font-semibold text-lg mb-4">Salin ke Section Lain</h2>

            <form id="share-item-form" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="text-xs font-semibold uppercase text-hitam2">Kelas Tujuan</label>
                    <select id="share-item-mpk"
                        class="mt-2 w-full rounded-lg border-border bg-cream2 focus:border-coral focus:ring-coral text-sm"></select>
                </div>
                <div>
                    <label class="text-xs font-semibold uppercase text-hitam2">Section Tujuan</label>
                    <select name="target_bahan_ajar_id" id="share-item-section" required disabled
                        class="mt-2 w-full rounded-lg border-border bg-cream2 focus:border-coral focus:ring-coral text-sm"></select>
                </div>
                <p class="text-xs text-hitam2">Item disalin dalam keadaan terkunci, bobot 0, dan tanpa batas waktu.
                    Jawaban siswa tidak ikut disalin.</p>
                <div class="flex justify-end gap-2 pt-2">
                    <button type="button"
                        class="modal-close-btn text-sm font-medium text-hitam2 px-4 py-2 hover:text-ink">Batal</button>
                    <button type="submit" id="share-item-submit" disabled
                        class="bg-ink text-cream text-sm font-medium rounded-lg px-4 py-2 hover:bg-hitam disabled:opacity-40 disabled:cursor-not-allowed">Salin</button>
                </div>
            </form>
        </div>
    </div>

    <form id="delete-item-form" method="POST" class="hidden">
        @csrf
        @method('DELETE')
    </form>

    <script>
        $(function() {
            // Accordion expand/collapse
            $('.section-toggle').on('click', function() {
                const target = $(this).data('target');
                $('#' + target).toggleClass('hidden');
                $(this).find('.section-chevron').toggleClass('rotate-180');
            });

            $('#manajemen-guru-btn').on('click', function() {
                $('#manajemen-guru-modal').removeClass('hidden');
            });

            $('.remove-guru-form').on('submit', function(e) {
                e.preventDefault();
                const form = this;
                Swal.fire({
                    title: 'Hapus guru ini dari pengajar?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#E8674A',
                    cancelButtonColor: '#5B5A55',
                    confirmButtonText: 'Ya, hapus',
                    cancelButtonText: 'Batal',
                }).then(function(result) {
                    if (result.isConfirmed) form.submit();
                });
            });

            $('#add-section-btn').on('click', function() {
                $('#add-section-modal').removeClass('hidden');
            });

            $('.modal-close-btn, .modal-overlay').on('click', function() {
                $(this).closest('[id$="-modal"]').addClass('hidden');
            });

            // Lock/unlock section
            $('.lock-section-btn').on('click', function() {
                const id = $(this).data('id');
                $.post('{{ url('/guru/section') }}/' + id + '/toggle-lock', {
                    _token: '{{ csrf_token() }}',
                }).done(function() {
                    location.reload();
                });
            });

            // Edit section
            $('.edit-section-btn').on('click', function() {
                const id = $(this).data('id');

                $('#edit-section-judul').val($(this).data('judul'));
                $('#edit-section-notes').val($(this).data('notes'));
                $('#edit-section-semester').val($(this).data('semester'));
                $('#edit-section-form').attr('action',
                    '{{ url('/guru/mata-pelajaran-kelas/' . $mataPelajaranKelas->id . '/section') }}/' +
                    id
                );
                $('#edit-section-modal').removeClass('hidden');
            });

            // Delete section
            $('.delete-section-btn').on('click', function() {
                const id = $(this).data('id');
                const judul = $(this).data('judul');

                Swal.fire({
                    title: 'Hapus section ini?',
                    text: judul + ' beserta seluruh materi dan tugas di dalamnya akan dihapus.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#E8674A',
                    cancelButtonColor: '#5B5A55',
                    confirmButtonText: 'Ya, hapus',
                    cancelButtonText: 'Batal',
                }).then(function(result) {
                    if (result.isConfirmed) {
                        $('#delete-section-form').attr('action',
                            '{{ url('/guru/mata-pelajaran-kelas/' . $mataPelajaranKelas->id . '/section') }}/' +
                            id).submit();
                    }
                });
            });

            // Share section
            $('.share-section-btn').on('click', function() {
                const bahanAjarId = $(this).data('id');

                $.getJSON('{{ route('guru.share.targets') }}', {
                    exclude: '{{ $mataPelajaranKelas->id }}'
                }).done(function(targets) {
                    const $select = $('#share-target-select');
                    $select.html('');
                    if (targets.length === 0) {
                        $select.html('<option value="">Tidak ada kelas lain</option>');
                    } else {
                        $select.html('<option value="">Pilih kelas tujuan</option>');
                        targets.forEach(function(t) {
                            $select.append($('<option>', {
                                value: t.id,
                                text: t.label
                            }));
                        });
                    }
                    $('#share-section-form').attr('action', '{{ url('/guru/section') }}/' +
                        bahanAjarId + '/share');
                    $('#share-section-modal').removeClass('hidden');
                });
            });

            // kunci / buka kunci item
            $('.lock-item-btn').on('click', function() {
                $.post('{{ url('/guru/item') }}/' + $(this).data('tipe') + '/' + $(this).data('id') +
                    '/toggle-lock', {
                        _token: '{{ csrf_token() }}',
                    }).done(function() {
                    location.reload();
                });
            });

            // hapus item
            $('.delete-item-btn').on('click', function() {
                const tipe = $(this).data('tipe'),
                    id = $(this).data('id'),
                    nama = $(this).data('nama');
                const catatan = {
                    latihan: ' Nilai kuis ini juga hilang dari rekap.',
                    assignment: ' Hasil pengumpulan siswa tidak akan tampil lagi.',
                    materi: '',
                } [tipe];

                Swal.fire({
                    title: 'Hapus item ini?',
                    text: nama + ' akan dihapus.' + catatan,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#E8674A',
                    cancelButtonColor: '#5B5A55',
                    confirmButtonText: 'Ya, hapus',
                    cancelButtonText: 'Batal',
                }).then(function(result) {
                    if (result.isConfirmed) {
                        $('#delete-item-form').attr('action', '{{ url('/guru/item') }}/' + tipe +
                            '/' + id).submit();
                    }
                });
            });

            // share item: pilih kelas tujuan lalu section tujuan
            let shareTargets = [];

            function isiSectionTujuan(mpkId) {
                const $sec = $('#share-item-section').html('');
                const mpk = shareTargets.find(t => String(t.id) === String(mpkId));

                if (!mpk || mpk.sections.length === 0) {
                    $sec.append('<option value="">Kelas ini belum punya section</option>').prop('disabled', true);
                    $('#share-item-submit').prop('disabled', true);
                    return;
                }

                mpk.sections.forEach(s => $sec.append($('<option>', {
                    value: s.id,
                    text: s.label
                })));
                $sec.prop('disabled', false);
                $('#share-item-submit').prop('disabled', false);
            }

            $('.share-item-btn').on('click', function() {
                const tipe = $(this).data('tipe'),
                    id = $(this).data('id');

                $.getJSON('{{ route('guru.share.targets') }}', {
                    exclude: '{{ $mataPelajaranKelas->id }}'
                }).done(function(targets) {
                    shareTargets = targets;
                    const $mpk = $('#share-item-mpk').html('');

                    if (!targets.length) {
                        $mpk.append('<option value="">Tidak ada kelas lain</option>');
                        $('#share-item-section').html('').prop('disabled', true);
                        $('#share-item-submit').prop('disabled', true);
                    } else {
                        targets.forEach(t => $mpk.append($('<option>', {
                            value: t.id,
                            text: t.label
                        })));
                        isiSectionTujuan(targets[0].id);
                    }

                    $('#share-item-form').attr('action', '{{ url('/guru/item') }}/' + tipe + '/' +
                        id + '/share');
                    $('#share-item-modal').removeClass('hidden');
                });
            });

            $('#share-item-mpk').on('change', function() {
                isiSectionTujuan($(this).val());
            });
        });
    </script>
</x-app-layout>
