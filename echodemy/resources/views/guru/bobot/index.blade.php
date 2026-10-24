<x-app-layout>
    <x-slot name="header">
        <p class="text-xs font-semibold tracking-widest text-coral uppercase mb-1">
            <a href="{{ route('guru.mata-pelajaran-kelas.show', $mataPelajaranKelas) }}" class="hover:underline">{{ $mataPelajaranKelas->mataPelajaran->nama }}</a>
            &middot; {{ $mataPelajaranKelas->kelas->nama }}
        </p>
        <h1 class="font-display text-2xl font-semibold">Bobot Penilaian</h1>
    </x-slot>

    @if (session('success'))
        <div class="bg-teal/10 text-teal text-sm rounded-lg px-4 py-3 mb-4">{{ session('success') }}</div>
    @endif
    @if ($errors->any())
        <div class="bg-coral/10 text-coral text-sm rounded-lg px-4 py-3 mb-4">{{ $errors->first() }}</div>
    @endif

    <div class="flex items-center gap-2 mb-5">
        @foreach ([1, 2] as $s)
            <a href="{{ route('guru.bobot.index', [$mataPelajaranKelas, 'semester' => $s]) }}"
                class="px-4 py-2 rounded-full text-sm font-medium transition {{ $semester === $s ? 'bg-ink text-cream' : 'bg-putih text-hitam2 border border-border hover:bg-cream2' }}">
                Semester {{ $s }}
            </a>
        @endforeach
    </div>

    @php
        $rows = collect();
        foreach ($bahanAjars as $ba) {
            foreach ($ba->latihans as $l) { $rows->push(['jenis' => 'latihan', 'model' => $l, 'section' => $ba->judul]); }
            foreach ($ba->assignments as $a) { $rows->push(['jenis' => 'assignment', 'model' => $a, 'section' => $ba->judul]); }
        }
        $kategoriOpsi = ['harian' => 'Harian', 'tengah_semester' => 'Tengah Semester', 'akhir_semester' => 'Akhir Semester'];
        $inputClass = 'rounded-lg border-border bg-cream2 focus:border-coral focus:ring-coral text-sm';
    @endphp

    <!-- Ringkasan -->
    <div class="bg-putih rounded-2xl shadow-sm p-5 mb-6">
        <div class="flex items-end justify-between mb-3">
            <div>
                <p class="text-[11px] font-semibold tracking-widest uppercase text-hitam2 mb-1">Total Bobot</p>
                <p class="font-display font-semibold text-3xl"><span id="total-bobot">0</span><span class="text-hitam2 text-lg">/100</span></p>
            </div>
            <p id="status-bobot" class="text-sm font-medium"></p>
        </div>
        <div class="h-2 rounded-full bg-cream2 overflow-hidden mb-4">
            <div id="bar-bobot" class="h-full rounded-full bg-coral" style="width: 0%"></div>
        </div>
        <div class="flex flex-wrap gap-6 text-sm">
            <div><span class="text-hitam2">Harian</span> <span id="sub-harian" class="font-semibold ml-1">0</span></div>
            <div><span class="text-hitam2">Tengah Semester</span> <span id="sub-tengah_semester" class="font-semibold ml-1">0</span></div>
            <div><span class="text-hitam2">Akhir Semester</span> <span id="sub-akhir_semester" class="font-semibold ml-1">0</span></div>
        </div>
    </div>

    <form method="POST" action="{{ route('guru.bobot.update', $mataPelajaranKelas) }}">
        @csrf
        @method('PUT')
        <input type="hidden" name="semester" value="{{ $semester }}">

        <div class="bg-putih rounded-2xl shadow-sm overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-border text-left text-[11px] tracking-widest uppercase text-hitam2">
                        <th class="px-5 py-3 font-semibold">Item</th>
                        <th class="px-5 py-3 font-semibold">Kategori</th>
                        <th class="px-5 py-3 font-semibold">Batas Waktu</th>
                        <th class="px-5 py-3 font-semibold">Nilai Diambil</th>
                        <th class="px-5 py-3 font-semibold text-right">Bobot (%)</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($rows as $r)
                        @php
                            $m = $r['model'];
                            $jenis = $r['jenis'];
                            $key = $jenis . '.' . $m->id;
                            $nm = $jenis . '[' . $m->id . ']';
                        @endphp
                        <tr class="bobot-row border-b border-border last:border-0">
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-2">
                                    <span class="inline-block px-2 py-0.5 rounded-full text-[11px] font-medium text-hitam {{ $jenis === 'latihan' ? 'bg-amber/60' : 'bg-periwinkle/60' }}">
                                        {{ $jenis === 'latihan' ? 'Latihan' : 'Tugas' }}
                                    </span>
                                    <span class="font-medium">{{ $m->judul }}</span>
                                </div>
                                <p class="text-xs text-hitam2 mt-0.5">{{ $r['section'] }}</p>
                            </td>
                            <td class="px-5 py-4">
                                <select name="{{ $nm }}[kategori]" class="kategori-select {{ $inputClass }}" @disabled(! $bisaEdit)>
                                    @foreach ($kategoriOpsi as $val => $label)
                                        <option value="{{ $val }}" @selected(old($key . '.kategori', $m->kategori) === $val)>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td class="px-5 py-4">
                                <input type="datetime-local" name="{{ $nm }}[batas_waktu]"
                                    value="{{ old($key . '.batas_waktu', $m->batas_waktu?->format('Y-m-d\TH:i')) }}"
                                    class="{{ $inputClass }}" @disabled(! $bisaEdit)>
                                @if ($errors->has($key . '.batas_waktu'))
                                    <p class="text-xs text-coral mt-1">{{ $errors->first($key . '.batas_waktu') }}</p>
                                @endif
                            </td>
                            <td class="px-5 py-4">
                                @if ($jenis === 'latihan')
                                    <select name="{{ $nm }}[penilaian_percobaan]" class="{{ $inputClass }}" @disabled(! $bisaEdit)>
                                        @foreach (['tertinggi' => 'Tertinggi', 'terakhir' => 'Terakhir'] as $val => $label)
                                            <option value="{{ $val }}" @selected(old($key . '.penilaian_percobaan', $m->penilaian_percobaan) === $val)>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                @else
                                    <span class="text-hitam2">-</span>
                                @endif
                            </td>
                            <td class="px-5 py-4 text-right">
                                <input type="number" min="0" max="100" name="{{ $nm }}[bobot]"
                                    value="{{ old($key . '.bobot', $m->persen_nilai_akhir ?? 0) }}"
                                    class="bobot-input w-20 text-right {{ $inputClass }}" @disabled(! $bisaEdit)>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-5 py-10 text-center text-hitam2">Belum ada latihan atau tugas di semester ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($bisaEdit && $rows->isNotEmpty())
            <div class="flex items-center justify-between mt-4">
                <p class="text-xs text-hitam2">Bobot 0 berarti tidak masuk penilaian. Item dengan bobot lebih dari 0 wajib punya batas waktu.</p>
                <button type="submit" class="bg-ink text-cream text-sm font-medium rounded-lg px-5 py-2.5 hover:bg-hitam">Simpan Bobot</button>
            </div>
        @endif
    </form>

    <script>
    $(function () {
        function recalc() {
            let total = 0;
            const sub = { harian: 0, tengah_semester: 0, akhir_semester: 0 };

            $('.bobot-row').each(function () {
                const bobot = parseInt($(this).find('.bobot-input').val()) || 0;
                total += bobot;
                sub[$(this).find('.kategori-select').val()] += bobot;
            });

            $('#total-bobot').text(total);
            $('#bar-bobot')
                .css('width', Math.min(total, 100) + '%')
                .toggleClass('bg-teal', total === 100)
                .toggleClass('bg-coral', total !== 100);

            let pesan = 'Pas 100%, bobot siap dipakai untuk raport.';
            if (total < 100) pesan = 'Kurang ' + (100 - total) + '% lagi.';
            if (total > 100) pesan = 'Lebih ' + (total - 100) + '%, kurangi bobotnya.';
            if (total === 0) pesan = 'Belum ada item yang diberi bobot.';

            $('#status-bobot').text(pesan)
                .toggleClass('text-teal', total === 100)
                .toggleClass('text-coral', total !== 100);

            Object.keys(sub).forEach(function (k) { $('#sub-' + k).text(sub[k]); });
        }

        $(document).on('input change', '.bobot-input, .kategori-select', recalc);
        recalc();
    });
    </script>
</x-app-layout>