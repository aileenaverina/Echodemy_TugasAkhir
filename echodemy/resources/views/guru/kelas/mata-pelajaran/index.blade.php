<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold tracking-widest text-coral uppercase mb-1">
                    <a href="{{ route('guru.kelas.show', $kelas) }}" class="hover:underline">{{ $kelas->nama }}</a> &middot; Manajemen Mata Pelajaran
                </p>
                <h1 class="font-display text-2xl font-semibold">Mata Pelajaran di {{ $kelas->nama }}</h1>
            </div>
            <button type="button" id="add-btn" class="bg-ink text-cream text-sm font-medium rounded-lg px-4 py-2.5 hover:bg-hitam flex items-center gap-2">
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

    <div class="bg-putih rounded-2xl shadow-sm overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-border text-left text-[11px] tracking-widest uppercase text-hitam2">
                    <th class="px-5 py-3 font-semibold">Mata Pelajaran</th>
                    <th class="px-5 py-3 font-semibold">Pengajar</th>
                    <th class="px-5 py-3 font-semibold">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($items as $item)
                    <tr class="border-b border-border last:border-0">
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-2">
                                <span class="inline-block w-3 h-3 rounded-full" style="background-color: {{ $item->mataPelajaran->warna }}"></span>
                                <span class="font-medium">{{ $item->mataPelajaran->nama }}</span>
                            </div>
                        </td>
                        <td class="px-5 py-4 text-hitam2">{{ $item->gurus->pluck('nama_lengkap')->join(', ') ?: '-' }}</td>
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-2">
                                <div class="relative group">
                                    <button type="button" class="edit-btn w-8 h-8 rounded-lg bg-amber/60 text-hitam flex items-center justify-center hover:bg-amber/70"
                                        data-id="{{ $item->id }}" data-guru-ids="{{ $item->gurus->pluck('id')->join(',') }}">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931z" />
                                        </svg>
                                    </button>
                                    <span class="pointer-events-none absolute -top-8 left-1/2 -translate-x-1/2 whitespace-nowrap bg-ink text-cream text-xs px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition">Edit Pengajar</span>
                                </div>
                                <div class="relative group">
                                    <button type="button" class="delete-btn w-8 h-8 rounded-lg bg-coral/60 text-hitam flex items-center justify-center hover:bg-coral/70"
                                        data-id="{{ $item->id }}" data-nama="{{ $item->mataPelajaran->nama }}">
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
                        <td colspan="3" class="px-5 py-8 text-center text-hitam2">Belum ada mata pelajaran.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Modal Tambah -->
    <div id="add-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center px-4 py-8 overflow-y-auto">
        <div class="modal-overlay absolute inset-0 bg-ink/40"></div>
        <div class="relative bg-putih rounded-2xl shadow-lg w-full max-w-md p-6 my-auto">
            <p class="text-xs font-semibold tracking-widest text-coral uppercase mb-1">Mata Pelajaran Baru</p>
            <h2 class="font-display font-semibold text-lg mb-4">Tambah ke Kelas</h2>

            <form method="POST" action="{{ route('guru.kelas.mata-pelajaran.store', $kelas) }}" class="space-y-4">
                @csrf

                <div>
                    <label class="text-xs font-semibold uppercase text-hitam2">Mata Pelajaran</label>
                    <select name="mata_pelajaran_id" required class="mt-2 w-full rounded-lg border-border bg-cream2 focus:border-coral focus:ring-coral text-sm">
                        <option value="">Pilih mata pelajaran</option>
                        @foreach ($mataPelajaranList as $mapel)
                            <option value="{{ $mapel->id }}">{{ $mapel->nama }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="text-xs font-semibold uppercase text-hitam2">Pengajar</label>
                    <div class="mt-2 grid grid-cols-2 gap-2 max-h-40 overflow-y-auto border border-border rounded-lg p-3">
                        @foreach ($guruList as $guru)
                            <label class="flex items-center gap-2 text-sm">
                                <input type="checkbox" name="guru_ids[]" value="{{ $guru->id }}" class="rounded border-border text-coral focus:ring-coral">
                                {{ $guru->nama_lengkap }}
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

    <!-- Modal Edit Pengajar -->
    <div id="edit-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center px-4 py-8 overflow-y-auto">
        <div class="modal-overlay absolute inset-0 bg-ink/40"></div>
        <div class="relative bg-putih rounded-2xl shadow-lg w-full max-w-md p-6 my-auto">
            <p class="text-xs font-semibold tracking-widest text-coral uppercase mb-1">Edit Pengajar</p>
            <h2 class="font-display font-semibold text-lg mb-4">Ubah Pengajar</h2>

            <form id="edit-form" method="POST" class="space-y-4">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-2 gap-2 max-h-48 overflow-y-auto border border-border rounded-lg p-3">
                    @foreach ($guruList as $guru)
                        <label class="flex items-center gap-2 text-sm">
                            <input type="checkbox" name="guru_ids[]" value="{{ $guru->id }}" class="edit-guru-checkbox rounded border-border text-coral focus:ring-coral">
                            {{ $guru->nama_lengkap }}
                        </label>
                    @endforeach
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
        $('#add-btn').on('click', function () {
            $('#add-modal').removeClass('hidden');
        });

        $('.edit-btn').on('click', function () {
            const id = $(this).data('id');
            const guruIds = String($(this).data('guru-ids')).split(',').filter(Boolean);

            $('.edit-guru-checkbox').prop('checked', false);
            guruIds.forEach(function (gid) {
                $('.edit-guru-checkbox[value="' + gid + '"]').prop('checked', true);
            });

            $('#edit-form').attr('action', '{{ url('/guru/kelas/'.$kelas->id.'/mata-pelajaran') }}/' + id);
            $('#edit-modal').removeClass('hidden');
        });

        $('.modal-close-btn, .modal-overlay').on('click', function () {
            $(this).closest('[id$="-modal"]').addClass('hidden');
        });

        $('.delete-btn').on('click', function () {
            const id = $(this).data('id');
            const nama = $(this).data('nama');

            Swal.fire({
                title: 'Hapus mata pelajaran ini?',
                text: nama + ' akan dihapus dari kelas ini.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#E8674A',
                cancelButtonColor: '#5B5A55',
                confirmButtonText: 'Ya, hapus',
                cancelButtonText: 'Batal',
            }).then(function (result) {
                if (result.isConfirmed) {
                    $('#delete-form').attr('action', '{{ url('/guru/kelas/'.$kelas->id.'/mata-pelajaran') }}/' + id).submit();
                }
            });
        });
    });
    </script>
</x-app-layout>