<nav class="space-y-1">
    <p class="px-4 text-[11px] font-semibold tracking-widest text-ink/40 uppercase mb-2">Menu</p>
    <x-sidebar-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
         <img src="{{ asset('images/sekolah/home.svg') }}" alt="home"  class="w-4 h-4 shrink-0">
        Beranda
    </x-sidebar-link>
    <x-sidebar-link :href="route('sekolah.kelas.index')" :active="request()->routeIs('sekolah.kelas.*')">
        <img src="{{ asset('images/sekolah/school.svg') }}" alt="kelas"  class="w-4 h-4 shrink-0">
        Kelas
    </x-sidebar-link>
    <x-sidebar-link :href="route('sekolah.guru.index')" :active="request()->routeIs('sekolah.guru.*')">
        <img src="{{ asset('images/sekolah/teacher.svg') }}" alt="guru"  class="w-4 h-4 shrink-0">
        Guru
    </x-sidebar-link>
    <x-sidebar-link :href="route('sekolah.siswa.index')" :active="request()->routeIs('sekolah.siswa.*')">
        <img src="{{ asset('images/sekolah/users.svg') }}" alt="siswa"  class="w-4 h-4 shrink-0">
        Siswa
    </x-sidebar-link>
    <x-sidebar-link :href="route('sekolah.log.index')" :active="request()->routeIs('sekolah.log.*')">
        <img src="{{ asset('images/sekolah/log.svg') }}" alt="log"  class="w-4 h-4 shrink-0">
            <path stroke-linecap="round" stroke-linejoin="round"
                d="M9 12h6m-6 4h6M9 8h6M5 4h14a1 1 0 011 1v14a1 1 0 01-1 1H5a1 1 0 01-1-1V5a1 1 0 011-1z" />
        </svg>
        Log Sistem
    </x-sidebar-link>
</nav>
