<header class="bg-putih shadow-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-3">
            <img src="{{ asset('images/logo-mark.svg') }}" alt="Echodemy" class="w-9 h-9 rounded-lg">
            <div class="leading-none">
                <p class="font-display font-semibold text-sm">Echodemy</p>
                <p class="text-[10px] text-hitam2 mt-0.5">Belajar dengan Audio</p>
            </div>
        </a>

        <div class="relative">
            <button id="profile-menu-button" class="flex items-center gap-3">
               <img src="{{ auth()->user()->photoUrl() }}" alt="Foto profil" class="w-9 h-9 rounded-full object-cover">
               <span class="hidden sm:inline text-sm font-medium">{{ auth()->user()->displayName() }}</span>
            </button>

            <div id="profile-menu" class="hidden absolute right-0 top-12 bg-putih rounded-lg shadow-lg border border-border py-1 w-40 z-50">
                <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm hover:bg-cream2">Profil</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full text-left px-4 py-2 text-sm text-coral hover:bg-cream2">Keluar</button>
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