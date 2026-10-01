<x-app-layout>
    <x-slot name="header">
        <p class="text-xs font-semibold tracking-widest text-coral uppercase mb-1">
            <a href="{{ route('siswa.mata-pelajaran-kelas.show', $mataPelajaranKelas) }}" class="hover:underline">{{ $mataPelajaranKelas->mataPelajaran->nama }}</a>
        </p>
        <h1 class="font-display text-2xl font-semibold">{{ $materi->nama }}</h1>
    </x-slot>

    <div id="unsupported-warning" class="hidden bg-coral/10 text-coral text-sm rounded-lg px-4 py-3 mb-4">
        Browser kamu belum mendukung fitur Text-to-Speech. Coba gunakan Google Chrome atau Microsoft Edge versi terbaru.
    </div>

    @if ($ttsAvailable)
        <p class="text-xs text-hitam2 mb-4">💡 Blok teks di bawah untuk memunculkan tombol dengarkan.</p>
    @endif

    <div class="bg-putih rounded-2xl shadow-sm p-6 max-w-3xl">
        @if ($materi->tipe === 'teks')
            <div id="content-panel" class="prose prose-sm max-w-none text-ink leading-relaxed">
                {!! nl2br(e($materi->konten)) !!}
            </div>

        @elseif ($materi->tipe === 'url')
            <p class="text-xs font-semibold tracking-widest uppercase text-hitam2 mb-4">Tautan</p>
            <a href="{{ $materi->url }}" target="_blank" rel="noopener"
                class="inline-flex items-center gap-2 bg-ink text-cream text-sm font-medium rounded-lg px-4 py-2.5 hover:bg-hitam">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 003 8.25v10.5A2.25 2.25 0 005.25 21h10.5A2.25 2.25 0 0018 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25" />
                </svg>
                Buka Tautan
            </a>
            <p class="text-xs text-hitam2 mt-3">Fitur Dengarkan tidak tersedia untuk tautan.</p>

        @else
            @php $downloadUrl = \Storage::url($materi->file); @endphp

            @if ($isPdf)
                <div class="flex items-center justify-between mb-4">
                    <p class="text-xs font-semibold tracking-widest uppercase text-hitam2">Konten</p>
                    <a href="{{ $downloadUrl }}" download class="text-sm font-medium text-coral hover:underline">Unduh PDF</a>
                </div>
                <div id="pdf-loading" class="text-sm text-hitam2">Memuat teks dari PDF...</div>
                <div id="content-panel" class="hidden prose prose-sm max-w-none text-ink leading-relaxed"></div>
                <div id="pdf-empty-warning" class="hidden bg-amber/10 text-hitam text-sm rounded-lg px-4 py-3 mt-3">
                    Teks tidak dapat dideteksi dari PDF ini (kemungkinan hasil pindai/scan). Fitur Dengarkan tidak tersedia untuk file ini.
                </div>
            @else
                <p class="text-xs font-semibold tracking-widest uppercase text-hitam2 mb-4">File</p>
                <a href="{{ $downloadUrl }}" download
                    class="inline-flex items-center gap-2 bg-ink text-cream text-sm font-medium rounded-lg px-4 py-2.5 hover:bg-hitam">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5" />
                    </svg>
                    Unduh {{ strtoupper($ext) }}
                </a>
                <p class="text-xs text-hitam2 mt-3">Fitur Dengarkan tidak tersedia untuk file Word/PowerPoint. Unduh file untuk membacanya lewat aplikasi terkait.</p>
            @endif
        @endif
    </div>

    <!-- Tombol play mengambang, muncul di dekat teks yang diblok -->
    <button type="button" id="floating-play-btn"
        class="hidden fixed z-50 w-10 h-10 rounded-full bg-ink text-cream shadow-lg flex items-center justify-center hover:bg-hitam">
        <svg id="floating-play-icon" class="w-4 h-4 ml-0.5" fill="currentColor" viewBox="0 0 24 24">
            <path d="M8 5v14l11-7z" />
        </svg>
    </button>

    @if ($materi->tipe === 'file' && $isPdf)
        <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
    @endif

    <script>
    $(function () {
        if (!('speechSynthesis' in window)) {
            $('#unsupported-warning').removeClass('hidden');
        }

        let currentText = '';
        let isSpeaking = false;
        let isPaused = false;

        function setIcon(state) {
            const $icon = $('#floating-play-icon');
            if (state === 'pause') {
                $icon.html('<path d="M6 5h4v14H6zM14 5h4v14h-4z" />').removeClass('ml-0.5');
            } else {
                $icon.html('<path d="M8 5v14l11-7z" />').addClass('ml-0.5');
            }
        }

        function hideFloatingBtn() {
            $('#floating-play-btn').addClass('hidden');
            speechSynthesis.cancel();
            isSpeaking = false;
            isPaused = false;
        }

        // Deteksi seleksi teks (mouse di desktop, touch/selection di HP)
        $(document).on('mouseup touchend', function (e) {
            // Jangan ganggu kalau yang diklik itu tombolnya sendiri
            if ($(e.target).closest('#floating-play-btn').length) return;

            setTimeout(function () {
                const selection = window.getSelection();
                const text = selection.toString().trim();

                if (!text || !$('#content-panel')[0]?.contains(selection.anchorNode)) {
                    hideFloatingBtn();
                    return;
                }

                currentText = text;

                const range = selection.getRangeAt(0);
                const rect = range.getBoundingClientRect();

                $('#floating-play-btn').css({
                    top: (rect.top + window.scrollY - 48) + 'px',
                    left: Math.max(8, rect.left + window.scrollX + (rect.width / 2) - 20) + 'px',
                }).removeClass('hidden');

                setIcon('play');
                isSpeaking = false;
                isPaused = false;
            }, 10);
        });

        $('#floating-play-btn').on('click', function () {
            if (!currentText) return;

            if (isSpeaking && !isPaused) {
                speechSynthesis.pause();
                isPaused = true;
                setIcon('play');
                return;
            }

            if (isPaused) {
                speechSynthesis.resume();
                isPaused = false;
                setIcon('pause');
                return;
            }

            speechSynthesis.cancel();
            const utterance = new SpeechSynthesisUtterance(currentText);
            utterance.lang = 'id-ID';

            const voices = speechSynthesis.getVoices();
            const matchingVoice = voices.find(v => v.lang === 'id-ID');
            if (!matchingVoice) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Suara tidak tersedia',
                    text: 'Perangkat/browser kamu tidak memiliki suara Bahasa Indonesia untuk Text-to-Speech.',
                    confirmButtonColor: '#1C1A17',
                });
                return;
            }
            utterance.voice = matchingVoice;

            utterance.onend = function () {
                isSpeaking = false;
                isPaused = false;
                setIcon('play');
            };

            speechSynthesis.speak(utterance);
            isSpeaking = true;
            isPaused = false;
            setIcon('pause');
        });

        @if ($materi->tipe === 'file' && $isPdf)
        pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';

        fetch('{{ \Storage::url($materi->file) }}')
            .then(res => res.arrayBuffer())
            .then(async function (arrayBuffer) {
                const pdf = await pdfjsLib.getDocument({ data: arrayBuffer }).promise;
                let fullText = '';

                for (let i = 1; i <= pdf.numPages; i++) {
                    const page = await pdf.getPage(i);
                    const content = await page.getTextContent();
                    fullText += content.items.map(item => item.str).join(' ') + '\n\n';
                }

                fullText = fullText.trim();
                $('#pdf-loading').addClass('hidden');

                if (fullText) {
                    const paragraphs = fullText.split('\n\n').map(function (p) {
                        return '<p class="mb-3">' + $('<div>').text(p).html() + '</p>';
                    }).join('');
                    $('#content-panel').removeClass('hidden').html(paragraphs);
                } else {
                    $('#pdf-empty-warning').removeClass('hidden');
                }
            })
            .catch(function () {
                $('#pdf-loading').addClass('hidden');
                $('#pdf-empty-warning').removeClass('hidden').text('Gagal memuat teks dari PDF ini.');
            });
        @endif
    });
    </script>
</x-app-layout>