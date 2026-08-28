<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold tracking-widest text-coral uppercase mb-1">Manajemen Data Sekolah</p>
                <h1 class="font-display text-2xl font-semibold">Daftar Guru</h1>
            </div>
            <button type="button" id="add-btn" class="bg-ink text-cream text-sm font-medium rounded-lg px-4 py-2.5 hover:bg-hitam flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                Tambah Guru
            </button>
        </div>
    </x-slot>

    @if (session('success'))
        <div class="bg-teal/10 text-teal text-sm rounded-lg px-4 py-3 mb-4">{{ session('success') }}</div>
    @endif

    <form method="GET" action="{{ route('sekolah.guru.index') }}" id="search-form" class="mb-5">
        <div class="relative max-w-md">
            <svg class="w-4 h-4 absolute left-3.5 top-1/2 -translate-y-1/2 text-hitam2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 10.5A6.5 6.5 0 114 10.5a6.5 6.5 0 0113 0z" />
            </svg>
            <input type="text" name="search" id="search-input" value="{{ $search }}" placeholder="Cari Nama, Email..."
                class="w-full pl-10 pr-4 py-2.5 rounded-lg border border-border bg-putih focus:border-coral focus:ring-coral text-sm">
        </div>
    </form>

    <div class="bg-putih rounded-2xl shadow-sm overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-border text-left text-[11px] tracking-widest uppercase text-hitam2">
                    <th class="px-5 py-3 font-semibold">Nama</th>
                    <th class="px-5 py-3 font-semibold">Email</th>
                    <th class="px-5 py-3 font-semibold">Kelas Diampu</th>
                    <th class="px-5 py-3 font-semibold">Status</th>
                    <th class="px-5 py-3 font-semibold">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($guruList as $guru)
                    @php
                        $kelasWali = $guru->kelasSebagaiWali->first();
                        $kelasDiampu = $kelasWali
                            ? 'Wali Kelas ' . $kelasWali->nama
                            : ($guru->mataPelajaranKelasDiajar->pluck('mataPelajaran.nama')->unique()->first() ?? '-');
                        $status = $guru->detailUser?->status ?? 'aktif';
                    @endphp
                    <tr class="border-b border-border last:border-0">
                        <td class="px-5 py-4">
                            <p class="font-medium">{{ $guru->nama_lengkap }}</p>
                            <p class="text-xs text-hitam2">{{ $guru->kode_user }}</p>
                        </td>
                        <td class="px-5 py-4 text-hitam2">{{ $guru->email }}</td>
                        <td class="px-5 py-4 text-hitam2">{{ $kelasDiampu }}</td>
                        <td class="px-5 py-4">
                            <span class="inline-block px-3 py-1 rounded-full text-xs font-medium {{ $status === 'aktif' ? 'bg-teal/60 text-hitam' : 'bg-coral/60 text-hitam' }}">
                                {{ ucfirst($status) }}
                            </span>
                        </td>
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-2">
                                <div class="relative group">
                                    <button type="button" class="edit-btn w-8 h-8 rounded-lg bg-amber/60 text-hitam flex items-center justify-center hover:bg-amber/70" data-id="{{ $guru->id }}">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931z" />
                                        </svg>
                                    </button>
                                    <span class="pointer-events-none absolute -top-8 left-1/2 -translate-x-1/2 whitespace-nowrap bg-ink text-cream text-xs px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition">Edit</span>
                                </div>
                                <div class="relative group">
                                    <button type="button" class="delete-btn w-8 h-8 rounded-lg bg-coral/60 text-hitam flex items-center justify-center hover:bg-coral/70"
                                        data-id="{{ $guru->id }}" data-nama="{{ $guru->nama_lengkap }}">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                        </svg>
                                    </button>
                                    <span class="pointer-events-none absolute -top-8 left-1/2 -translate-x-1/2 whitespace-nowrap bg-ink text-cream text-xs px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition">Hapus</span>
                                </div>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-5 py-8 text-center text-hitam2">Belum ada guru terdaftar.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $guruList->links() }}</div>

    <div id="add-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center px-4 py-8 overflow-y-auto">
        <div class="modal-overlay absolute inset-0 bg-ink/40"></div>
        <div class="relative bg-putih rounded-2xl shadow-lg w-full max-w-md p-6 my-auto">
            <p class="text-xs font-semibold tracking-widest text-coral uppercase mb-1">Guru Baru</p>
            <h2 class="font-display font-semibold text-lg mb-4">Tambah Guru</h2>

            <form method="POST" action="{{ route('sekolah.guru.store') }}" class="space-y-4">
                @csrf

                <div>
                    <label class="text-xs font-semibold uppercase text-hitam2">Nama Lengkap</label>
                    <input type="text" name="nama_lengkap" required
                        class="mt-2 w-full rounded-lg border-border bg-cream2 focus:border-coral focus:ring-coral text-sm">
                </div>

                <div>
                    <label class="text-xs font-semibold uppercase text-hitam2">Email</label>
                    <input type="email" name="email" required
                        class="mt-2 w-full rounded-lg border-border bg-cream2 focus:border-coral focus:ring-coral text-sm">
                </div>

                <div>
                    <label class="text-xs font-semibold uppercase text-hitam2">Tahun Masuk</label>
                    <input type="number" name="tahun_masuk" placeholder="cth. 2025" min="2000" max="2100" required
                        class="mt-2 w-full rounded-lg border-border bg-cream2 focus:border-coral focus:ring-coral text-sm">
                </div>

                <div>
                    <label class="text-xs font-semibold uppercase text-hitam2">Nomor Telepon</label>
                    <input type="text" name="nomor_telepon"
                        class="mt-2 w-full rounded-lg border-border bg-cream2 focus:border-coral focus:ring-coral text-sm">
                </div>

                <p class="text-xs text-hitam2">Akun login akan dikirim ke email guru setelah disimpan.</p>

                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" class="modal-close-btn text-sm font-medium text-hitam2 px-4 py-2 hover:text-ink">Batal</button>
                    <button type="submit" class="bg-ink text-cream text-sm font-medium rounded-lg px-4 py-2 hover:bg-hitam">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <div id="edit-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center px-4 py-8 overflow-y-auto">
        <div class="modal-overlay absolute inset-0 bg-ink/40"></div>
        <div class="relative bg-putih rounded-2xl shadow-lg w-full max-w-md p-6 my-auto">
            <p class="text-xs font-semibold tracking-widest text-coral uppercase mb-1">Edit Guru</p>
            <h2 class="font-display font-semibold text-lg mb-4">Ubah Data Guru</h2>

            <form id="edit-form" method="POST" class="space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label class="text-xs font-semibold uppercase text-hitam2">Nama Lengkap</label>
                    <input type="text" name="nama_lengkap" id="edit-nama" required
                        class="mt-2 w-full rounded-lg border-border bg-cream2 focus:border-coral focus:ring-coral text-sm">
                </div>

                <div>
                    <label class="text-xs font-semibold uppercase text-hitam2">Email</label>
                    <input type="email" name="email" id="edit-email" required
                        class="mt-2 w-full rounded-lg border-border bg-cream2 focus:border-coral focus:ring-coral text-sm">
                </div>

                <div>
                    <label class="text-xs font-semibold uppercase text-hitam2">Nomor Telepon</label>
                    <input type="text" name="nomor_telepon" id="edit-telepon"
                        class="mt-2 w-full rounded-lg border-border bg-cream2 focus:border-coral focus:ring-coral text-sm">
                </div>

                <div>
                    <label class="text-xs font-semibold uppercase text-hitam2">Status</label>
                    <select name="status" id="edit-status" class="mt-2 w-full rounded-lg border-border bg-cream2 focus:border-coral focus:ring-coral text-sm">
                        <option value="aktif">Aktif</option>
                        <option value="nonaktif">Nonaktif</option>
                    </select>
                </div>

                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" class="modal-close-btn text-sm font-medium text-hitam2 px-4 py-2 hover:text-ink">Batal</button>
                    <button type="submit" class="bg-ink text-cream text-sm font-medium rounded-lg px-4 py-2 hover:bg-hitam">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>

    <form id="delete-form" method="POST" class="hidden">
        @csrf
        @method('DELETE')
    </form>

    <script>
    $(function () {
        let searchTimeout;
        $('#search-input').on('keyup', function () {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(function () { $('#search-form').submit(); }, 500);
        });

        $('#add-btn').on('click', function () {
            $('#add-modal').removeClass('hidden');
        });

        $('.edit-btn').on('click', function () {
            const id = $(this).data('id');
            $.getJSON('/sekolah/guru/' + id).done(function (data) {
                $('#edit-nama').val(data.nama_lengkap);
                $('#edit-email').val(data.email);
                $('#edit-telepon').val(data.nomor_telepon);
                $('#edit-status').val(data.status);
                $('#edit-form').attr('action', '/sekolah/guru/' + id);
                $('#edit-modal').removeClass('hidden');
            });
        });

        $('.modal-close-btn, .modal-overlay').on('click', function () {
            $(this).closest('[id$="-modal"]').addClass('hidden');
        });

        $('.delete-btn').on('click', function () {
            const id = $(this).data('id');
            const nama = $(this).data('nama');

            Swal.fire({
                title: 'Hapus akun guru ini?',
                text: nama + ' tidak akan bisa login lagi setelah dihapus.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#E8674A',
                cancelButtonColor: '#5B5A55',
                confirmButtonText: 'Ya, hapus',
                cancelButtonText: 'Batal',
            }).then(function (result) {
                if (result.isConfirmed) {
                    $('#delete-form').attr('action', '/sekolah/guru/' + id).submit();
                }
            });
        });
    });
    </script>
</x-app-layout>