<nav class="space-y-1">
    <p class="px-4 text-[11px] font-semibold tracking-widest text-ink/40 uppercase mb-2">Menu</p>
    <x-sidebar-link :href="route('guru.kelas.index')" :active="request()->routeIs('guru.kelas.*')">
        <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 21V7a1 1 0 011-1h6a1 1 0 011 1v14M14 21v-7a1 1 0 011-1h4a1 1 0 011 1v7" /></svg>
        Kelas Saya
    </x-sidebar-link>
    <x-sidebar-link :href="route('guru.rapor.index')" :active="request()->routeIs('guru.rapor.*')">
        <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6M9 8h6M5 4h14a1 1 0 011 1v14a1 1 0 01-1 1H5a1 1 0 01-1-1V5a1 1 0 011-1z" /></svg>
        Rapor Siswa
    </x-sidebar-link>
    <x-sidebar-link :href="route('log-sistem.index')" :active="request()->routeIs('log-sistem.*')">
        <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6M9 8h6M5 4h14a1 1 0 011 1v14a1 1 0 01-1 1H5a1 1 0 01-1-1V5a1 1 0 011-1z" /></svg>
        Log Sistem
    </x-sidebar-link>
</nav>