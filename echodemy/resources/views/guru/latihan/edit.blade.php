<x-app-layout>
    <x-slot name="header">
        <p class="text-xs font-semibold tracking-widest text-coral uppercase mb-1">
            <a href="{{ route('guru.latihan.show', $latihan) }}" class="hover:underline">{{ $latihan->judul }}</a>
        </p>
        <h1 class="font-display text-2xl font-semibold">Edit Soal</h1>
    </x-slot>

    @if ($errors->any())
        <div class="bg-coral/10 text-coral text-sm rounded-lg px-4 py-3 mb-4">
            <ul class="list-disc list-inside space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('guru.latihan.soal.update', $latihan) }}" id="edit-soal-form">
        @csrf
        @method('PUT')

        <div class="space-y-4" id="soal-wrapper">
            @foreach ($latihan->detailLatihans->sortBy('nomor') as $i => $soal)
                @include('partials.soal-form', ['index' => $i, 'soal' => $soal])
            @endforeach
        </div>

        <button type="button" id="tambah-soal-btn"
            class="mt-4 text-sm font-semibold text-coral hover:underline">
            + Tambah Soal
        </button>

        <div class="flex items-center justify-between mt-6 pt-4 border-t border-border">
            <div>
                <span class="text-xs text-hitam2">Total poin: </span>
                <span id="total-poin" class="text-sm font-semibold">0</span>
                <span class="text-xs text-hitam2">/100</span>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('guru.latihan.show', $latihan) }}"
                    class="text-sm font-medium text-hitam2 px-4 py-2 hover:text-ink">Batal</a>
                <button type="submit"
                    class="bg-ink text-cream text-sm font-medium rounded-lg px-5 py-2.5 hover:bg-hitam">
                    Simpan Soal
                </button>
            </div>
        </div>
    </form>

    <script>
    $(function () {
        const abjad = ['A','B','C','D','E','F'];

        function getSoalCount() {
            return $('#soal-wrapper .soal-block').length;
        }

        function reindexAll() {
            $('#soal-wrapper .soal-block').each(function (i) {
                $(this).attr('data-index', i);
                $(this).find('.soal-nomor').text(i + 1);
                // Reindex semua name attribute
                $(this).find('[name]').each(function () {
                    const name = $(this).attr('name').replace(/soal\[\d+\]/, `soal[${i}]`);
                    $(this).attr('name', name);
                });
                // Reindex pilihan
                $(this).find('.pilihan-item').each(function (pi) {
                    $(this).find('span').first().text(abjad[pi] || pi);
                    $(this).find('[name]').each(function () {
                        const name = $(this).attr('name').replace(/pilihan\[\d+\]/, `pilihan[${pi}]`);
                        $(this).attr('name', name);
                    });
                });
                // Reindex jawaban isian
                $(this).find('.jawaban-item').each(function (ji) {
                    $(this).find('[name]').each(function () {
                        const name = $(this).attr('name').replace(/jawaban\[\d+\]/, `jawaban[${ji}]`);
                        $(this).attr('name', name);
                    });
                });
            });
            updateTotalPoin();
        }

        function updateTotalPoin() {
            let total = 0;
            $('.poin-input').each(function () {
                total += parseFloat($(this).val()) || 0;
            });
            $('#total-poin').text(Math.round(total * 100) / 100);
        }

        // Tipe soal toggle
        $(document).on('change', '.tipe-soal-select', function () {
            const block = $(this).closest('.soal-block');
            const tipe = $(this).val();
            block.find('.pilihan-section').toggleClass('hidden', tipe !== 'pilihan_ganda');
            block.find('.isian-section').toggleClass('hidden', tipe !== 'isian');
            block.find('.esai-section').toggleClass('hidden', tipe !== 'esai');
        });

        // Tambah soal baru
        $('#tambah-soal-btn').on('click', function () {
            const i = getSoalCount();
            const html = `
            <div class="soal-block bg-putih rounded-2xl shadow-sm p-5" data-index="${i}">
                <div class="flex items-center justify-between mb-4">
                    <p class="font-display font-semibold text-sm">Soal <span class="soal-nomor">${i+1}</span></p>
                    <button type="button" class="hapus-soal-btn text-xs text-coral hover:underline">Hapus Soal</button>
                </div>
                <div class="space-y-3">
                    <div>
                        <label class="text-xs font-semibold uppercase text-hitam2">Pertanyaan</label>
                        <textarea name="soal[${i}][pertanyaan]" rows="2" required
                            class="mt-1 w-full rounded-lg border-border bg-cream2 focus:border-coral focus:ring-coral text-sm"></textarea>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="text-xs font-semibold uppercase text-hitam2">Tipe Soal</label>
                            <select name="soal[${i}][tipe_soal]" class="tipe-soal-select mt-1 w-full rounded-lg border-border bg-cream2 focus:border-coral focus:ring-coral text-sm">
                                <option value="pilihan_ganda">Pilihan Ganda</option>
                                <option value="isian">Isian</option>
                                <option value="esai">Esai</option>
                            </select>
                        </div>
                        <div>
                            <label class="text-xs font-semibold uppercase text-hitam2">Poin</label>
                            <input type="number" name="soal[${i}][nilai]" value="10" min="0" step="0.01" required
                                class="poin-input mt-1 w-full rounded-lg border-border bg-cream2 focus:border-coral focus:ring-coral text-sm">
                        </div>
                    </div>
                    <div class="pilihan-section">
                        <div class="flex items-center justify-between mb-2">
                            <label class="text-xs font-semibold uppercase text-hitam2">Pilihan Jawaban</label>
                            <label class="flex items-center gap-1.5 text-xs text-hitam2 cursor-pointer">
                                <input type="hidden" name="soal[${i}][is_random]" value="0">
                                <input type="checkbox" name="soal[${i}][is_random]" value="1" class="rounded border-border text-coral focus:ring-coral">
                                Acak urutan pilihan
                            </label>
                        </div>
                        <div class="pilihan-list space-y-2">
                            <div class="pilihan-item flex items-center gap-2">
                                <span class="w-6 text-xs font-semibold text-hitam2">A</span>
                                <input type="text" name="soal[${i}][pilihan][0][teks]" placeholder="Teks pilihan" required
                                    class="flex-1 rounded-lg border-border bg-cream2 focus:border-coral focus:ring-coral text-sm">
                                <input type="number" name="soal[${i}][pilihan][0][persentase]" value="100" min="-100" max="100" placeholder="%"
                                    class="w-20 rounded-lg border-border bg-cream2 focus:border-coral focus:ring-coral text-sm">
                                <button type="button" class="hapus-pilihan-btn text-coral text-lg leading-none">&times;</button>
                            </div>
                            <div class="pilihan-item flex items-center gap-2">
                                <span class="w-6 text-xs font-semibold text-hitam2">B</span>
                                <input type="text" name="soal[${i}][pilihan][1][teks]" placeholder="Teks pilihan" required
                                    class="flex-1 rounded-lg border-border bg-cream2 focus:border-coral focus:ring-coral text-sm">
                                <input type="number" name="soal[${i}][pilihan][1][persentase]" value="0" min="-100" max="100" placeholder="%"
                                    class="w-20 rounded-lg border-border bg-cream2 focus:border-coral focus:ring-coral text-sm">
                                <button type="button" class="hapus-pilihan-btn text-coral text-lg leading-none">&times;</button>
                            </div>
                        </div>
                        <button type="button" class="tambah-pilihan-btn mt-2 text-xs text-coral font-semibold hover:underline">+ Tambah Pilihan</button>
                        <p class="text-[11px] text-hitam2 mt-1">Persentase positif = benar, negatif = penalti. Total persentase positif harus 100%.</p>
                    </div>
                    <div class="isian-section hidden">
                        <label class="text-xs font-semibold uppercase text-hitam2">Jawaban yang Diterima</label>
                        <div class="jawaban-list space-y-2 mt-1">
                            <div class="jawaban-item flex items-center gap-2">
                                <input type="text" name="soal[${i}][jawaban][0]" placeholder="Jawaban ke-1"
                                    class="flex-1 rounded-lg border-border bg-cream2 focus:border-coral focus:ring-coral text-sm">
                                <button type="button" class="hapus-jawaban-btn text-coral text-lg leading-none">&times;</button>
                            </div>
                        </div>
                        <button type="button" class="tambah-jawaban-btn mt-2 text-xs text-coral font-semibold hover:underline">+ Tambah Variasi Jawaban</button>
                    </div>
                    <div class="esai-section hidden">
                        <p class="text-xs text-hitam2">Jawaban esai dinilai manual oleh guru.</p>
                    </div>
                </div>
            </div>`;
            $('#soal-wrapper').append(html);
            updateTotalPoin();
        });

        // Hapus soal
        $(document).on('click', '.hapus-soal-btn', function () {
            if (getSoalCount() <= 1) {
                Swal.fire({ title: 'Minimal 1 soal', icon: 'warning', confirmButtonColor: '#1C1A17' });
                return;
            }
            Swal.fire({
                title: 'Hapus soal ini?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#E8674A',
                cancelButtonColor: '#5B5A55',
                confirmButtonText: 'Ya, hapus',
                cancelButtonText: 'Batal',
            }).then(function (r) {
                if (r.isConfirmed) {
                    $(event.target).closest('.soal-block').remove();
                    reindexAll();
                }
            });
        });

        // Tambah pilihan
        $(document).on('click', '.tambah-pilihan-btn', function () {
            const block = $(this).closest('.soal-block');
            const i = block.data('index');
            const pi = block.find('.pilihan-item').length;
            if (pi >= 6) return;
            const html = `
            <div class="pilihan-item flex items-center gap-2">
                <span class="w-6 text-xs font-semibold text-hitam2">${abjad[pi] || pi}</span>
                <input type="text" name="soal[${i}][pilihan][${pi}][teks]" placeholder="Teks pilihan" required
                    class="flex-1 rounded-lg border-border bg-cream2 focus:border-coral focus:ring-coral text-sm">
                <input type="number" name="soal[${i}][pilihan][${pi}][persentase]" value="0" min="-100" max="100" placeholder="%"
                    class="w-20 rounded-lg border-border bg-cream2 focus:border-coral focus:ring-coral text-sm">
                <button type="button" class="hapus-pilihan-btn text-coral text-lg leading-none">&times;</button>
            </div>`;
            $(this).closest('.pilihan-section').find('.pilihan-list').append(html);
        });

        // Hapus pilihan
        $(document).on('click', '.hapus-pilihan-btn', function () {
            const list = $(this).closest('.pilihan-list');
            if (list.find('.pilihan-item').length <= 2) return;
            $(this).closest('.pilihan-item').remove();
            reindexAll();
        });

        // Tambah variasi jawaban isian
        $(document).on('click', '.tambah-jawaban-btn', function () {
            const block = $(this).closest('.soal-block');
            const i = block.data('index');
            const ji = block.find('.jawaban-item').length;
            const html = `
            <div class="jawaban-item flex items-center gap-2">
                <input type="text" name="soal[${i}][jawaban][${ji}]" placeholder="Jawaban ke-${ji+1}"
                    class="flex-1 rounded-lg border-border bg-cream2 focus:border-coral focus:ring-coral text-sm">
                <button type="button" class="hapus-jawaban-btn text-coral text-lg leading-none">&times;</button>
            </div>`;
            $(this).closest('.isian-section').find('.jawaban-list').append(html);
        });

        // Hapus variasi jawaban
        $(document).on('click', '.hapus-jawaban-btn', function () {
            const list = $(this).closest('.jawaban-list');
            if (list.find('.jawaban-item').length <= 1) return;
            $(this).closest('.jawaban-item').remove();
        });

        // Update total poin saat input berubah
        $(document).on('input', '.poin-input', updateTotalPoin);

        updateTotalPoin();
    });
    </script>
</x-app-layout>