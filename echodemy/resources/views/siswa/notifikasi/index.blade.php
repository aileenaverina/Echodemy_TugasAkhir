<x-app-layout>
    <x-slot name="header">
        <p class="text-xs font-semibold tracking-widest text-coral uppercase mb-1">Notifikasi</p>
        <h1 class="font-display text-2xl font-semibold">Semua Notifikasi</h1>
    </x-slot>

    <div class="bg-putih rounded-2xl shadow-sm overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-border text-left text-[11px] tracking-widest uppercase text-hitam2">
                    <th class="px-5 py-3 font-semibold">Nama</th>
                    <th class="px-5 py-3 font-semibold text-right">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($notifs as $notif)
                    <tr class="border-b border-border last:border-0 {{ ! $notif->is_read ? 'bg-amber/5' : '' }}">
                        <td class="px-5 py-4">
                            <p class="font-semibold">{{ $notif->judul }}</p>
                            <p class="text-xs text-hitam2 mt-0.5">{{ $notif->deskripsi }}</p>
                        </td>
                        <td class="px-5 py-4 text-right">
                            <button type="button" class="toggle-notif-btn inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-medium border transition
                                {{ $notif->is_read ? 'border-teal text-teal bg-teal/10' : 'border-border text-hitam2 hover:bg-cream2' }}"
                                data-id="{{ $notif->id }}">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                                </svg>
                                <span class="btn-label">{{ $notif->is_read ? 'Done' : 'Tandai Selesai' }}</span>
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="2" class="px-5 py-10 text-center text-hitam2">Belum ada notifikasi.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <script>
    $(function () {
        $('.toggle-notif-btn').on('click', function () {
            const $btn = $(this);
            const id = $btn.data('id');

            $.post('{{ url('/siswa/notifikasi') }}/' + id + '/toggle', {
                _token: '{{ csrf_token() }}',
            }).done(function (data) {
                if (data.is_read) {
                    $btn.removeClass('border-border text-hitam2 hover:bg-cream2').addClass('border-teal text-teal bg-teal/10');
                    $btn.find('.btn-label').text('Done');
                    $btn.closest('tr').removeClass('bg-amber/5');
                } else {
                    $btn.removeClass('border-teal text-teal bg-teal/10').addClass('border-border text-hitam2 hover:bg-cream2');
                    $btn.find('.btn-label').text('Tandai Selesai');
                    $btn.closest('tr').addClass('bg-amber/5');
                }
            });
        });
    });
    </script>
</x-app-layout>