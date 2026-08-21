<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold tracking-widest text-coral uppercase mb-1">Manajemen Sekolah</p>
                <h1 class="font-display text-2xl font-semibold">Manajemen dan Verifikasi Sekolah</h1>
            </div>
            <a href="{{ route('admin.sekolah-verification.index') }}"
                class="bg-ink text-cream text-sm font-medium rounded-lg px-4 py-2.5 hover:bg-hitam flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Verifikasi Pendaftaran
            </a>
        </div>
    </x-slot>

    @if (session('success'))
        <div class="bg-teal/10 text-teal text-sm rounded-lg px-4 py-3 mb-4">{{ session('success') }}</div>
    @endif

    <!-- Search + Filter -->
    <form method="GET" action="{{ route('admin.sekolah.index') }}" id="filter-form"
        class="flex flex-wrap items-center gap-3 mb-5">
        <input type="hidden" name="filter" id="filter-input" value="{{ $filter }}">

        <div class="relative flex-1 min-w-[240px]">
            <svg class="w-4 h-4 absolute left-3.5 top-1/2 -translate-y-1/2 text-hitam2" fill="none"
                viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M21 21l-4.35-4.35M17 10.5A6.5 6.5 0 114 10.5a6.5 6.5 0 0113 0z" />
            </svg>
            <input type="text" name="search" id="search-input" value="{{ $search }}"
                placeholder="Cari Nama Sekolah, NPSN atau Kota..."
                class="w-full pl-10 pr-4 py-2.5 rounded-lg border border-border bg-putih focus:border-coral focus:ring-coral text-sm">
        </div>

        <div class="flex items-center gap-2 flex-wrap">
            @php
                $tabs = [
                    'semua' => 'Semua',
                    'menunggu' => 'Menunggu',
                    'terverifikasi' => 'Terverifikasi',
                    'nonaktif' => 'Nonaktif',
                ];
            @endphp
            @foreach ($tabs as $key => $label)
                <button type="button" data-filter="{{ $key }}"
                    class="filter-tab px-4 py-2 rounded-full text-sm font-medium transition {{ $filter === $key ? 'bg-ink text-cream' : 'bg-putih text-hitam2 border border-border hover:bg-cream2' }}">
                    {{ $label }} &middot; {{ $counts[$key] }}
                </button>
            @endforeach
        </div>
    </form>

    <!-- Table -->
    <div class="bg-putih rounded-2xl shadow-sm overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-border text-left text-[11px] tracking-widest uppercase text-hitam2">
                    <th class="px-5 py-3 font-semibold">Sekolah</th>
                    <th class="px-5 py-3 font-semibold">Kota/Kabupaten</th>
                    <th class="px-5 py-3 font-semibold">Siswa/Guru</th>
                    <th class="px-5 py-3 font-semibold">Terdaftar Sejak</th>
                    <th class="px-5 py-3 font-semibold">Status</th>
                    <th class="px-5 py-3 font-semibold">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($sekolahs as $sekolah)
                    @php
                        $jumlahSiswa = $sekolah->users()->where('role', 'siswa')->count();
                        $jumlahGuru = $sekolah->users()->where('role', 'guru')->count();

                        [$badgeLabel, $badgeClass] = match (true) {
                            !$sekolah->is_active => ['Nonaktif', 'bg-coral/60 text-coral'],
                            $sekolah->status === \App\Models\Sekolah::STATUS_PENDING => [
                                'Menunggu',
                                'bg-amber/60 text-amber',
                            ],
                            $sekolah->status === \App\Models\Sekolah::STATUS_VERIFIED => [
                                'Aktif',
                                'bg-periwinkle/60 text-periwinkle',
                            ],
                            default => ['Ditolak', 'bg-coral/60 text-coral'],
                        };
                    @endphp
                    <tr class="border-b border-border last:border-0">
                        <td class="px-5 py-4">
                            <p class="font-medium">{{ $sekolah->nama }}</p>
                            <p class="text-xs text-hitam2">{{ $sekolah->npsn }}</p>
                        </td>
                        <td class="px-5 py-4 text-hitam2">{{ $sekolah->kotaKabupaten() ?? '-' }}</td>
                        <td class="px-5 py-4 text-hitam2">{{ $jumlahSiswa }}/{{ $jumlahGuru }}</td>
                        <td class="px-5 py-4 text-hitam2">{{ $sekolah->created_at->format('d/m/Y') }}</td>
                        <td class="px-5 py-4">
                            <span
                                class="inline-block px-3 py-1 rounded-full text-xs font-medium {{ $badgeClass }}">{{ $badgeLabel }}</span>
                        </td>
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-2">
                                <div class="relative group">
                                    <button type="button"
                                        class="view-btn w-8 h-8 rounded-lg bg-teal/60 text-teal flex items-center justify-center hover:bg-teal/30"
                                        data-id="{{ $sekolah->id }}">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                            stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                    </button>
                                    <span
                                        class="pointer-events-none absolute -top-8 left-1/2 -translate-x-1/2 whitespace-nowrap bg-ink text-cream text-xs px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition">Lihat</span>
                                </div>

                                <div class="relative group">
                                    <button type="button"
                                        class="edit-btn w-8 h-8 rounded-lg bg-amber/60 text-amber flex items-center justify-center hover:bg-amber/30"
                                        data-id="{{ $sekolah->id }}">
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
                                        class="delete-btn w-8 h-8 rounded-lg bg-coral/60 text-coral flex items-center justify-center hover:bg-coral/30"
                                        data-id="{{ $sekolah->id }}" data-nama="{{ $sekolah->nama }}">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                            stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                        </svg>
                                    </button>
                                    <span
                                        class="pointer-events-none absolute -top-8 left-1/2 -translate-x-1/2 whitespace-nowrap bg-ink text-cream text-xs px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition">Nonaktifkan</span>
                                </div>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-5 py-8 text-center text-hitam2">Belum ada sekolah terdaftar.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $sekolahs->links() }}
    </div>

    <!-- Modal Lihat Detail -->
    <div id="view-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center px-4">
        <div class="modal-overlay absolute inset-0 bg-ink/40"></div>
        <div class="relative bg-putih rounded-2xl shadow-lg w-full max-w-md p-6">
            <p class="text-xs font-semibold tracking-widest text-coral uppercase mb-1">Detail Sekolah</p>
            <h2 id="view-nama" class="font-display font-semibold text-lg mb-4">-</h2>

            <div class="space-y-3 text-sm">
                <div class="flex justify-between"><span class="text-hitam2">NPSN</span><span id="view-npsn"
                        class="font-medium">-</span></div>
                <div class="flex justify-between"><span class="text-hitam2">Jenjang</span><span id="view-jenjang"
                        class="font-medium">-</span></div>
                <div class="flex justify-between"><span class="text-hitam2">Singkatan</span><span id="view-singkatan"
                        class="font-medium">-</span></div>
                <div class="flex justify-between"><span class="text-hitam2">Kota/Kabupaten</span><span id="view-kota"
                        class="font-medium">-</span></div>
                <div class="flex justify-between"><span class="text-hitam2">Alamat</span><span id="view-alamat"
                        class="font-medium text-right max-w-[60%]">-</span></div>
                <div class="flex justify-between"><span class="text-hitam2">Telepon</span><span id="view-telepon"
                        class="font-medium">-</span></div>
                <div class="flex justify-between"><span class="text-hitam2">Email</span><span id="view-email"
                        class="font-medium">-</span></div>
                <div class="flex justify-between"><span class="text-hitam2">Siswa/Guru</span><span
                        id="view-siswa-guru" class="font-medium">-</span></div>
            </div>

            <button type="button"
                class="modal-close-btn w-full mt-6 bg-cream2 text-ink font-medium text-sm rounded-lg py-2.5 hover:bg-border">
                Tutup
            </button>
        </div>
    </div>

    <!-- Modal Edit -->
    <div id="edit-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center px-4">
        <div class="modal-overlay absolute inset-0 bg-ink/40"></div>
        <div class="relative bg-putih rounded-2xl shadow-lg w-full max-w-md p-6">
            <p class="text-xs font-semibold tracking-widest text-coral uppercase mb-1">Edit Sekolah</p>
            <h2 class="font-display font-semibold text-lg mb-4">Ubah Data Sekolah</h2>

            <form id="edit-form" method="POST">
                @csrf
                @method('PUT')

                <div class="space-y-4">
                    <div>
                        <label class="text-xs font-semibold uppercase text-hitam2">Nama Sekolah</label>
                        <input type="text" name="nama" id="edit-nama" required
                            class="mt-1 w-full rounded-lg border-border bg-cream2 focus:border-coral focus:ring-coral text-sm">
                    </div>
                    <div>
                        <label class="text-xs font-semibold uppercase text-hitam2">NPSN</label>
                        <input type="text" name="npsn" id="edit-npsn" maxlength="8" required
                            class="mt-1 w-full rounded-lg border-border bg-cream2 focus:border-coral focus:ring-coral text-sm">
                    </div>
                    <div>
                        <label class="text-xs font-semibold uppercase text-hitam2">Nomor Telepon</label>
                        <input type="text" name="nomor_telepon" id="edit-telepon" required
                            class="mt-1 w-full rounded-lg border-border bg-cream2 focus:border-coral focus:ring-coral text-sm">
                    </div>
                    <div>
                        <label class="text-xs font-semibold uppercase text-hitam2">Alamat</label>
                        <input type="text" name="detail_alamat" id="edit-alamat" required
                            class="mt-1 w-full rounded-lg border-border bg-cream2 focus:border-coral focus:ring-coral text-sm">
                    </div>
                    <div>
                        <label class="text-xs font-semibold uppercase text-hitam2">Email</label>
                        <input type="email" name="email" id="edit-email" required
                            class="mt-1 w-full rounded-lg border-border bg-cream2 focus:border-coral focus:ring-coral text-sm">
                    </div>
                </div>

                <div class="flex justify-end gap-2 mt-6">
                    <button type="button"
                        class="modal-close-btn text-sm font-medium text-hitam2 px-4 py-2 hover:text-ink">Batal</button>
                    <button type="submit"
                        class="bg-ink text-cream text-sm font-medium rounded-lg px-4 py-2 hover:bg-hitam">Simpan
                        Perubahan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Form tersembunyi buat proses nonaktifkan -->
    <form id="delete-form" method="POST" class="hidden">
        @csrf
        @method('DELETE')
    </form>

    <script>
        $(function() {
            // Filter tab
            $('.filter-tab').on('click', function() {
                $('#filter-input').val($(this).data('filter'));
                $('#filter-form').submit();
            });

            // Search debounce
            let searchTimeout;
            $('#search-input').on('keyup', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(function() {
                    $('#filter-form').submit();
                }, 500);
            });

            // Modal lihat detail
            $('.view-btn').on('click', function() {
                const id = $(this).data('id');
                $.getJSON('/admin/sekolah/' + id).done(function(data) {
                    $('#view-nama').text(data.nama);
                    $('#view-npsn').text(data.npsn);
                    $('#view-jenjang').text(data.jenjang);
                    $('#view-singkatan').text(data.singkatan);
                    $('#view-kota').text(data.kota ?? '-');
                    $('#view-alamat').text(data.detail_alamat);
                    $('#view-telepon').text(data.nomor_telepon);
                    $('#view-email').text(data.email);
                    $('#view-siswa-guru').text(data.jumlah_siswa + ' / ' + data.jumlah_guru);
                    $('#view-modal').removeClass('hidden');
                });
            });

            // Modal edit
            $('.edit-btn').on('click', function() {
                const id = $(this).data('id');
                $.getJSON('/admin/sekolah/' + id).done(function(data) {
                    $('#edit-nama').val(data.nama);
                    $('#edit-npsn').val(data.npsn);
                    $('#edit-telepon').val(data.nomor_telepon);
                    $('#edit-alamat').val(data.detail_alamat);
                    $('#edit-email').val(data.email);
                    $('#edit-form').attr('action', '/admin/sekolah/' + id);
                    $('#edit-modal').removeClass('hidden');
                });
            });

            // Tutup modal
            $('.modal-close-btn, .modal-overlay').on('click', function() {
                $(this).closest('[id$="-modal"]').addClass('hidden');
            });

            // Hapus (nonaktifkan) pakai SweetAlert
            $('.delete-btn').on('click', function() {
                const id = $(this).data('id');
                const nama = $(this).data('nama');

                Swal.fire({
                    title: 'Nonaktifkan sekolah ini?',
                    text: 'Data ' + nama + ' akan dinonaktifkan, bukan dihapus permanen.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#E8674A',
                    cancelButtonColor: '#5B5A55',
                    confirmButtonText: 'Ya, nonaktifkan',
                    cancelButtonText: 'Batal',
                }).then(function(result) {
                    if (result.isConfirmed) {
                        $('#delete-form').attr('action', '/admin/sekolah/' + id).submit();
                    }
                });
            });
        });
    </script>
</x-app-layout>
