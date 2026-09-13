<!DOCTYPE html>
<html lang="id" class="bg-cream">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Tambah Konten - {{ config('app.name') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link
        href="https://fonts.googleapis.com/css2?family=Fredoka:wght@500;600;700&family=Inter:wght@400;500;600&display=swap"
        rel="stylesheet">

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-body bg-cream text-ink antialiased">

    <header class="bg-putih shadow-sm">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 h-16 flex items-center justify-between">
            <a href="{{ route('guru.mata-pelajaran-kelas.show', $bahanAjar->mataPelajaranKelas) }}"
                class="flex items-center gap-2 text-sm font-medium text-hitam2 hover:text-ink">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                </svg>
                Kembali ke {{ $bahanAjar->judul }}
            </a>
            <p class="font-display font-semibold text-sm">Echodemy</p>
        </div>
    </header>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 py-8">

        <!-- Step 1: Pilih tipe -->
        <div id="type-picker">
            <p class="text-xs font-semibold tracking-widest text-coral uppercase mb-1">Tambah ke {{ $bahanAjar->judul }}
            </p>
            <h1 class="font-display text-2xl font-semibold mb-6">Apa yang ingin kamu tambahkan?</h1>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <button type="button"
                    class="type-btn bg-putih rounded-2xl shadow-sm p-6 text-left hover:shadow-md transition"
                    data-type="materi">
                    <div class="w-10 h-10 rounded-xl bg-periwinkle/60 flex items-center justify-center mb-3">
                        <svg class="w-5 h-5 text-hitam" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                            stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s4.332.477 5.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                    </div>
                    <p class="font-display font-semibold">Materi</p>
                    <p class="text-sm text-hitam2 mt-1">Teks, tautan, atau file bacaan.</p>
                </button>

                <button type="button"
                    class="type-btn bg-putih rounded-2xl shadow-sm p-6 text-left hover:shadow-md transition"
                    data-type="latihan">
                    <div class="w-10 h-10 rounded-xl bg-amber/60 flex items-center justify-center mb-3">
                        <svg class="w-5 h-5 text-hitam" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                            stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9 5.25h.008v.008H12v-.008z" />
                        </svg>
                    </div>
                    <p class="font-display font-semibold">Latihan</p>
                    <p class="text-sm text-hitam2 mt-1">Kuis dengan soal & jawaban.</p>
                </button>

                <button type="button"
                    class="type-btn bg-putih rounded-2xl shadow-sm p-6 text-left hover:shadow-md transition"
                    data-type="assignment">
                    <div class="w-10 h-10 rounded-xl bg-coral/60 flex items-center justify-center mb-3">
                        <svg class="w-5 h-5 text-hitam" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                            stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M9 12h3.75M9 15h3.75M9 18h3.75M3.75 4.5h16.5M4.5 4.5v15a1.5 1.5 0 001.5 1.5h12a1.5 1.5 0 001.5-1.5v-15" />
                        </svg>
                    </div>
                    <p class="font-display font-semibold">Tugas</p>
                    <p class="text-sm text-hitam2 mt-1">Siswa mengumpulkan file/teks.</p>
                </button>
            </div>
        </div>

        <!-- Step 2a: Form Materi -->
        <div id="form-materi" class="hidden">
            <button type="button"
                class="back-btn text-sm font-medium text-hitam2 hover:text-ink mb-4 flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                </svg> Pilih tipe lain
            </button>
            <h1 class="font-display text-2xl font-semibold mb-6">Tambah Materi</h1>

            <form method="POST" action="{{ route('guru.section.materi.store', $bahanAjar) }}"
                enctype="multipart/form-data" class="bg-putih rounded-2xl shadow-sm p-6 space-y-4">
                @csrf
                <div>
                    <label class="text-xs font-semibold uppercase text-hitam2">Nama Materi</label>
                    <input type="text" name="nama" required
                        class="mt-2 w-full rounded-lg border-border bg-cream2 focus:border-coral focus:ring-coral text-sm">
                </div>

                <div>
                    <label class="text-xs font-semibold uppercase text-hitam2">Tipe</label>
                    <div class="mt-2 flex gap-2">
                        <label class="flex-1"><input type="radio" name="tipe" value="teks"
                                class="materi-tipe-radio peer hidden" checked>
                            <div
                                class="peer-checked:bg-ink peer-checked:text-cream text-center py-2 rounded-lg border border-border cursor-pointer text-sm">
                                Teks</div>
                        </label>
                        <label class="flex-1"><input type="radio" name="tipe" value="url"
                                class="materi-tipe-radio peer hidden">
                            <div
                                class="peer-checked:bg-ink peer-checked:text-cream text-center py-2 rounded-lg border border-border cursor-pointer text-sm">
                                Tautan</div>
                        </label>
                        <label class="flex-1"><input type="radio" name="tipe" value="file"
                                class="materi-tipe-radio peer hidden">
                            <div
                                class="peer-checked:bg-ink peer-checked:text-cream text-center py-2 rounded-lg border border-border cursor-pointer text-sm">
                                File</div>
                        </label>
                    </div>
                </div>

                <div id="materi-field-teks">
                    <label class="text-xs font-semibold uppercase text-hitam2">Konten</label>
                    <textarea name="konten" rows="6"
                        class="mt-2 w-full rounded-lg border-border bg-cream2 focus:border-coral focus:ring-coral text-sm"></textarea>
                </div>
                <div id="materi-field-url" class="hidden">
                    <label class="text-xs font-semibold uppercase text-hitam2">URL</label>
                    <input type="url" name="url" placeholder="https://..."
                        class="mt-2 w-full rounded-lg border-border bg-cream2 focus:border-coral focus:ring-coral text-sm">
                </div>
                <div id="materi-field-file" class="hidden">
                    <label class="text-xs font-semibold uppercase text-hitam2">File</label>
                    <input type="file" name="file"
                        class="mt-2 w-full text-sm text-hitam2 file:mr-3 file:py-2 file:px-3 file:rounded-lg file:border-0 file:bg-cream2 file:text-ink file:text-sm">
                </div>

                <button type="submit"
                    class="bg-ink text-cream text-sm font-medium rounded-lg px-5 py-2.5 hover:bg-hitam">Simpan
                    Materi</button>
            </form>
        </div>

        <!-- Step 2b: Form Assignment -->
        <div id="form-assignment" class="hidden">
            <button type="button"
                class="back-btn text-sm font-medium text-hitam2 hover:text-ink mb-4 flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                </svg> Pilih tipe lain
            </button>
            <h1 class="font-display text-2xl font-semibold mb-6">Tambah Tugas</h1>

            <form method="POST" action="{{ route('guru.section.assignment.store', $bahanAjar) }}"
                enctype="multipart/form-data" class="bg-putih rounded-2xl shadow-sm p-6 space-y-4">
                @csrf
                <div>
                    <label class="text-xs font-semibold uppercase text-hitam2">Judul Tugas</label>
                    <input type="text" name="judul" required
                        class="mt-2 w-full rounded-lg border-border bg-cream2 focus:border-coral focus:ring-coral text-sm">
                </div>
                <div>
                    <label class="text-xs font-semibold uppercase text-hitam2">File Lampiran (opsional)</label>
                    <input type="file" name="file"
                        class="mt-2 w-full text-sm text-hitam2 file:mr-3 file:py-2 file:px-3 file:rounded-lg file:border-0 file:bg-cream2 file:text-ink file:text-sm">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="text-xs font-semibold uppercase text-hitam2">Batas Waktu</label>
                        <input type="datetime-local" name="batas_waktu"
                            class="mt-2 w-full rounded-lg border-border bg-cream2 focus:border-coral focus:ring-coral text-sm">
                    </div>
                    <div>
                        <label class="text-xs font-semibold uppercase text-hitam2">Bobot Nilai Akhir (%)</label>
                        <input type="number" name="persen_nilai_akhir" min="0" max="100"
                            class="mt-2 w-full rounded-lg border-border bg-cream2 focus:border-coral focus:ring-coral text-sm">
                    </div>
                </div>
                <button type="submit"
                    class="bg-ink text-cream text-sm font-medium rounded-lg px-5 py-2.5 hover:bg-hitam">Simpan
                    Tugas</button>
            </form>
        </div>

        <!-- Step 2c: Form Latihan -->
        <div id="form-latihan" class="hidden">
            <button type="button"
                class="back-btn text-sm font-medium text-hitam2 hover:text-ink mb-4 flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                </svg> Pilih tipe lain
            </button>
            <h1 class="font-display text-2xl font-semibold mb-6">Tambah Latihan</h1>

            <form method="POST" action="{{ route('guru.section.latihan.store', $bahanAjar) }}" class="space-y-4">
                @csrf

                <div class="bg-putih rounded-2xl shadow-sm p-6 space-y-4">
                    <div>
                        <label class="text-xs font-semibold uppercase text-hitam2">Judul Latihan</label>
                        <input type="text" name="judul" required
                            class="mt-2 w-full rounded-lg border-border bg-cream2 focus:border-coral focus:ring-coral text-sm">
                    </div>
                    <div class="grid grid-cols-3 gap-4">
                        <div>
                            <label class="text-xs font-semibold uppercase text-hitam2">Batas Waktu</label>
                            <input type="datetime-local" name="batas_waktu"
                                class="mt-2 w-full rounded-lg border-border bg-cream2 focus:border-coral focus:ring-coral text-sm">
                        </div>
                        <div>
                            <label class="text-xs font-semibold uppercase text-hitam2">Maks. Percobaan</label>
                            <input type="number" name="maksimal_percobaan" min="1" value="1" required
                                class="mt-2 w-full rounded-lg border-border bg-cream2 focus:border-coral focus:ring-coral text-sm">
                        </div>
                        <div>
                            <label class="text-xs font-semibold uppercase text-hitam2">Waktu Kerja (menit)</label>
                            <input type="number" name="waktu_kerja" min="1" required
                                class="mt-2 w-full rounded-lg border-border bg-cream2 focus:border-coral focus:ring-coral text-sm">
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="text-xs font-semibold uppercase text-hitam2">Bobot Nilai Akhir (%)</label>
                            <input type="number" name="persen_nilai_akhir" min="0" max="100"
                                class="mt-2 w-full rounded-lg border-border bg-cream2 focus:border-coral focus:ring-coral text-sm">
                        </div>
                        <label class="flex items-center gap-2 mt-7 text-sm">
                            <input type="checkbox" name="is_random" value="1"
                                class="rounded border-border text-coral focus:ring-coral">
                            Acak urutan nomor soal
                        </label>
                    </div>
                </div>

                <div id="soal-list" class="space-y-4"></div>

                <button type="button" id="add-soal-btn" class="text-sm font-semibold text-coral hover:underline">+
                    Tambah Soal</button>

                <div class="pt-2">
                    <button type="submit"
                        class="bg-ink text-cream text-sm font-medium rounded-lg px-5 py-2.5 hover:bg-hitam">Simpan
                        Latihan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Template soal (disembunyikan, dikloning via JS) -->
    <template id="soal-template">
        <div class="soal-item bg-putih rounded-2xl shadow-sm p-6 space-y-4">
            <div class="flex items-center justify-between">
                <p class="font-display font-semibold soal-number">Soal 1</p>
                <button type="button" class="remove-soal-btn text-coral text-sm font-medium hover:underline">Hapus
                    Soal</button>
            </div>

            <div>
                <label class="text-xs font-semibold uppercase text-hitam2">Pertanyaan</label>
                <textarea name="" rows="2"
                    class="soal-pertanyaan mt-2 w-full rounded-lg border-border bg-cream2 focus:border-coral focus:ring-coral text-sm"></textarea>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="text-xs font-semibold uppercase text-hitam2">Tipe Soal</label>
                    <select
                        class="soal-tipe mt-2 w-full rounded-lg border-border bg-cream2 focus:border-coral focus:ring-coral text-sm">
                        <option value="pilihan_ganda">Pilihan Ganda</option>
                        <option value="isian">Isian Singkat</option>
                        <option value="esai">Esai</option>
                    </select>
                </div>
                <div>
                    <label class="text-xs font-semibold uppercase text-hitam2">Nilai Soal</label>
                    <input type="number"
                        class="soal-nilai mt-2 w-full rounded-lg border-border bg-cream2 focus:border-coral focus:ring-coral text-sm"
                        min="0" value="10">
                </div>
            </div>

            <label class="flex items-center gap-2 text-sm">
                <input type="checkbox" class="soal-is-random rounded border-border text-coral focus:ring-coral">
                Acak urutan pilihan A/B/C/D
            </label>

            <div class="soal-field-pg">
                <label class="text-xs font-semibold uppercase text-hitam2 block mb-2">Pilihan Jawaban</label>
                <div class="pilihan-list space-y-2"></div>
                <button type="button" class="add-pilihan-btn text-sm font-semibold text-coral hover:underline mt-2">+
                    Tambah Pilihan</button>
            </div>

            <div class="soal-field-isian hidden">
                <label class="text-xs font-semibold uppercase text-hitam2 block mb-2">Jawaban Benar (bisa lebih dari
                    satu variasi)</label>
                <div class="jawaban-isian-list space-y-2"></div>
                <button type="button"
                    class="add-jawaban-isian-btn text-sm font-semibold text-coral hover:underline mt-2">+ Tambah
                    Variasi Jawaban</button>
            </div>
        </div>
    </template>

    <template id="pilihan-template">
        <div class="pilihan-item flex items-center gap-2">
            <input type="radio" class="pilihan-benar shrink-0">
            <input type="text"
                class="pilihan-teks flex-1 rounded-lg border-border bg-cream2 focus:border-coral focus:ring-coral text-sm"
                placeholder="Teks pilihan">
            <button type="button" class="remove-pilihan-btn text-coral shrink-0">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    </template>

    <template id="jawaban-isian-template">
        <div class="jawaban-isian-item flex items-center gap-2">
            <input type="text"
                class="jawaban-isian-teks flex-1 rounded-lg border-border bg-cream2 focus:border-coral focus:ring-coral text-sm"
                placeholder="cth. Jakarta">
            <button type="button" class="remove-jawaban-isian-btn text-coral shrink-0">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    </template>

    <script>
        $(function() {
            // Step navigation
            $('.type-btn').on('click', function() {
                const type = $(this).data('type');
                $('#type-picker').addClass('hidden');
                $('#form-' + type).removeClass('hidden');
            });

            $('.back-btn').on('click', function() {
                $('#form-materi, #form-assignment, #form-latihan').addClass('hidden');
                $('#type-picker').removeClass('hidden');
            });

            // Materi tipe switcher
            $('.materi-tipe-radio').on('change', function() {
                $('#materi-field-teks, #materi-field-url, #materi-field-file').addClass('hidden');
                $('#materi-field-' + $(this).val()).removeClass('hidden');
            });

            // Latihan: dynamic soal
            let soalCounter = 0;

            function reindexAll() {
                $('#soal-list .soal-item').each(function(soalIdx) {
                    const $soal = $(this);
                    $soal.data('index', soalIdx);
                    $soal.find('.soal-number').text('Soal ' + (soalIdx + 1));

                    $soal.find('.soal-pertanyaan').attr('name', `soal[${soalIdx}][pertanyaan]`);
                    $soal.find('.soal-tipe').attr('name', `soal[${soalIdx}][tipe_soal]`);
                    $soal.find('.soal-nilai').attr('name', `soal[${soalIdx}][nilai]`);
                    $soal.find('.soal-is-random').attr('name', `soal[${soalIdx}][is_random]`);

                    // Pilihan ganda
                    $soal.find('.pilihan-item').each(function(pilihanIdx) {
                        const $pilihan = $(this);
                        $pilihan.find('.pilihan-teks').attr('name',
                            `soal[${soalIdx}][pilihan][${pilihanIdx}][teks]`);
                        $pilihan.find('.pilihan-benar').attr('name',
                            `soal[${soalIdx}][jawaban_benar_radio]`).val(pilihanIdx);
                    });

                    // Hidden field jawaban_benar ikut soal ini, hapus dulu biar gak numpuk
                    $soal.find('input[name="soal[' + soalIdx + '][jawaban_benar]"]').remove();

                    // Jawaban isian
                    $soal.find('.jawaban-isian-item').each(function(jawabanIdx) {
                        $(this).find('.jawaban-isian-teks').attr('name',
                            `soal[${soalIdx}][jawaban][${jawabanIdx}]`);
                    });
                });
            }

            function addSoal() {
                soalCounter++;
                const $soal = $($('#soal-template').html());
                $('#soal-list').append($soal);

                addPilihanRaw($soal);
                addPilihanRaw($soal);

                reindexAll();
                toggleSoalFields($soal);
            }

            function addPilihanRaw($soal) {
                const $pilihan = $($('#pilihan-template').html());
                $soal.find('.pilihan-list').append($pilihan);

                $pilihan.find('.pilihan-benar').on('change', function() {
                    const soalIdx = $soal.data('index');
                    $soal.find('input[name="soal[' + soalIdx + '][jawaban_benar]"]').remove();
                    const pilihanIdx = $pilihan.index();
                    $soal.append(
                        `<input type="hidden" name="soal[${soalIdx}][jawaban_benar]" value="${pilihanIdx}">`
                        );
                });
            }

            function addJawabanIsian($soal) {
                const $jawaban = $($('#jawaban-isian-template').html());
                $soal.find('.jawaban-isian-list').append($jawaban);
                reindexAll();
            }

            function toggleSoalFields($soal) {
                const tipe = $soal.find('.soal-tipe').val();
                $soal.find('.soal-field-pg').toggleClass('hidden', tipe !== 'pilihan_ganda');
                $soal.find('.soal-field-isian').toggleClass('hidden', tipe !== 'isian');

                if (tipe === 'isian' && $soal.find('.jawaban-isian-item').length === 0) {
                    addJawabanIsian($soal);
                }
            }

            $('#add-soal-btn').on('click', addSoal);
            addSoal();

            $(document).on('change', '.soal-tipe', function() {
                toggleSoalFields($(this).closest('.soal-item'));
            });

            $(document).on('click', '.add-pilihan-btn', function() {
                addPilihanRaw($(this).closest('.soal-item'));
                reindexAll();
            });

            $(document).on('click', '.remove-pilihan-btn', function() {
                $(this).closest('.pilihan-item').remove();
                reindexAll();
            });

            $(document).on('click', '.remove-soal-btn', function() {
                $(this).closest('.soal-item').remove();
                reindexAll();
            });

            $(document).on('click', '.add-jawaban-isian-btn', function() {
                addJawabanIsian($(this).closest('.soal-item'));
            });

            $(document).on('click', '.remove-jawaban-isian-btn', function() {
                $(this).closest('.jawaban-isian-item').remove();
                reindexAll();
            });
        });
    </script>
</body>

</html>
