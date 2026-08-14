<nav class="space-y-1">
    <p class="px-4 text-[11px] font-semibold tracking-widest text-ink/40 uppercase mb-2">Menu</p>
    <x-sidebar-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
        <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l9-9 9 9M5 10v10h14V10" /></svg>
        Beranda
    </x-sidebar-link>
    <x-sidebar-link :href="route('sekolah.kelas.index')" :active="request()->routeIs('sekolah.kelas.*')">
        <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 21V7a1 1 0 011-1h6a1 1 0 011 1v14M14 21v-7a1 1 0 011-1h4a1 1 0 011 1v7M9 9h.01M9 13h.01" /></svg>
        Kelas
    </x-sidebar-link>
    <x-sidebar-link :href="route('sekolah.guru.index')" :active="request()->routeIs('sekolah.guru.*')">
        <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 4h18v12H9l-4 4v-4H3V4z" /></svg>
        Guru
    </x-sidebar-link>
    <x-sidebar-link :href="route('sekolah.siswa.index')" :active="request()->routeIs('sekolah.siswa.*')">
        <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20v-2a4 4 0 00-3-3.87M9 20v-2a4 4 0 013-3.87M13 7a4 4 0 11-8 0 4 4 0 018 0zM21 20v-2a4 4 0 00-3-3.87" /></svg>
        Siswa
    </x-sidebar-link>
    <x-sidebar-link :href="route('log-sistem.index')" :active="request()->routeIs('log-sistem.*')">
        <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6M9 8h6M5 4h14a1 1 0 011 1v14a1 1 0 01-1 1H5a1 1 0 01-1-1V5a1 1 0 011-1z" /></svg>
        Log Sistem
    </x-sidebar-link>
</nav>