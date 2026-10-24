@php $dihitung = $rekap['items']->where('bobot', '>', 0); @endphp

<div class="bg-putih rounded-2xl shadow-sm overflow-hidden">
    <table class="w-full text-sm">
        <thead>
            <tr class="border-b border-border text-left text-[11px] tracking-widest uppercase text-hitam2">
                <th class="px-5 py-3 font-semibold">Nama</th>
                <th class="px-5 py-3 font-semibold text-right">Nilai</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($dihitung as $item)
                <tr class="border-b border-border last:border-0">
                    <td class="px-5 py-4">
                        <p class="font-medium">{{ $item['nama'] }}</p>
                        <p class="text-xs text-hitam2">{{ $item['section'] }} &middot; bobot {{ $item['bobot'] }}%</p>
                    </td>
                    <td class="px-5 py-4 text-right font-medium">
                        @if ($item['nilai'] !== null)
                            {{ $item['nilai'] }}
                            @if ($item['status'] === 'tidak_mengumpulkan')
                                <span class="text-xs text-coral ml-1">telat</span>
                            @endif
                        @elseif ($item['status'] === 'menunggu_koreksi')
                            <span class="text-xs text-hitam2">Menunggu penilaian</span>
                        @else
                            -
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="2" class="px-5 py-10 text-center text-hitam2">Belum ada penilaian di mata pelajaran ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if ($dihitung->isNotEmpty())
    <div class="bg-teal/10 rounded-2xl mt-4 px-5 py-4">
        <div class="flex items-center justify-between">
            <p class="font-display font-semibold text-teal">Hasil Akhir</p>
            @if ($rekap['lengkap'])
                <p class="font-display font-semibold text-2xl">{{ $rekap['hasil_akhir'] }}</p>
            @else
                <p class="font-display font-semibold text-lg text-hitam2">Belum lengkap</p>
            @endif
        </div>

        @if (! $rekap['lengkap'])
            @if ($rekap['sementara'] !== null)
                <p class="text-xs text-hitam2 mt-1 text-right">Sementara: {{ $rekap['sementara'] }}</p>
            @endif
            @if ($tampilAlasan ?? false)
                <ul class="text-xs text-coral list-disc pl-4 mt-3 space-y-0.5">
                    @foreach ($rekap['alasan'] as $a)
                        <li>{{ $a }}</li>
                    @endforeach
                </ul>
            @endif
        @endif
    </div>
@endif