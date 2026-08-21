<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold tracking-widest text-hitam2 uppercase mb-1">Verifikasi dan Akses</p>
                <h1 class="font-display text-2xl font-semibold">Daftar Akun Admin</h1>
            </div>
            <button type="button" id="add-btn"
                class="bg-ink text-cream text-sm font-medium rounded-lg px-4 py-2.5 hover:bg-hitam flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                Tambah Akun Admin
            </button>
        </div>
    </x-slot>

    @if (session('success'))
        <div class="bg-teal/10 text-teal text-sm rounded-lg px-4 py-3 mb-4">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="bg-coral/10 text-coral text-sm rounded-lg px-4 py-3 mb-4">{{ session('error') }}</div>
    @endif

    <!-- Akun Admin -->
    <h2 class="font-body text-md font-semibold mb-3">Akun Administrator Platform</h2>
    <div class="bg-putih rounded-2xl shadow-sm overflow-hidden mb-8">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-border text-left text-[11px] tracking-widest uppercase text-hitam2">
                    <th class="px-5 py-3 font-semibold">Nama</th>
                    <th class="px-5 py-3 font-semibold">Email</th>
                    <th class="px-5 py-3 font-semibold">Aktif Terakhir</th>
                    <th class="px-5 py-3 font-semibold">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($admins as $admin)
                    <tr class="border-b border-border last:border-0">
                        <td class="px-5 py-4 font-medium">{{ $admin->nama_lengkap ?? '-' }}</td>
                        <td class="px-5 py-4 text-hitam2">{{ $admin->email }}</td>
                        <td class="px-5 py-4 text-hitam2">{{ $admin->last_login_at?->format('d/m/y H.i.s') ?? '-' }}
                        </td>
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-2">
                                <div class="relative group">
                                    <button type="button"
                                        class="view-btn w-8 h-8 rounded-lg bg-teal/60 text-teal flex items-center justify-center hover:bg-teal/30"
                                        data-id="{{ $admin->id }}">
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
                                        class="delete-btn w-8 h-8 rounded-lg bg-coral/60 text-coral flex items-center justify-center hover:bg-coral/30"
                                        data-id="{{ $admin->id }}"
                                        data-nama="{{ $admin->nama_lengkap ?? $admin->email }}">
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
                        <td colspan="4" class="px-5 py-8 text-center text-hitam2">Belum ada akun admin lain.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Riwayat Keputusan -->
    <h2 class="font-body text-md font-semibold mb-3">Riwayat Keputusan Verifikasi Sekolah</h2>
    <div class="bg-putih rounded-2xl shadow-sm overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-border text-left text-[11px] tracking-widest uppercase text-hitam2">
                    <th class="px-5 py-3 font-semibold">Sekolah</th>
                    <th class="px-5 py-3 font-semibold">Keputusan</th>
                    <th class="px-5 py-3 font-semibold">Oleh</th>
                    <th class="px-5 py-3 font-semibold">Waktu</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($riwayat as $log)
                    @php
                        [$label, $badgeClass] = match (true) {
                            str_starts_with($log->deskripsi, 'Memverifikasi') => [
                                'Diverifikasi',
                                'bg-teal/60 text-hitam2',
                            ],
                            str_starts_with($log->deskripsi, 'Menolak') => ['Ditolak', 'bg-coral/60 text-hitam2'],
                            str_starts_with($log->deskripsi, 'Menonaktifkan') => [
                                'Dinonaktifkan',
                                'bg-amber/60 text-hitam2',
                            ],
                            default => ['-', 'bg-cream2 text-hitam2'],
                        };
                    @endphp
                    <tr class="border-b border-border last:border-0">
                        <td class="px-5 py-4 font-medium">{{ $log->sekolah->nama ?? '-' }}</td>
                        <td class="px-5 py-4">
                            <span
                                class="inline-block px-3 py-1 rounded-full text-xs font-medium {{ $badgeClass }}">{{ $label }}</span>
                        </td>
                        <td class="px-5 py-4 text-hitam2">{{ $log->user->nama_lengkap ?? '-' }}</td>
                        <td class="px-5 py-4 text-hitam2">{{ $log->created_at->format('d/m/y H.i.s') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-5 py-8 text-center text-hitam2">Belum ada riwayat keputusan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">
        {{ $riwayat->links() }}
    </div>

    <!-- Modal Tambah -->
    <div id="add-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center px-4">
        <div class="modal-overlay absolute inset-0 bg-ink/40"></div>
        <div class="relative bg-putih rounded-2xl shadow-lg w-full max-w-md p-6">
            <p class="text-xs font-semibold tracking-widest text-hitam2 uppercase mb-1">Akun Baru</p>
            <h2 class="font-display font-semibold text-lg mb-4">Tambah Akun Admin</h2>

            <form method="POST" action="{{ route('admin.akun.store') }}">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="text-xs font-semibold uppercase text-hitam2">Nama Lengkap</label>
                        <input type="text" name="nama_lengkap" required
                            class="mt-1 w-full rounded-lg border-border bg-cream2 focus:border-coral focus:ring-coral text-sm">
                    </div>
                    <div>
                        <label class="text-xs font-semibold uppercase text-hitam2">Email</label>
                        <input type="email" name="email" required
                            class="mt-1 w-full rounded-lg border-border bg-cream2 focus:border-coral focus:ring-coral text-sm">
                    </div>
                    <div>
                        <label class="text-xs font-semibold uppercase text-hitam2">Password</label>
                        <input type="password" name="password" minlength="8" required
                            class="mt-1 w-full rounded-lg border-border bg-cream2 focus:border-coral focus:ring-coral text-sm">
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

    <!-- Modal Lihat -->
    <div id="view-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center px-4">
        <div class="modal-overlay absolute inset-0 bg-ink/40"></div>
        <div class="relative bg-putih rounded-2xl shadow-lg w-full max-w-md p-6">
            <p class="text-xs font-semibold tracking-widest text-coral uppercase mb-1">Detail Akun</p>
            <h2 id="view-nama" class="font-display font-semibold text-lg mb-4">-</h2>

            <div class="space-y-3 text-sm">
                <div class="flex justify-between"><span class="text-hitam2">Kode User</span><span id="view-kode"
                        class="font-medium">-</span></div>
                <div class="flex justify-between"><span class="text-hitam2">Email</span><span id="view-email"
                        class="font-medium">-</span></div>
                <div class="flex justify-between"><span class="text-hitam2">Terdaftar</span><span id="view-created"
                        class="font-medium">-</span></div>
                <div class="flex justify-between"><span class="text-hitam2">Aktif Terakhir</span><span
                        id="view-login" class="font-medium">-</span></div>
            </div>

            <button type="button"
                class="modal-close-btn w-full mt-6 bg-cream2 text-ink font-medium text-sm rounded-lg py-2.5 hover:bg-border">
                Tutup
            </button>
        </div>
    </div>

    <!-- Form tersembunyi hapus -->
    <form id="delete-form" method="POST" class="hidden">
        @csrf
        @method('DELETE')
    </form>

    <script>
        $(function() {
            $('#add-btn').on('click', function() {
                $('#add-modal').removeClass('hidden');
            });

            $('.view-btn').on('click', function() {
                const id = $(this).data('id');
                $.getJSON('/admin/akun/' + id).done(function(data) {
                    $('#view-nama').text(data.nama_lengkap ?? '-');
                    $('#view-kode').text(data.kode_user);
                    $('#view-email').text(data.email);
                    $('#view-created').text(data.created_at);
                    $('#view-login').text(data.last_login_at);
                    $('#view-modal').removeClass('hidden');
                });
            });

            $('.modal-close-btn, .modal-overlay').on('click', function() {
                $(this).closest('[id$="-modal"]').addClass('hidden');
            });

            $('.delete-btn').on('click', function() {
                const id = $(this).data('id');
                const nama = $(this).data('nama');

                Swal.fire({
                    title: 'Hapus akun ini?',
                    text: nama + ' tidak akan bisa login lagi setelah dihapus.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#E8674A',
                    cancelButtonColor: '#5B5A55',
                    confirmButtonText: 'Ya, hapus',
                    cancelButtonText: 'Batal',
                }).then(function(result) {
                    if (result.isConfirmed) {
                        $('#delete-form').attr('action', '/admin/akun/' + id).submit();
                    }
                });
            });
        });
    </script>
</x-app-layout>
