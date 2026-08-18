<x-guest-layout>
    <div class="min-h-screen bg-cream px-4 py-10 flex items-center">
        <div class="max-w-md mx-auto w-full">

            <!-- Header -->
            <p class="text-xs font-semibold tracking-widest text-hitam2 uppercase mb-1 text-center">SELAMAT DATANG</p>
            <h1 class="font-display font-semibold text-2xl mb-2 text-center">Masuk ke Echodemy</h1>
            <p class="text-sm text-hitam2 mb-6 text-center">Belajar dengan audio, kapan saja, di mana saja.</p>

            <!-- Card -->
            <div class="bg-putih rounded-2xl shadow-sm p-6">

                <x-auth-session-status class="mb-4" :status="session('status')" />

                <form method="POST" action="{{ route('login') }}" class="space-y-5">
                    @csrf

                    <!-- Email -->
                    <div>
                        <x-input-label for="email" value="Email" class="text-xs font-semibold tracking-wide uppercase text-hitam2" />
                        <x-text-input id="email" name="email" type="email" placeholder="nama@sekolah.ac.id"
                            class="mt-2 block w-full rounded-lg border-border bg-cream2 focus:border-coral focus:ring-coral text-sm"
                            :value="old('email')" required autofocus autocomplete="username" />
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    <!-- Password -->
                    <div>
                        <x-input-label for="password" value="Password" class="text-xs font-semibold tracking-wide uppercase text-hitam2" />
                        <x-text-input id="password" name="password" type="password" placeholder="••••••••"
                            class="mt-2 block w-full rounded-lg border-border bg-cream2 focus:border-coral focus:ring-coral text-sm"
                            required autocomplete="current-password" />
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>

                    <!-- Remember + Forgot -->
                    <div class="flex items-center justify-between">
                        <label for="remember_me" class="inline-flex items-center gap-2 cursor-pointer">
                            <input id="remember_me" type="checkbox" name="remember"
                                class="rounded border-border text-coral focus:ring-coral">
                            <span class="text-sm text-hitam2">Ingat saya</span>
                        </label>

                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" class="text-sm text-coral font-medium hover:underline">
                                Lupa password?
                            </a>
                        @endif
                    </div>

                    <button type="submit"
                        class="w-full bg-ink text-cream font-display font-medium text-sm rounded-lg py-3 hover:bg-hitam transition">
                        Masuk
                    </button>
                </form>
            </div>

            <p class="text-center text-sm text-hitam2 mt-6">
                Sekolah baru?
                <a href="{{ route('register') }}" class="text-coral font-medium hover:underline">Daftarkan sekolahmu</a>
            </p>
        </div>
    </div>
</x-guest-layout>