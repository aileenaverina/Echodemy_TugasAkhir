<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold tracking-widest text-coral uppercase mb-1">Manajemen Data Sekolah</p>
                <h1 class="font-display text-2xl font-semibold">Daftar Kelas</h1>
            </div>

            @if ($guruList->isEmpty())
                <div class="relative group">
                    <button type="button" disabled
                        class="bg-cream2 text-hitam2 text-sm font-medium rounded-lg px-4 py-2.5 flex items-center gap-2 cursor-not-allowed">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                        Tambah Kelas
                    </button>
                    <span class="pointer-events-none absolute -bottom-9 right-0 whitespace-nowrap bg-ink text-cream text-xs px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition">
                        Tambahkan guru terlebih dahulu
                    </span>
                </div>
            @else
                <button type="button" id="add-btn" class="bg-ink text-cream text-sm font-medium rounded-lg px-4 py-2.5 hover:bg-hitam flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    Tambah Kelas
                </button>
            @endif
        </div>
    </x-slot>

    @if (session('success'))
        <div class="bg-teal/10 text-teal text-sm rounded-lg px-4 py-3 mb-4">{{ session('success') }}</div>
    @endif

    @if ($guruList->isEmpty())
        <div class="bg-amber/10 text-hitam text-sm rounded-lg px-4 py-3 mb-4 flex items-center gap-2">
            <svg class="w-4 h-4 text-amber shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
            </svg>
            Tambahkan minimal 1 guru terlebih dahulu sebelum bisa membuat kelas.
        </div>
    @endif

    <form method="GET" action="{{ route('sekolah.kelas.index') }}" id="filter-form" class="flex flex-wrap items-center gap-3 mb-5">
        <input type="hidden" name="filter" id="filter-input" value="{{ $filter }}">

        <div class="relative flex-1 min-w-[240px]">
            <svg class="w-4 h-4 absolute left-3.5 top-1/2 -translate-y-1/2 text-hitam2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 10.5A6.5 6.5 0 114 10.5a6.5 6.5 0 0113 0z" />
            </svg>
            <input type="text" name="search" id="search-input" value="{{ $search }}" placeholder="Cari Nama Kelas, Wali Kelas..."
                class="w-full pl-10 pr-4 py-2.5 rounded-lg border border-border bg-putih focus:border-coral focus:ring-coral text-sm">
        </div>

        <div class="flex items-center gap-2">
            @foreach (['aktif' => 'Aktif', 'selesai' => 'Selesai'] as $key => $label)
                <button type="button" data-filter="{{ $key }}"
                    class="filter-tab px-4 py-2 rounded-full text-sm font-medium transition {{ $filter === $key ? 'bg-ink text-cream' : 'bg-putih text-hitam2 border border-border hover:bg-cream2' }}">
                    {{ $label }}
                </button>
            @endforeach
        </div>
    </form>

    <div class="bg-putih rounded-2xl shadow-sm overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-border text-left text-[11px] tracking-widest uppercase text-hitam2">
                    <th class="px-5 py-3 font-semibold">Kelas</th>
                    <th class="px-5 py-3 font-semibold">Wali Kelas</th>
                    <th class="px-5 py-3 font-semibold">Siswa</th>
                    <th class="px-5 py-3 font-semibold">Mapel</th>
                    <th class="px-5 py-3 font-semibold">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($kelas as $item)
                    @php
                        $mapelNames = $item->penugasanMataPelajaran->pluck('mataPelajaran.nama')->filter();
                        $mapelDisplay = $mapelNames->take(2)->join(', ');
                        if ($mapelNames->count() > 2) {
                            $mapelDisplay .= ', dan ' . ($mapelNames->count() - 2) . ' lainnya';
                        }
                    @endphp
                    <tr class="border-b border-border last:border-0">
                        <td class="px-5 py-4 font-medium">{{ $item->nama }}</td>
                        <td class="px-5 py-4 text-hitam2">{{ $item->waliKelas->first()?->nama_lengkap ?? '-' }}</td>
                        <td class="px-5 py-4 text-hitam2">{{ $item->jumlah_siswa }}</td>
                        <td class="px-5 py-4 text-hitam2">{{ $mapelDisplay ?: '-' }}</td>
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-2">
                                <div class="relative group">
                                    <button type="button" class="edit-btn w-8 h-8 rounded-lg bg-amber/60 text-hitam flex items-center justify-center hover:bg-amber/70" data-id="{{ $item->id }}">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931z" />
                                        </svg>
                                    </button>
                                    <span class="pointer-events-none absolute -top-8 left-1/2 -translate-x-1/2 whitespace-nowrap bg-ink text-cream text-xs px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition">Edit</span>
                                </div>
                                <div class="relative group">
                                    <button type="button" class="delete-btn w-8 h-8 rounded-lg bg-coral/60 text-hitam flex items-center justify-center hover:bg-coral/70"
                                        data-id="{{ $item->id }}" data-nama="{{ $item->nama }}">
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
                        <td colspan="5" class="px-5 py-8 text-center text-hitam2">Belum ada kelas.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $kelas->links() }}</div>

    <div id="add-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center px-4 py-8 overflow-y-auto">
        <div class="modal-overlay absolute inset-0 bg-ink/40"></div>
        <div class="relative bg-putih rounded-2xl shadow-lg w-full max-w-md p-6 my-auto">
            <p class="text-xs font-semibold tracking-widest text-coral uppercase mb-1">Kelas Baru</p>
            <h2 class="font-display font-semibold text-lg mb-4">Tambah Kelas</h2>

            <form method="POST" action="{{ route('sekolah.kelas.store') }}" class="space-y-4">
                @csrf

                <div>
                    <label class="text-xs font-semibold uppercase text-hitam2">Nama Kelas</label>
                    <input type="text" name="nama" placeholder="cth. Kelas 1A" required
                        class="mt-2 w-full rounded-lg border-border bg-cream2 focus:border-coral focus:ring-coral text-sm">
                </div>

                <div>
                    <label class="text-xs font-semibold uppercase text-hitam2">Tahun Ajaran</label>
                    <input type="text" name="tahun_ajaran" placeholder="cth. 2025/2026" required
                        class="mt-2 w-full rounded-lg border-border bg-cream2 focus:border-coral focus:ring-coral text-sm">
                </div>

                <div>
                    <label class="text-xs font-semibold uppercase text-hitam2">Wali Kelas</label>
                    <select name="wali_kelas_id" required class="mt-2 w-full rounded-lg border-border bg-cream2 focus:border-coral focus:ring-coral text-sm">
                        <option value="" disabled selected>Pilih wali kelas</option>
                        @foreach ($guruList as $guru)
                            <option value="{{ $guru->id }}">{{ $guru->nama_lengkap }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="text-xs font-semibold uppercase text-hitam2">Mata Pelajaran</label>
                    <div class="mt-2 grid grid-cols-2 gap-2 max-h-40 overflow-y-auto border border-border rounded-lg p-3">
                        @foreach ($mataPelajaranList as $mapel)
                            <label class="flex items-center gap-2 text-sm">
                                <input type="checkbox" name="mata_pelajaran_ids[]" value="{{ $mapel->id }}" class="rounded border-border text-coral focus:ring-coral">
                                {{ $mapel->nama }}
                            </label>
                        @endforeach
                    </div>
                </div>

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
            <p class="text-xs font-semibold tracking-widest text-coral uppercase mb-1">Edit Kelas</p>
            <h2 class="font-display font-semibold text-lg mb-4">Ubah Data Kelas</h2>

            <form id="edit-form" method="POST" class="space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label class="text-xs font-semibold uppercase text-hitam2">Nama Kelas</label>
                    <input type="text" name="nama" id="edit-nama" required
                        class="mt-2 w-full rounded-lg border-border bg-cream2 focus:border-coral focus:ring-coral text-sm">
                </div>

                <div>
                    <label class="text-xs font-semibold uppercase text-hitam2">Tahun Ajaran</label>
                    <input type="text" name="tahun_ajaran" id="edit-tahun-ajaran" required
                        class="mt-2 w-full rounded-lg border-border bg-cream2 focus:border-coral focus:ring-coral text-sm">
                </div>

                <div>
                    <label class="text-xs font-semibold uppercase text-hitam2">Wali Kelas</label>
                    <select name="wali_kelas_id" id="edit-wali-kelas" required class="mt-2 w-full rounded-lg border-border bg-cream2 focus:border-coral focus:ring-coral text-sm">
                        <option value="" disabled>Pilih wali kelas</option>
                        @foreach ($guruList as $guru)
                            <option value="{{ $guru->id }}">{{ $guru->nama_lengkap }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="text-xs font-semibold uppercase text-hitam2">Mata Pelajaran</label>
                    <div id="edit-mapel-list" class="mt-2 grid grid-cols-2 gap-2 max-h-40 overflow-y-auto border border-border rounded-lg p-3">
                        @foreach ($mataPelajaranList as $mapel)
                            <label class="flex items-center gap-2 text-sm">
                                <input type="checkbox" name="mata_pelajaran_ids[]" value="{{ $mapel->id }}" class="edit-mapel-checkbox rounded border-border text-coral focus:ring-coral">
                                {{ $mapel->nama }}
                            </label>
                        @endforeach
                    </div>
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
        $('.filter-tab').on('click', function () {
            $('#filter-input').val($(this).data('filter'));
            $('#filter-form').submit();
        });

        let searchTimeout;
        $('#search-input').on('keyup', function () {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(function () { $('#filter-form').submit(); }, 500);
        });

        $('#add-btn').on('click', function () {
            $('#add-modal').removeClass('hidden');
        });

        $('.edit-btn').on('click', function () {
            const id = $(this).data('id');
            $.getJSON('/sekolah/kelas/' + id).done(function (data) {
                $('#edit-nama').val(data.nama);
                $('#edit-tahun-ajaran').val(data.tahun_ajaran);
                $('#edit-wali-kelas').val(data.wali_kelas_id ?? '');

                $('.edit-mapel-checkbox').prop('checked', false);
                data.mata_pelajaran_ids.forEach(function (mapelId) {
                    $('.edit-mapel-checkbox[value="' + mapelId + '"]').prop('checked', true);
                });

                $('#edit-form').attr('action', '/sekolah/kelas/' + id);
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
                title: 'Hapus kelas ini?',
                text: nama + ' akan dihapus dari daftar.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#E8674A',
                cancelButtonColor: '#5B5A55',
                confirmButtonText: 'Ya, hapus',
                cancelButtonText: 'Batal',
            }).then(function (result) {
                if (result.isConfirmed) {
                    $('#delete-form').attr('action', '/sekolah/kelas/' + id).submit();
                }
            });
        });
    });
    </script>
</x-app-layout>