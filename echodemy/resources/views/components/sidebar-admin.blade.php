<nav class="space-y-1">
    <p class="px-4 text-[11px] font-semibold tracking-widest text-ink/40 uppercase mb-2">Menu</p>
    <x-sidebar-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
        <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l9-9 9 9M5 10v10h14V10" /></svg>
        Beranda
    </x-sidebar-link>
    <x-sidebar-link :href="route('admin.sekolah.index')" :active="request()->routeIs('admin.sekolah.*')">
        <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 14l9-5-9-5-9 5 9 5zm0 0v6m-9-6v6a9 3 0 0018 0v-6" /></svg>
        Manajemen Sekolah
    </x-sidebar-link>
    <x-sidebar-link :href="route('admin.mata-pelajaran.index')" :active="request()->routeIs('admin.mata-pelajaran.*')">
        <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h7" /></svg>
        Manajemen Mata Pelajaran
    </x-sidebar-link>
    <x-sidebar-link :href="route('admin.sekolah-verification.index')" :active="request()->routeIs('admin.sekolah-verification.*')">
        <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 2l8 4v6c0 5-3.5 8.5-8 10-4.5-1.5-8-5-8-10V6l8-4z" /></svg>
        Verifikasi & Akses
    </x-sidebar-link>
    <x-sidebar-link :href="route('log-sistem.index')" :active="request()->routeIs('log-sistem.*')">
        <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6M9 8h6M5 4h14a1 1 0 011 1v14a1 1 0 01-1 1H5a1 1 0 01-1-1V5a1 1 0 011-1z" /></svg>
        Log Sistem
    </x-sidebar-link>
</nav>