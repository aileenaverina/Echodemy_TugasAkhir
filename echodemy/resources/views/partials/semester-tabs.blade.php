<div class="flex items-center gap-2 mb-5">
    @foreach ([1, 2] as $s)
        <a href="{{ request()->fullUrlWithQuery(['semester' => $s]) }}"
            class="px-4 py-2 rounded-full text-sm font-medium transition {{ $semester === $s ? 'bg-ink text-cream' : 'bg-putih text-hitam2 border border-border hover:bg-cream2' }}">
            Semester {{ $s }}
        </a>
    @endforeach
</div>