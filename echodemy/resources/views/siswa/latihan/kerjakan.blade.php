<!DOCTYPE html>
<html lang="id" class="bg-cream">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $latihan->judul }} - Echodemy</title>

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
        <div class="max-w-7xl mx-auto px-4 sm:px-6 h-16 flex items-center justify-between">
            <p class="font-display font-semibold text-sm">Echodemy</p>
            <div id="timer-box" class="bg-putih border border-border rounded-lg px-4 py-1.5 text-right">
                <p class="text-[10px] font-semibold tracking-widest uppercase text-hitam2">Waktu</p>
                <p id="timer-display" class="font-display font-semibold">--:--:--</p>
            </div>
        </div>
    </header>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 py-6">
        <p class="text-xs font-semibold tracking-widest text-coral uppercase mb-1">Kelas Saya</p>
        <h1 class="font-display text-2xl font-semibold mb-4">{{ $latihan->judul }}</h1>

        <!-- Progress bar segmen -->
        <div class="flex gap-1 mb-6">
            @foreach ($soalList as $index => $item)
                <div
                    class="h-1.5 flex-1 rounded-full {{ isset($jawabanTersimpan[$item->id]) ? 'bg-teal' : ($index + 1 === $nomor ? 'bg-amber' : 'bg-cream2') }}">
                </div>
            @endforeach
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
            <!-- Soal -->
            <div class="lg:col-span-2 bg-putih rounded-2xl shadow-sm p-6">
                <p class="text-xs font-semibold tracking-widest uppercase text-hitam2 mb-2">Soal {{ $nomor }}
                    dari {{ $soalList->count() }}</p>
                <p class="font-display font-semibold text-lg mb-5">{{ $soal->pertanyaan }}</p>

                <form id="jawaban-form">
                    @csrf
                    <input type="hidden" name="detail_latihan_id" value="{{ $soal->id }}">

                    @if ($soal->tipe_soal === 'pilihan_ganda')
                        @php
                            $pilihans = $soal->is_random
                                ? $soal->pilihans->shuffle()
                                : $soal->pilihans->sortBy('nomor_abjad');
                            $jawabanSekarang = $jawabanTersimpan[$soal->id] ?? null;
                        @endphp
                        <div class="space-y-2">
                            @foreach ($pilihans as $pilihan)
                                <label
                                    class="pilihan-option flex items-center gap-3 px-4 py-3 rounded-lg border cursor-pointer transition
                                    {{ $jawabanSekarang === $pilihan->pilihan_jawaban ? 'border-amber bg-amber/10' : 'border-border hover:bg-cream2' }}">
                                    <input type="radio" name="pilihan_id" value="{{ $pilihan->id }}"
                                        class="hidden pilihan-radio"
                                        {{ $jawabanSekarang === $pilihan->pilihan_jawaban ? 'checked' : '' }}>
                                    <span
                                        class="w-6 h-6 rounded-full flex items-center justify-center text-xs font-semibold shrink-0
                                        {{ $jawabanSekarang === $pilihan->pilihan_jawaban ? 'bg-amber text-hitam' : 'bg-cream2 text-hitam2' }}">
                                        {{ $pilihan->nomor_abjad }}
                                    </span>
                                    <span class="text-sm">{{ $pilihan->pilihan_jawaban }}</span>
                                </label>
                            @endforeach
                        </div>
                    @elseif ($soal->tipe_soal === 'isian')
                        <input type="text" name="jawaban" value="{{ $jawabanTersimpan[$soal->id] ?? '' }}"
                            id="jawaban-input" placeholder="Ketik jawaban singkat..."
                            class="w-full rounded-lg border-border bg-cream2 focus:border-coral focus:ring-coral text-sm">
                    @else
                        <textarea name="jawaban" id="jawaban-input" rows="8" placeholder="Tulis jawaban lengkapmu di sini..."
                            class="w-full rounded-lg border-border bg-cream2 focus:border-coral focus:ring-coral text-sm">{{ $jawabanTersimpan[$soal->id] ?? '' }}</textarea>
                    @endif
                </form>
            </div>

            <!-- List Soal -->
            <div class="bg-putih rounded-2xl shadow-sm p-5">
                <p class="font-display font-semibold mb-3">List Soal</p>
                <div class="flex flex-wrap gap-2">
                    @foreach ($soalList as $index => $item)
                        <a href="{{ route('siswa.latihan.kerjakan', [$latihan, $submission, 'soal' => $index + 1]) }}"
                            class="w-9 h-9 rounded-lg flex items-center justify-center text-sm font-semibold
                            {{ $index + 1 === $nomor ? 'bg-amber text-hitam' : (isset($jawabanTersimpan[$item->id]) ? 'bg-periwinkle text-white' : 'bg-cream2 text-hitam2') }}">
                            {{ $index + 1 }}
                        </a>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="flex items-center justify-end gap-2 mt-6">
            @if ($nomor > 1)
                <a href="{{ route('siswa.latihan.kerjakan', [$latihan, $submission, 'soal' => $nomor - 1]) }}"
                    class="bg-putih border border-border text-ink text-sm font-medium rounded-lg px-5 py-2.5 hover:bg-cream2">
                    Sebelumnya
                </a>
            @endif

            @if ($nomor < $soalList->count())
                <button type="button" id="next-btn"
                    class="bg-ink text-cream text-sm font-medium rounded-lg px-5 py-2.5 hover:bg-hitam">
                    Selanjutnya
                </button>
            @else
                <button type="button" id="finish-btn"
                    class="bg-teal text-white text-sm font-medium rounded-lg px-5 py-2.5 hover:opacity-90">
                    Selesai & Kumpulkan
                </button>
            @endif
        </div>
    </div>

    <script>
        $(function() {
            const jawabUrl = '{{ route('siswa.latihan.jawab', [$latihan, $submission]) }}';
            const nextUrl = @json($nomor < $soalList->count() ? route('siswa.latihan.kerjakan', [$latihan, $submission, 'soal' => $nomor + 1]) : null);
            const selesaiUrl = '{{ route('siswa.latihan.selesai', [$latihan, $submission]) }}';

            $('.pilihan-option').on('click', function() {
                $('.pilihan-option').removeClass('border-amber bg-amber/10').addClass('border-border');
                $(this).addClass('border-amber bg-amber/10').removeClass('border-border');
                $(this).find('.pilihan-radio').prop('checked', true);
                $(this).find('span').first().removeClass('bg-cream2 text-hitam2').addClass(
                    'bg-amber text-hitam');
            });

            function saveAnswer(callback) {
                const data = $('#jawaban-form').serialize();
                $.post(jawabUrl, data).always(function() {
                    if (callback) callback();
                });
            }

            $('#next-btn').on('click', function() {
                saveAnswer(function() {
                    window.location.href = nextUrl;
                });
            });

            $('#finish-btn').on('click', function() {
                saveAnswer(function() {
                    Swal.fire({
                        title: 'Kumpulkan jawaban?',
                        text: 'Kamu tidak bisa mengubah jawaban setelah ini.',
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#1C1A17',
                        cancelButtonColor: '#5B5A55',
                        confirmButtonText: 'Ya, kumpulkan',
                        cancelButtonText: 'Batal',
                    }).then(function(result) {
                        if (result.isConfirmed) {
                            const form = $('<form>', {
                                method: 'POST',
                                action: selesaiUrl
                            });
                            form.append(
                                '<input type="hidden" name="_token" value="{{ csrf_token() }}">'
                            );
                            $('body').append(form);
                            form.submit();
                        }
                    });
                });
            });

            // Countdown timer
            const deadline = new Date('{{ $deadline->toIso8601String() }}').getTime();

            function updateTimer() {
                const now = new Date().getTime();
                const distance = deadline - now;

                if (distance <= 0) {
                    $('#timer-display').text('00:00:00');
                    Swal.fire({
                        title: 'Waktu Habis',
                        text: 'Jawaban kamu akan otomatis dikumpulkan.',
                        icon: 'warning',
                        confirmButtonColor: '#1C1A17',
                        allowOutsideClick: false,
                    }).then(function() {
                        saveAnswer(function() {
                            const form = $('<form>', {
                                method: 'POST',
                                action: selesaiUrl
                            });
                            form.append(
                                '<input type="hidden" name="_token" value="{{ csrf_token() }}">'
                            );
                            $('body').append(form);
                            form.submit();
                        });
                    });
                    clearInterval(timerInterval);
                    return;
                }

                const h = Math.floor(distance / (1000 * 60 * 60));
                const m = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                const s = Math.floor((distance % (1000 * 60)) / 1000);

                $('#timer-display').text(
                    String(h).padStart(2, '0') + ':' + String(m).padStart(2, '0') + ':' + String(s).padStart(2,
                        '0')
                );
            }

            updateTimer();
            const timerInterval = setInterval(updateTimer, 1000);

            // Auto-save tiap ganti soal via "Sebelumnya" / klik nomor
            $(window).on('beforeunload', function() {
                navigator.sendBeacon(jawabUrl, new FormData(document.getElementById('jawaban-form')));
            });
        });
    </script>
</body>

</html>
