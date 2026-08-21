<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold tracking-widest text-hitam2 uppercase mb-1">Manajemen Mata Pelajaran</p>
                <h1 class="font-display text-2xl font-semibold">Kelola Mata Pelajaran</h1>
            </div>
            <button type="button" id="add-btn"
                class="bg-ink text-cream text-sm font-medium rounded-lg px-4 py-2.5 hover:bg-hitam flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                Tambah Mata Pelajaran
            </button>
        </div>
    </x-slot>

    @if (session('success'))
        <div class="bg-teal/10 text-teal text-sm rounded-lg px-4 py-3 mb-4">{{ session('success') }}</div>
    @endif

    <!-- Search -->
    <form method="GET" action="{{ route('admin.mata-pelajaran.index') }}" id="search-form" class="mb-5">
        <div class="relative max-w-md">
            <svg class="w-4 h-4 absolute left-3.5 top-1/2 -translate-y-1/2 text-hitam2" fill="none"
                viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M21 21l-4.35-4.35M17 10.5A6.5 6.5 0 114 10.5a6.5 6.5 0 0113 0z" />
            </svg>
            <input type="text" name="search" id="search-input" value="{{ $search }}"
                placeholder="Cari Nama Mata Pelajaran..."
                class="w-full pl-10 pr-4 py-2.5 rounded-lg border border-border bg-putih focus:border-coral focus:ring-coral text-sm">
        </div>
    </form>

    <!-- Table -->
    <div class="bg-putih rounded-2xl shadow-sm overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-border text-left text-[11px] tracking-widest uppercase text-hitam2">
                    <th class="px-5 py-3 font-semibold">Mata Pelajaran</th>
                    <th class="px-5 py-3 font-semibold">Dipakai Di</th>
                    <th class="px-5 py-3 font-semibold">Warna</th>
                    <th class="px-5 py-3 font-semibold">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($mataPelajarans as $mapel)
                    <tr class="border-b border-border last:border-0">
                        <td class="px-5 py-4 font-medium">{{ $mapel->nama }}</td>
                        <td class="px-5 py-4 text-hitam2">{{ $mapel->penugasan_kelas_count }} Kelas</td>
                        <td class="px-5 py-4">
                            <span class="inline-block w-4 h-4 rounded-full"
                                style="background-color: {{ $mapel->warna }}"></span>
                        </td>
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-2">
                                <div class="relative group">
                                    <button type="button"
                                        class="view-btn w-8 h-8 rounded-lg bg-teal/60 text-teal flex items-center justify-center hover:bg-teal/30"
                                        data-id="{{ $mapel->id }}">
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
                                        data-id="{{ $mapel->id }}">
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
                                        data-id="{{ $mapel->id }}" data-nama="{{ $mapel->nama }}">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                            stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                        </svg>
                                    </button>
                                    <span
                                        class="pointer-events-none absolute -top-8 left-1/2 -translate-x-1/2 whitespace-nowrap bg-ink text-cream text-xs px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition">Hapus</span>
                                </div>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-5 py-8 text-center text-hitam2">Belum ada mata pelajaran.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">
        {{ $mataPelajarans->links() }}
    </div>

    <!-- Modal Tambah -->
    <div id="add-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center px-4">
        <div class="modal-overlay absolute inset-0 bg-ink/40"></div>
        <div class="relative bg-putih rounded-2xl shadow-lg w-full max-w-md p-6">
            <p class="text-xs font-semibold tracking-widest text-hitam2 uppercase mb-1">Mata Pelajaran Baru</p>
            <h2 class="font-display font-semibold text-lg mb-4">Tambah Mata Pelajaran</h2>

            <form method="POST" action="{{ route('admin.mata-pelajaran.store') }}" enctype="multipart/form-data">
                @csrf

                <div class="space-y-4">
                    <div>
                        <label class="text-xs font-semibold uppercase text-hitam2">Nama Mata Pelajaran</label>
                        <input type="text" name="nama" placeholder="cth. Matematika" required
                            class="mt-1 w-full rounded-lg border-border bg-cream2 focus:border-coral focus:ring-coral text-sm">
                    </div>
                    <div>
                        <label class="text-xs font-semibold uppercase text-hitam2">Warna</label>
                        <input type="color" name="warna" value="#E8674A" required
                            class="mt-1 w-full h-10 rounded-lg border-border cursor-pointer">
                    </div>
                    <div>
                        <label class="text-xs font-semibold uppercase text-hitam2">File (Ikon/Kurikulum)</label>
                        <input type="file" name="file" accept=".pdf,.png,.jpg,.jpeg" required
                            class="mt-1 w-full text-sm text-hitam2 file:mr-3 file:py-2 file:px-3 file:rounded-lg file:border-0 file:bg-cream2 file:text-ink file:text-sm">
                    </div>
                </div>

                <div class="flex justify-end gap-2 mt-6">
                    <button type="button"
                        class="modal-close-btn text-sm font-medium text-hitam2 px-4 py-2 hover:text-ink">Batal</button>
                    <button type="submit"
                        class="bg-ink text-cream text-sm font-medium rounded-lg px-4 py-2 hover:bg-hitam">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Lihat Detail -->
    <div id="view-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center px-4">
        <div class="modal-overlay absolute inset-0 bg-ink/40"></div>
        <div class="relative bg-putih rounded-2xl shadow-lg w-full max-w-md p-6">
            <p class="text-xs font-semibold tracking-widest text-hitam2 uppercase mb-1">Detail Mata Pelajaran</p>
            <h2 id="view-nama" class="font-display font-semibold text-lg mb-4">-</h2>

            <div class="space-y-3 text-sm">
                <div class="flex justify-between items-center">
                    <span class="text-hitam2">Warna</span>
                    <span id="view-warna" class="inline-block w-5 h-5 rounded-full"></span>
                </div>
                <div class="flex justify-between"><span class="text-hitam2">Dipakai di</span><span id="view-dipakai"
                        class="font-medium">-</span></div>
                <div class="flex justify-between items-center">
                    <span class="text-hitam2">File</span>
                    <a id="view-file" href="#" target="_blank"
                        class="text-coral font-medium hover:underline">Lihat file</a>
                </div>
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
            <p class="text-xs font-semibold tracking-widest text-hitam2 uppercase mb-1">Edit Mata Pelajaran</p>
            <h2 class="font-display font-semibold text-lg mb-4">Ubah Data Mata Pelajaran</h2>

            <form id="edit-form" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="space-y-4">
                    <div>
                        <label class="text-xs font-semibold uppercase text-hitam2">Nama Mata Pelajaran</label>
                        <input type="text" name="nama" id="edit-nama" required
                            class="mt-1 w-full rounded-lg border-border bg-cream2 focus:border-coral focus:ring-coral text-sm">
                    </div>
                    <div>
                        <label class="text-xs font-semibold uppercase text-hitam2">Warna</label>
                        <input type="color" name="warna" id="edit-warna" required
                            class="mt-1 w-full h-10 rounded-lg border-border cursor-pointer">
                    </div>
                    <div>
                        <label class="text-xs font-semibold uppercase text-hitam2">Ganti File (opsional)</label>
                        <input type="file" name="file" accept=".pdf,.png,.jpg,.jpeg"
                            class="mt-1 w-full text-sm text-hitam2 file:mr-3 file:py-2 file:px-3 file:rounded-lg file:border-0 file:bg-cream2 file:text-ink file:text-sm">
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


    <!-- Form tersembunyi buat hapus -->
    <form id="delete-form" method="POST" class="hidden">
        @csrf
        @method('DELETE')
    </form>

    <script>
        $(function() {
            // Search auto-submit
            let searchTimeout;
            $('#search-input').on('keyup', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(function() {
                    $('#search-form').submit();
                }, 500);
            });

            // Tombol tambah
            $('#add-btn').on('click', function() {
                $('#add-modal').removeClass('hidden');
            });

            // Modal lihat detail
            $('.view-btn').on('click', function() {
                const id = $(this).data('id');
                $.getJSON('/admin/mata-pelajaran/' + id).done(function(data) {
                    $('#view-nama').text(data.nama);
                    $('#view-warna').css('background-color', data.warna);
                    $('#view-dipakai').text(data.dipakai_di + ' Kelas');
                    $('#view-file').attr('href', data.file ?? '#');
                    $('#view-modal').removeClass('hidden');
                });
            });

            // Modal edit
            $('.edit-btn').on('click', function() {
                const id = $(this).data('id');
                $.getJSON('/admin/mata-pelajaran/' + id).done(function(data) {
                    $('#edit-nama').val(data.nama);
                    $('#edit-warna').val(data.warna);
                    $('#edit-form').attr('action', '/admin/mata-pelajaran/' + id);
                    $('#edit-modal').removeClass('hidden');
                });
            });

            // Tutup modal
            $('.modal-close-btn, .modal-overlay').on('click', function() {
                $(this).closest('[id$="-modal"]').addClass('hidden');
            });

            // Hapus pakai SweetAlert
            $('.delete-btn').on('click', function() {
                const id = $(this).data('id');
                const nama = $(this).data('nama');

                Swal.fire({
                    title: 'Hapus mata pelajaran ini?',
                    text: nama + ' akan dihapus dari daftar.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#E8674A',
                    cancelButtonColor: '#5B5A55',
                    confirmButtonText: 'Ya, hapus',
                    cancelButtonText: 'Batal',
                }).then(function(result) {
                    if (result.isConfirmed) {
                        $('#delete-form').attr('action', '/admin/mata-pelajaran/' + id).submit();
                    }
                });
            });
        });
    </script>
</x-app-layout>
