<x-app-layout>
    <x-slot name="header">
        <h1 class="font-display text-xl font-semibold">Verifikasi Sekolah</h1>
    </x-slot>

    @if (session('success'))
        <div class="bg-teal/10 text-teal text-sm rounded-lg px-4 py-3 mb-4">{{ session('success') }}</div>
    @endif

    <div class="space-y-3">
        @forelse ($pendingSekolahs as $sekolah)
            <div class="bg-white rounded-xl shadow-sm p-5 flex items-center justify-between gap-4">
                <div>
                    <p class="font-medium">{{ $sekolah->nama }}</p>
                    <p class="text-sm text-ink/50">NPSN {{ $sekolah->npsn }} &middot; {{ $sekolah->jenjang }} &middot; {{ $sekolah->email }}</p>
                </div>
                <div class="flex items-center gap-2 shrink-0">
                    <form method="POST" action="{{ route('admin.sekolah-verification.approve', $sekolah) }}">
                        @csrf
                        <button type="submit" class="bg-ink text-cream text-sm font-medium rounded-lg px-4 py-2 hover:bg-ink/90">
                            Setujui
                        </button>
                    </form>
                    <button onclick="document.getElementById('reject-modal-{{ $sekolah->id }}').classList.remove('hidden')"
                        class="border border-coral text-coral text-sm font-medium rounded-lg px-4 py-2 hover:bg-coral/5">
                        Tolak
                    </button>
                </div>
            </div>

            <div id="reject-modal-{{ $sekolah->id }}" class="hidden fixed inset-0 z-50 flex items-center justify-center px-4">
                <div onclick="document.getElementById('reject-modal-{{ $sekolah->id }}').classList.add('hidden')"
                     class="absolute inset-0 bg-ink/40"></div>
                <div class="relative bg-white rounded-2xl shadow-lg w-full max-w-md p-6">
                    <p class="text-xs font-semibold tracking-widest text-coral uppercase mb-1">Tolak Pendaftaran</p>
                    <h2 class="font-display font-semibold text-lg mb-4">{{ $sekolah->nama }}</h2>

                    <form method="POST" action="{{ route('admin.sekolah-verification.reject', $sekolah) }}">
                        @csrf
                        <label for="reason-{{ $sekolah->id }}" class="text-xs font-semibold uppercase text-ink/70">Alasan Penolakan</label>
                        <textarea id="reason-{{ $sekolah->id }}" name="reason" rows="4" required
                            class="mt-2 w-full rounded-lg border-ink/15 bg-cream/40 focus:border-coral focus:ring-coral text-sm"
                            placeholder="cth. NPSN tidak sesuai data resmi Kemendikbud"></textarea>

                        <div class="flex justify-end gap-2 mt-5">
                            <button type="button" onclick="document.getElementById('reject-modal-{{ $sekolah->id }}').classList.add('hidden')"
                                class="text-sm font-medium text-ink/60 px-4 py-2 hover:text-ink">Batal</button>
                            <button type="submit"
                                class="bg-coral text-white text-sm font-medium rounded-lg px-4 py-2 hover:bg-coral/90">
                                Kirim Penolakan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        @empty
            <p class="text-sm text-ink/50 text-center py-10">Tidak ada pendaftaran yang menunggu verifikasi.</p>
        @endforelse
    </div>
</x-app-layout>