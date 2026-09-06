<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold tracking-widest text-coral uppercase mb-1">
                    <a href="{{ route('guru.kelas.show', $kelas) }}" class="hover:underline">{{ $kelas->nama }}</a> &middot; Manajemen Siswa
                </p>
                <h1 class="font-display text-2xl font-semibold">Siswa di {{ $kelas->nama }}</h1>
            </div>
            <button type="button" id="add-btn" class="bg-ink text-cream text-sm font-medium rounded-lg px-4 py-2.5 hover:bg-hitam flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                Tambah Siswa
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
                    <th class="px-5 py-3 font-semibold">Nama</th>
                    <th class="px-5 py-3 font-semibold">NIS</th>
                    <th class="px-5 py-3 font-semibold">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($siswaKelas as $siswa)
                    <tr class="border-b border-border last:border-0">
                        <td class="px-5 py-4 font-medium">{{ $siswa->nama_lengkap }}</td>
                        <td class="px-5 py-4 text-hitam2">{{ $siswa->detailUser?->nis ?? '-' }}</td>
                        <td class="px-5 py-4">
                            <div class="relative group inline-block">
                                <button type="button" class="delete-btn w-8 h-8 rounded-lg bg-coral/60 text-hitam flex items-center justify-center hover:bg-coral/70"
                                    data-id="{{ $siswa->id }}" data-nama="{{ $siswa->nama_lengkap }}">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                    </svg>
                                </button>
                                <span class="pointer-events-none absolute -top-8 left-1/2 -translate-x-1/2 whitespace-nowrap bg-ink text-cream text-xs px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition">Keluarkan</span>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="px-5 py-8 text-center text-hitam2">Belum ada siswa di kelas ini.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Modal Tambah -->
    <div id="add-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center px-4 py-8 overflow-y-auto">
        <div class="modal-overlay absolute inset-0 bg-ink/40"></div>
        <div class="relative bg-putih rounded-2xl shadow-lg w-full max-w-md p-6 my-auto">
            <p class="text-xs font-semibold tracking-widest text-coral uppercase mb-1">Siswa Baru</p>
            <h2 class="font-display font-semibold text-lg mb-4">Tambahkan ke Kelas</h2>

            <form method="POST" action="{{ route('guru.kelas.siswa.store', $kelas) }}" class="space-y-4">
                @csrf

                <div>
                    <label class="text-xs font-semibold uppercase text-hitam2">Pilih Siswa</label>
                    <select name="siswa_id" required class="mt-2 w-full rounded-lg border-border bg-cream2 focus:border-coral focus:ring-coral text-sm">
                        <option value="">Pilih siswa</option>
                        @foreach ($siswaTersedia as $siswa)
                            <option value="{{ $siswa->id }}">{{ $siswa->nama_lengkap }} ({{ $siswa->detailUser?->nis ?? '-' }})</option>
                        @endforeach
                    </select>
                    @if ($siswaTersedia->isEmpty())
                        <p class="text-xs text-hitam2 mt-2">Tidak ada siswa yang tersedia — semua siswa sudah masuk kelas aktif lain.</p>
                    @endif
                </div>

                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" class="modal-close-btn text-sm font-medium text-hitam2 px-4 py-2 hover:text-ink">Batal</button>
                    <button type="submit" class="bg-ink text-cream text-sm font-medium rounded-lg px-4 py-2 hover:bg-hitam">Simpan</button>
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

        $('.modal-close-btn, .modal-overlay').on('click', function () {
            $(this).closest('[id$="-modal"]').addClass('hidden');
        });

        $('.delete-btn').on('click', function () {
            const id = $(this).data('id');
            const nama = $(this).data('nama');

            Swal.fire({
                title: 'Keluarkan siswa ini?',
                text: nama + ' akan dikeluarkan dari kelas ini.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#E8674A',
                cancelButtonColor: '#5B5A55',
                confirmButtonText: 'Ya, keluarkan',
                cancelButtonText: 'Batal',
            }).then(function (result) {
                if (result.isConfirmed) {
                    $('#delete-form').attr('action', '{{ url('/guru/kelas/'.$kelas->id.'/siswa') }}/' + id).submit();
                }
            });
        });
    });
    </script>
</x-app-layout>