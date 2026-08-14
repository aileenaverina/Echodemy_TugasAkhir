@props(['href', 'active' => false])

<a href="{{ $href }}"
   @class([
       'flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm font-medium transition',
       'bg-cream text-ink' => $active,
       'text-ink/60 hover:bg-cream/60 hover:text-ink' => ! $active,
   ])>
    {{ $slot }}
</a>