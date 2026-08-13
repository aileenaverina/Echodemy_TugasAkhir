<x-guest-layout>
    <div class="min-h-screen bg-cream px-4 py-10">
        <div class="max-w-2xl mx-auto">

            <!-- Logo header -->
            <div class="flex items-center gap-3 bg-white rounded-xl px-5 py-4 mb-8 shadow-sm">
                <div class="w-10 h-10 rounded-lg bg-ink flex items-center justify-center">
                    <svg class="w-5 h-5 text-amber" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" d="M9 18V6l7 3-7 3M4 6v12M20 6v12" />
                    </svg>
                </div>
                <div>
                    <p class="font-display font-semibold text-base leading-none">Echodemy</p>
                    <p class="text-xs text-ink/50 mt-1">Belajar dengan Audio</p>
                </div>
            </div>

            <!-- Status card -->
            <div class="bg-white rounded-2xl shadow-sm px-8 py-10 text-center">
                <p class="text-xs font-semibold tracking-widest text-coral uppercase mb-2">Status Pendaftaran</p>
                <h1 class="font-display font-semibold text-2xl mb-6">Menunggu Verifikasi Admin</h1>

                <div class="w-14 h-14 rounded-full bg-amber/20 flex items-center justify-center mx-auto mb-6">
                    <svg class="w-6 h-6 text-amber" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M6 3h12M6 21h12M8 3c0 4 4 5 4 9s-4 5-4 9M16 3c0 4-4 5-4 9s4 5 4 9" />
                    </svg>
                </div>

                <p class="text-sm text-ink/60 max-w-sm mx-auto leading-relaxed">
                    Estimasi konfirmasi pendaftaran sekolah sekitar 5–7 hari kerja. Mohon menunggu hasil
                    verifikasi yang akan dikirim melalui email sekolah yang telah didaftarkan.
                </p>
            </div>

            <p class="text-center text-sm text-ink/50 mt-6">
                <a href="{{ route('login') }}" class="text-coral font-medium hover:underline">Kembali ke halaman login</a>
            </p>
        </div>
    </div>
</x-guest-layout>