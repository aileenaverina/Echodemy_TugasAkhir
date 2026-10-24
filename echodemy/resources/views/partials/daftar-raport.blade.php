<div class="bg-putih rounded-2xl shadow-sm p-5 mb-6">
    <p class="text-[11px] font-semibold tracking-widest uppercase text-hitam2 mb-3">Raport</p>

    @forelse ($raports as $r)
        <div class="flex items-center justify-between py-2 {{ ! $loop->last ? 'border-b border-border' : '' }}">
            <div>
                <p class="text-sm font-medium">Semester {{ $r->semester }} &middot; {{ $r->kelas->nama }}</p>
                <p class="text-xs text-hitam2">{{ $r->snapshot['tahun_ajaran'] }} &middot; terbit {{ $r->created_at->translatedFormat('d M Y') }}</p>
            </div>
            <a href="{{ route('raport.unduh', $r) }}" class="inline-flex items-center gap-1.5 bg-ink text-cream text-xs font-medium rounded-lg px-3 py-2 hover:bg-hitam">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M7.5 12L12 16.5m0 0L16.5 12M12 16.5V3" /></svg>
                Unduh PDF
            </a>
        </div>
    @empty
        <p class="text-sm text-hitam2">Belum ada raport yang diterbitkan.</p>
    @endforelse
</div>