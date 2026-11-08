<x-guest-layout>
    <div class="min-h-screen bg-cream px-4 py-10 flex items-center">
        <div class="max-w-md mx-auto w-full">

            <!-- Header -->
            <p class="text-xs font-semibold tracking-widest text-hitam2 uppercase mb-1 text-center">RESET PASSWORD</p>
            <h1 class="font-display font-semibold text-2xl mb-2 text-center">Buat Password Baru</h1>
            <p class="text-sm text-hitam2 mb-6 text-center">Gunakan minimal 8 karakter.</p>

            <!-- Card -->
            <div class="bg-putih rounded-2xl shadow-sm p-6">
                <form method="POST" action="{{ route('password.store') }}" class="space-y-5">
                    @csrf

                    <input type="hidden" name="token" value="{{ $request->route('token') }}">

                    <!-- Email -->
                    <div>
                        <x-input-label for="email" value="Email"
                            class="text-xs font-semibold tracking-wide uppercase text-hitam2" />
                        <x-text-input id="email" name="email" type="email"
                            class="mt-2 block w-full rounded-lg border-border bg-cream2 focus:border-coral focus:ring-coral text-sm"
                            :value="old('email', $request->email)" required autofocus autocomplete="username" />
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    <!-- Password -->
                    <div>
                        <x-input-label for="password" value="Password Baru"
                            class="text-xs font-semibold tracking-wide uppercase text-hitam2" />
                        <x-text-input id="password" name="password" type="password" placeholder="••••••••"
                            class="mt-2 block w-full rounded-lg border-border bg-cream2 focus:border-coral focus:ring-coral text-sm"
                            required autocomplete="new-password" />
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>

                    <!-- Konfirmasi -->
                    <div>
                        <x-input-label for="password_confirmation" value="Konfirmasi Password"
                            class="text-xs font-semibold tracking-wide uppercase text-hitam2" />
                        <x-text-input id="password_confirmation" name="password_confirmation" type="password"
                            placeholder="••••••••"
                            class="mt-2 block w-full rounded-lg border-border bg-cream2 focus:border-coral focus:ring-coral text-sm"
                            required autocomplete="new-password" />
                        <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                    </div>

                    <button type="submit"
                        class="w-full bg-ink text-cream font-display font-medium text-sm rounded-lg py-3 hover:bg-hitam transition">
                        Simpan Password Baru
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-guest-layout>