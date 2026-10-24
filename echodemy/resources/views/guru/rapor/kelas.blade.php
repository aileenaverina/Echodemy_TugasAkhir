<x-app-layout>
    <x-slot name="header">
        <p class="text-xs font-semibold tracking-widest text-coral uppercase mb-1">
            <a href="{{ route('guru.rapor.index') }}" class="hover:underline">Raport</a>
        </p>
        <h1 class="font-display text-2xl font-semibold">{{ $kelas->nama }}</h1>
    </x-slot>

    @if (session('success'))
        <div class="bg-teal/10 text-teal text-sm rounded-lg px-4 py-3 mb-4">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="bg-coral/10 text-coral text-sm rounded-lg px-4 py-3 mb-4">{{ session('error') }}</div>
    @endif

    <div class="flex flex-wrap items-center justify-between gap-3 mb-5">
        <div class="flex items-center gap-2">
            @foreach ([1, 2] as $s)
                <a href="{{ route('guru.rapor.kelas', [$kelas, 'semester' => $s, 'search' => $search]) }}"
                    class="px-4 py-2 rounded-full text-sm font-medium transition {{ $semester === $s ? 'bg-ink text-cream' : 'bg-putih text-hitam2 border border-border hover:bg-cream2' }}">
                    Semester {{ $s }}
                </a>
            @endforeach
        </div>

        <form method="POST" action="{{ route('guru.rapor.terbitkan-semua', $kelas) }}" id="terbitkan-semua-form">
            @csrf
            <input type="hidden" name="semester" value="{{ $semester }}">
            <button type="button" id="terbitkan-semua-btn"
                class="bg-ink text-cream text-sm font-medium rounded-lg px-4 py-2.5 hover:bg-hitam">
                Terbitkan Semua yang Lengkap
            </button>
        </form>
    </div>

    <form method="GET" id="search-form" class="mb-5">
        <input type="hidden" name="semester" value="{{ $semester }}">
        <div class="relative max-w-md">
            <svg class="w-4 h-4 absolute left-3.5 top-1/2 -translate-y-1/2 text-hitam2" fill="none"
                viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M21 21l-4.35-4.35M17 10.5A6.5 6.5 0 114 10.5a6.5 6.5 0 0113 0z" />
            </svg>
            <input type="text" name="search" id="search-input" value="{{ $search }}"
                placeholder="Cari nama siswa..."
                class="w-full pl-10 pr-4 py-2.5 rounded-lg border border-border bg-putih focus:border-coral focus:ring-coral text-sm">
        </div>
    </form>

    <div class="bg-putih rounded-2xl shadow-sm overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-border text-left text-[11px] tracking-widest uppercase text-hitam2">
                    <th class="px-5 py-3 font-semibold">Nama</th>
                    <th class="px-5 py-3 font-semibold">NIS</th>
                    <th class="px-5 py-3 font-semibold">Raport</th>
                    <th class="px-5 py-3 font-semibold">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($siswaList as $siswa)
                    @php $raport = $raports->get($siswa->id); @endphp
                    <tr class="border-b border-border last:border-0 hover:bg-cream2/40 transition">
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-3">
                                <img src="{{ $siswa->photoUrl() }}" alt=""
                                    class="w-8 h-8 rounded-full object-cover">
                                <span class="font-medium">{{ $siswa->nama_lengkap }}</span>
                            </div>
                        </td>
                        <td class="px-5 py-4 text-hitam2">{{ $siswa->detailUser?->nis ?? '-' }}</td>
                        <td class="px-5 py-4">
                            @if ($raport)
                                <span
                                    class="inline-block px-3 py-1 rounded-full text-xs font-medium bg-teal/60 text-hitam">Terbit
                                    &middot; v{{ $raport->versi }}</span>
                                <p class="text-xs text-hitam2 mt-1">
                                    {{ $raport->created_at->translatedFormat('d M Y') }}</p>
                            @else
                                <span
                                    class="inline-block px-3 py-1 rounded-full text-xs font-medium bg-cream2 text-hitam2">Belum
                                    terbit</span>
                            @endif
                        </td>
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-2">
                                <div class="relative group">
                                    <a href="{{ route('guru.rapor.siswa', [$kelas, $siswa, 'semester' => $semester]) }}"
                                        class="w-8 h-8 rounded-lg bg-teal/60 text-hitam flex items-center justify-center hover:bg-teal/70">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                            stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                    </a>
                                    <span
                                        class="pointer-events-none absolute -top-8 left-1/2 -translate-x-1/2 whitespace-nowrap bg-ink text-cream text-xs px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition">Lihat
                                        Nilai</span>
                                </div>

                                <div class="relative group">
                                    <button type="button"
                                        class="raport-btn w-8 h-8 rounded-lg bg-amber/60 text-hitam flex items-center justify-center hover:bg-amber/70"
                                        data-siswa="{{ $siswa->id }}" data-nama="{{ $siswa->nama_lengkap }}">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                            stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M9 12h6m-6 4h6M9 8h6M5 4h14a1 1 0 011 1v14a1 1 0 01-1 1H5a1 1 0 01-1-1V5a1 1 0 011-1z" />
                                        </svg>
                                    </button>
                                    <span
                                        class="pointer-events-none absolute -top-8 left-1/2 -translate-x-1/2 whitespace-nowrap bg-ink text-cream text-xs px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition">Buat
                                        Raport</span>
                                </div>

                                @if ($raport)
                                    <div class="relative group">
                                        <a href="{{ route('raport.unduh', $raport) }}"
                                            class="w-8 h-8 rounded-lg bg-periwinkle/60 text-hitam flex items-center justify-center hover:bg-periwinkle/70">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                                                stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M7.5 12L12 16.5m0 0L16.5 12M12 16.5V3" />
                                            </svg>
                                        </a>
                                        <span
                                            class="pointer-events-none absolute -top-8 left-1/2 -translate-x-1/2 whitespace-nowrap bg-ink text-cream text-xs px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition">Unduh
                                            PDF</span>
                                    </div>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-5 py-10 text-center text-hitam2">
                            {{ $search ? 'Tidak ada siswa dengan nama "' . $search . '".' : 'Belum ada siswa di kelas ini.' }}
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Modal Raport -->
    <div id="raport-modal"
        class="hidden fixed inset-0 z-50 flex items-center justify-center px-4 py-8 overflow-y-auto">
        <div class="modal-overlay absolute inset-0 bg-ink/40"></div>
        <div class="relative bg-putih rounded-2xl shadow-lg w-full max-w-md p-6 my-auto">
            <p class="text-xs font-semibold tracking-widest text-coral uppercase mb-1">Raport Semester
                {{ $semester }}</p>
            <h2 id="raport-nama" class="font-display font-semibold text-lg mb-4">-</h2>

            <div id="raport-body" class="mb-5"></div>

            <form id="raport-form" method="POST">
                @csrf
                <input type="hidden" name="semester" value="{{ $semester }}">
                <div class="flex justify-end gap-2">
                    <button type="button"
                        class="modal-close-btn text-sm font-medium text-hitam2 px-4 py-2 hover:text-ink">Tutup</button>
                    <button type="submit" id="raport-submit" disabled
                        class="bg-ink text-cream text-sm font-medium rounded-lg px-4 py-2 hover:bg-hitam disabled:opacity-40 disabled:cursor-not-allowed">
                        Terbitkan Raport
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        $(function() {
            const base = '{{ url('/guru/rapor/kelas/' . $kelas->id . '/siswa') }}';
            const semester = {{ $semester }};
            let versiTerakhir = null;

            const esc = s => $('<div>').text(s).html();

            let searchTimeout;
            $('#search-input').on('keyup', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(function() {
                    $('#search-form').submit();
                }, 500);
            });

            $('.raport-btn').on('click', function() {
                const siswaId = $(this).data('siswa');

                $('#raport-nama').text($(this).data('nama'));
                $('#raport-body').html('<p class="text-sm text-hitam2">Memeriksa kelengkapan nilai...</p>');
                $('#raport-submit').prop('disabled', true);
                $('#raport-form').attr('action', base + '/' + siswaId + '/terbitkan');
                $('#raport-modal').removeClass('hidden');

                $.getJSON(base + '/' + siswaId + '/cek', {
                    semester: semester
                }).done(function(data) {
                    versiTerakhir = data.versi_terakhir;

                    let html = '<div class="space-y-2 mb-3">';
                    data.mapel.forEach(function(m) {
                        html +=
                            '<div class="flex items-center justify-between px-4 py-2.5 rounded-lg border border-border">' +
                            '<span class="text-sm font-medium">' + esc(m.nama) + '</span>' +
                            (m.lengkap ?
                                '<span class="text-sm font-semibold text-teal">' + m
                                .hasil_akhir + '</span>' :
                                '<span class="text-xs font-semibold text-coral">Belum lengkap</span>'
                                ) +
                            '</div>';
                    });
                    html += '</div>';

                    if (!data.lengkap) {
                        html += '<div class="bg-coral/10 rounded-lg p-3">' +
                            '<p class="text-xs font-semibold text-coral mb-1">Raport belum bisa dibuat:</p>' +
                            '<ul class="text-xs list-disc pl-4 space-y-0.5">' +
                            data.alasan.map(a => '<li>' + esc(a) + '</li>').join('') +
                            '</ul></div>';
                    }

                    $('#raport-body').html(html);
                    $('#raport-submit')
                        .prop('disabled', !data.lengkap)
                        .text(versiTerakhir ? 'Terbitkan Ulang (v' + (versiTerakhir + 1) + ')' :
                            'Terbitkan Raport');
                }).fail(function() {
                    $('#raport-body').html(
                        '<p class="text-sm text-coral">Gagal memeriksa nilai. Coba lagi.</p>');
                });
            });

            // terbit ulang minta konfirmasi dulu
            $('#raport-form').on('submit', function(e) {
                if (!versiTerakhir) return;

                e.preventDefault();
                const form = this;

                Swal.fire({
                    title: 'Terbitkan ulang raport?',
                    text: 'Versi baru akan dibuat dan menggantikan versi lama untuk siswa dan orang tua.',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#1C1A17',
                    cancelButtonColor: '#5B5A55',
                    confirmButtonText: 'Ya, terbitkan',
                    cancelButtonText: 'Batal',
                }).then(function(r) {
                    if (r.isConfirmed) form.submit();
                });
            });

            $('#terbitkan-semua-btn').on('click', function() {
                Swal.fire({
                    title: 'Terbitkan semua raport yang lengkap?',
                    text: 'Siswa yang sudah punya raport dan yang nilainya belum lengkap akan dilewati.',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#1C1A17',
                    cancelButtonColor: '#5B5A55',
                    confirmButtonText: 'Ya, terbitkan',
                    cancelButtonText: 'Batal',
                }).then(function(r) {
                    if (r.isConfirmed) $('#terbitkan-semua-form').submit();
                });
            });

            $('.modal-close-btn, .modal-overlay').on('click', function() {
                $(this).closest('[id$="-modal"]').addClass('hidden');
            });
        });
    </script>
</x-app-layout>
