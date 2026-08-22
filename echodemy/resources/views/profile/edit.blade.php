<x-app-layout>
    <x-slot name="header">
        <p class="text-xs font-semibold tracking-widest text-coral uppercase mb-1">Akun Saya</p>
        <h1 class="font-display text-2xl font-semibold">Profil Saya</h1>
    </x-slot>

    <div class="max-w-2xl space-y-6">

        <!-- Ringkasan profil -->
        <div class="bg-putih rounded-2xl shadow-sm p-6 flex items-center gap-4">
            <img src="{{ auth()->user()->photoUrl() }}" alt="Foto profil" class="w-16 h-16 rounded-full object-cover">
            <div>
                <p class="font-display font-semibold text-lg">{{ auth()->user()->displayName() }}</p>
                <p class="text-sm text-hitam2 capitalize">{{ auth()->user()->role?->value }}</p>
            </div>
        </div>

        @unless (auth()->user()->role?->value === 'orang_tua')
            <!-- Update info -->
            <div class="bg-putih rounded-2xl shadow-sm p-6">
                <p class="text-xs font-semibold tracking-widest text-coral uppercase mb-1">Informasi Profil</p>
                <h2 class="font-display font-semibold text-lg mb-4">
                    {{ auth()->user()->role?->value === 'sekolah' ? 'Data Sekolah' : 'Ubah Informasi' }}
                </h2>
                @include('profile.partials.update-profile-information-form')
            </div>
        @endunless

        <!-- Update password -->
        <div class="bg-putih rounded-2xl shadow-sm p-6">
            <p class="text-xs font-semibold tracking-widest text-coral uppercase mb-1">Keamanan</p>
            <h2 class="font-display font-semibold text-lg mb-4">Ubah Password</h2>
            @include('profile.partials.update-password-form')
        </div>

        <!-- Delete account -->
        <div class="bg-putih rounded-2xl shadow-sm p-6">
            <p class="text-xs font-semibold tracking-widest text-coral uppercase mb-1">Zona Berbahaya</p>
            <h2 class="font-display font-semibold text-lg mb-4">Hapus Akun</h2>
            @include('profile.partials.delete-user-form')
        </div>

    </div>
</x-app-layout>