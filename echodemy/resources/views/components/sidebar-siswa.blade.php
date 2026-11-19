<nav class="space-y-1">
    <p class="px-4 text-[11px] font-semibold tracking-widest text-ink/40 uppercase mb-2">Menu</p>
    <x-sidebar-link :href="route('siswa.mata-pelajaran.index')" :active="request()->routeIs('siswa.mata-pelajaran.*')">
        <img src="{{ asset('images/siswa/book.svg') }}" alt="mapel" class="w-4 h-4 shrink-0">
        Mata Pelajaran Saya
    </x-sidebar-link>
    <x-sidebar-link :href="route('siswa.nilai.index')" :active="request()->routeIs('siswa.nilai.*')">
         <img src="{{ asset('images/siswa/nilai.svg') }}" alt="nilai" class="w-4 h-4 shrink-0">
        Nilai
    </x-sidebar-link>
    <x-sidebar-link :href="route('siswa.notifikasi.index')" :active="request()->routeIs('siswa.notifikasi.*')">
        <img src="{{ asset('images/siswa/notif.svg') }}" alt="notifikasi" class="w-4 h-4 shrink-0">
        Notifikasi
    </x-sidebar-link>
</nav>