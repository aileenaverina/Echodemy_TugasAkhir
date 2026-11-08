<x-guest-layout>
    <div class="min-h-screen bg-cream px-4 py-10 flex items-center">
        <div class="max-w-md mx-auto w-full">

            <!-- Header -->
            <p class="text-xs font-semibold tracking-widest text-hitam2 uppercase mb-1 text-center">LUPA PASSWORD</p>
            <h1 class="font-display font-semibold text-2xl mb-2 text-center">Atur Ulang Password</h1>
            <p class="text-sm text-hitam2 mb-6 text-center">Masukkan email akunmu. Kami akan mengirim tautan untuk membuat password baru.</p>

            <!-- Card -->
            <div class="bg-putih rounded-2xl shadow-sm p-6">

                <x-auth-session-status class="mb-4" :status="session('status')" />

                <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
                    @csrf

                    <!-- Email -->
                    <div>
                        <x-input-label for="email" value="Email"
                            class="text-xs font-semibold tracking-wide uppercase text-hitam2" />
                        <x-text-input id="email" name="email" type="email" placeholder="nama@sekolah.ac.id"
                            class="mt-2 block w-full rounded-lg border-border bg-cream2 focus:border-coral focus:ring-coral text-sm"
                            :value="old('email')" required autofocus />
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    <button type="submit"
                        class="w-full bg-ink text-cream font-display font-medium text-sm rounded-lg py-3 hover:bg-hitam transition">
                        Kirim Tautan Reset
                    </button>
                </form>
            </div>

            <p class="text-center text-sm text-hitam2 mt-6">
                Sudah ingat?
                <a href="{{ route('login') }}" class="text-coral font-medium hover:underline">Kembali ke halaman masuk</a>
            </p>
        </div>
    </div>
</x-guest-layout>