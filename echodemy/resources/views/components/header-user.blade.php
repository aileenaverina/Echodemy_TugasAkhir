<header class="bg-white shadow-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-lg bg-ink flex items-center justify-center">
                <svg class="w-4 h-4 text-amber" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" d="M9 18V6l7 3-7 3M4 6v12M20 6v12" />
                </svg>
            </div>
            <div class="leading-none">
                <p class="font-display font-semibold text-sm">Echodemy</p>
                <p class="text-[10px] text-ink/50 mt-0.5">Belajar dengan Audio</p>
            </div>
        </a>

        <div class="relative">
           <button id="profile-menu-button" class="flex items-center gap-3">
                <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->nama_lengkap ?? 'U') }}&background=E8A84C&color=1C1A17"
                     alt="Foto profil" class="w-9 h-9 rounded-full object-cover">
                <span class="hidden sm:inline text-sm font-medium capitalize">{{ auth()->user()->role?->value }}</span>
            </button>

            <div id="profile-menu" class="hidden absolute right-0 top-12 bg-white rounded-lg shadow-lg border border-ink/10 py-1 w-40 z-50">
                <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm hover:bg-cream/60">Profil</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full text-left px-4 py-2 text-sm text-coral hover:bg-cream/60">Keluar</button>
                </form>
            </div>
        </div>
    </div>
</header>

<script>
$(function () {
    $('#profile-menu-button').on('click', function () {
        $('#profile-menu').toggleClass('hidden');
    });

    $(document).on('click', function (e) {
        const $menu = $('#profile-menu');
        const $button = $('#profile-menu-button');

        if (!$menu.hasClass('hidden') && !$button.is(e.target) && $button.has(e.target).length === 0
            && !$menu.is(e.target) && $menu.has(e.target).length === 0) {
            $menu.addClass('hidden');
        }
    });
});
</script>