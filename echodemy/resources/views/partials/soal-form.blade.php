{{--
    Partial: soal-form
    Variabel:
    - $index (int)         — index soal (0-based)
    - $soal (optional)     — DetailLatihan model untuk pre-fill saat edit
--}}
@php
    $abjad = ['A', 'B', 'C', 'D', 'E', 'F'];
    $tipe = $soal->tipe_soal ?? 'pilihan_ganda';
    $nilai = $soal->nilai ?? 10;
    $pertanyaan = $soal->pertanyaan ?? '';
    $isRandom = $soal->is_random ?? false;
    $pilihans = $soal->pilihans ?? collect();
    $jawabans = $soal->jawaban ?? [];
@endphp

<div class="soal-block bg-putih rounded-2xl shadow-sm p-5" data-index="{{ $index }}">
    <div class="flex items-center justify-between mb-4">
        <p class="font-display font-semibold text-sm">Soal <span class="soal-nomor">{{ $index + 1 }}</span></p>
        <button type="button" class="hapus-soal-btn text-xs text-coral hover:underline">Hapus Soal</button>
    </div>

    <div class="space-y-3">
        <div>
            <label class="text-xs font-semibold uppercase text-hitam2">Pertanyaan</label>
            <textarea name="soal[{{ $index }}][pertanyaan]" rows="2" required
                class="mt-1 w-full rounded-lg border-border bg-cream2 focus:border-coral focus:ring-coral text-sm">{{ $pertanyaan }}</textarea>
        </div>

        <div class="grid grid-cols-2 gap-3">
            <div>
                <label class="text-xs font-semibold uppercase text-hitam2">Tipe Soal</label>
                <select name="soal[{{ $index }}][tipe_soal]"
                    class="tipe-soal-select mt-1 w-full rounded-lg border-border bg-cream2 focus:border-coral focus:ring-coral text-sm">
                    <option value="pilihan_ganda" {{ $tipe === 'pilihan_ganda' ? 'selected' : '' }}>Pilihan Ganda
                    </option>
                    <option value="isian" {{ $tipe === 'isian' ? 'selected' : '' }}>Isian</option>
                    <option value="esai" {{ $tipe === 'esai' ? 'selected' : '' }}>Esai</option>
                </select>
            </div>
            <div>
                <label class="text-xs font-semibold uppercase text-hitam2">Poin</label>
                <input type="number" name="soal[{{ $index }}][nilai]" value="{{ $nilai }}" min="0"
                    step="0.01" required
                    class="poin-input mt-1 w-full rounded-lg border-border bg-cream2 focus:border-coral focus:ring-coral text-sm">
            </div>
        </div>

        {{-- Pilihan Ganda --}}
        <div class="pilihan-section {{ $tipe !== 'pilihan_ganda' ? 'hidden' : '' }}">
            <div class="flex items-center justify-between mb-2">
                <label class="text-xs font-semibold uppercase text-hitam2">Pilihan Jawaban</label>
                <label class="flex items-center gap-1.5 text-xs text-hitam2 cursor-pointer">
                    <input type="hidden" name="soal[{{ $index }}][is_random]" value="0">
                    <input type="checkbox" name="soal[{{ $index }}][is_random]" value="1"
                        {{ $isRandom ? 'checked' : '' }} class="rounded border-border text-coral focus:ring-coral">
                    Acak urutan pilihan
                </label>
            </div>
            <div class="pilihan-list space-y-2">
                @forelse ($pilihans as $pi => $pilihan)
                    <div class="pilihan-item flex items-center gap-2">
                        <span class="w-6 text-xs font-semibold text-hitam2">{{ $abjad[$pi] ?? $pi }}</span>
                        <input type="text" name="soal[{{ $index }}][pilihan][{{ $pi }}][teks]"
                            value="{{ $pilihan->pilihan_jawaban }}" placeholder="Teks pilihan" required
                            class="flex-1 rounded-lg border-border bg-cream2 focus:border-coral focus:ring-coral text-sm">
                        <input type="number"
                            name="soal[{{ $index }}][pilihan][{{ $pi }}][persentase]"
                            value="{{ $pilihan->persentase_correct }}" min="-100" max="100" placeholder="%"
                            class="w-20 rounded-lg border-border bg-cream2 focus:border-coral focus:ring-coral text-sm">
                        <button type="button"
                            class="hapus-pilihan-btn text-coral text-lg leading-none">&times;</button>
                    </div>
                @empty
                    <div class="pilihan-item flex items-center gap-2">
                        <span class="w-6 text-xs font-semibold text-hitam2">A</span>
                        <input type="text" name="soal[{{ $index }}][pilihan][0][teks]"
                            placeholder="Teks pilihan" required
                            class="flex-1 rounded-lg border-border bg-cream2 focus:border-coral focus:ring-coral text-sm">
                        <input type="number" name="soal[{{ $index }}][pilihan][0][persentase]" value="100"
                            min="-100" max="100" placeholder="%"
                            class="w-20 rounded-lg border-border bg-cream2 focus:border-coral focus:ring-coral text-sm">
                        <button type="button"
                            class="hapus-pilihan-btn text-coral text-lg leading-none">&times;</button>
                    </div>
                    <div class="pilihan-item flex items-center gap-2">
                        <span class="w-6 text-xs font-semibold text-hitam2">B</span>
                        <input type="text" name="soal[{{ $index }}][pilihan][1][teks]"
                            placeholder="Teks pilihan" required
                            class="flex-1 rounded-lg border-border bg-cream2 focus:border-coral focus:ring-coral text-sm">
                        <input type="number" name="soal[{{ $index }}][pilihan][1][persentase]" value="0"
                            min="-100" max="100" placeholder="%"
                            class="w-20 rounded-lg border-border bg-cream2 focus:border-coral focus:ring-coral text-sm">
                        <button type="button"
                            class="hapus-pilihan-btn text-coral text-lg leading-none">&times;</button>
                    </div>
                @endforelse
            </div>
            <button type="button" class="tambah-pilihan-btn mt-2 text-xs text-coral font-semibold hover:underline">+
                Tambah Pilihan</button>
            <p class="text-[11px] text-hitam2 mt-1">Persentase positif = benar, negatif = penalti. Total persentase
                positif harus 100%.</p>
        </div>

        {{-- Isian --}}
        <div class="isian-section {{ $tipe !== 'isian' ? 'hidden' : '' }}">
            <label class="text-xs font-semibold uppercase text-hitam2">Jawaban yang Diterima</label>
            <div class="jawaban-list space-y-2 mt-1">
                @forelse ($jawabans as $ji => $jawaban)
                    <div class="jawaban-item flex items-center gap-2">
                        <input type="text" name="soal[{{ $index }}][jawaban][{{ $ji }}]"
                            value="{{ $jawaban }}" placeholder="Jawaban ke-{{ $ji + 1 }}"
                            class="flex-1 rounded-lg border-border bg-cream2 focus:border-coral focus:ring-coral text-sm">
                        <button type="button"
                            class="hapus-jawaban-btn text-coral text-lg leading-none">&times;</button>
                    </div>
                @empty
                    <div class="jawaban-item flex items-center gap-2">
                        <input type="text" name="soal[{{ $index }}][jawaban][0]" placeholder="Jawaban ke-1"
                            class="flex-1 rounded-lg border-border bg-cream2 focus:border-coral focus:ring-coral text-sm">
                        <button type="button"
                            class="hapus-jawaban-btn text-coral text-lg leading-none">&times;</button>
                    </div>
                @endforelse
            </div>
            <button type="button" class="tambah-jawaban-btn mt-2 text-xs text-coral font-semibold hover:underline">+
                Tambah Variasi Jawaban</button>
        </div>

        {{-- Esai: tidak ada field tambahan --}}
        <div class="esai-section {{ $tipe !== 'esai' ? 'hidden' : '' }}">
            <p class="text-xs text-hitam2">Jawaban esai dinilai manual oleh guru.</p>
        </div>
    </div>
</div>
