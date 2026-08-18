@props(['href', 'active' => false])

<a href="{{ $href }}"
   @class([
       'flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm font-medium transition',
       'bg-cream text-ink' => $active,
       'text-hitam2 hover:bg-cream2 hover:text-ink' => ! $active,
   ])>
    {{ $slot }}
</a>