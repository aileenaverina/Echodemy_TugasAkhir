<nav class="space-y-1">
    <p class="px-4 text-[11px] font-semibold tracking-widest text-ink/40 uppercase mb-2">Menu</p>
    <x-sidebar-link :href="route('guru.kelas.index')" :active="request()->routeIs('guru.kelas.*')">
         <img src="{{ asset('images/guru/school.svg') }}" alt="kelas" class="w-4 h-4 shrink-0">
        Kelas Saya
    </x-sidebar-link>
    <x-sidebar-link :href="route('guru.rapor.index')" :active="request()->routeIs('guru.rapor.*')">
         <img src="{{ asset('images/guru/rapor.svg') }}" alt="rapor" class="w-4 h-4 shrink-0">
        Rapor Siswa
    </x-sidebar-link>
    <x-sidebar-link :href="route('guru.log.index')" :active="request()->routeIs('log-sistem.*')">
         <img src="{{ asset('images/guru/log.svg') }}" alt="log" class="w-4 h-4 shrink-0">
        Log Sistem
    </x-sidebar-link>
</nav>