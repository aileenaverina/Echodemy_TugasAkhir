<nav class="space-y-1">
    <p class="px-4 text-[11px] font-semibold tracking-widest text-ink/40 uppercase mb-2">Menu</p>
    <x-sidebar-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
        <img src="{{ asset('images/admin/home.svg') }}" alt="home" class="w-4 h-4 shrink-0">
        Beranda
    </x-sidebar-link>
    <x-sidebar-link :href="route('admin.sekolah.index')" :active="request()->routeIs('admin.sekolah.*')">
        <img src="{{ asset('images/admin/school.svg') }}" alt="school" class="w-4 h-4 shrink-0">
        Manajemen Sekolah
    </x-sidebar-link>
    <x-sidebar-link :href="route('admin.mata-pelajaran.index')" :active="request()->routeIs('admin.mata-pelajaran.*')">
        <img src="{{ asset('images/admin/book.svg') }}" alt="book" class="w-4 h-4 shrink-0">
        Manajemen Mata Pelajaran
    </x-sidebar-link>
    <x-sidebar-link :href="route('admin.akun.index')" :active="request()->routeIs('admin.akun.*')">
        <img src="{{ asset('images/admin/safe.svg') }}" alt="safe" class="w-4 h-4 shrink-0">
        Verifikasi & Akses
    </x-sidebar-link>
    <x-sidebar-link :href="route('admin.log.index')" :active="request()->routeIs('admin.log.*')">
        <img src="{{ asset('images/admin/log.svg') }}" alt="log" class="w-4 h-4 shrink-0">
        Log Sistem
    </x-sidebar-link>
</nav>
