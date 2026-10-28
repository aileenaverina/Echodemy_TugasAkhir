<x-app-layout>
    <x-slot name="header">
        <p class="text-xs font-semibold tracking-widest text-coral uppercase mb-1">
            <a href="{{ route('guru.mata-pelajaran-kelas.show', $materi->bahanAjar->mataPelajaranKelas) }}" class="hover:underline">{{ $materi->bahanAjar->judul }}</a>
        </p>
        <h1 class="font-display text-2xl font-semibold">Edit Materi</h1>
    </x-slot>

    @if ($errors->any())
        <div class="bg-coral/10 text-coral text-sm rounded-lg px-4 py-3 mb-4">{{ $errors->first() }}</div>
    @endif

    @php
        $tipe = old('tipe', $materi->tipe);
        $input = 'mt-2 w-full rounded-lg border-border bg-cream2 focus:border-coral focus:ring-coral text-sm';
    @endphp

    <form method="POST" action="{{ route('guru.materi.update', $materi) }}" enctype="multipart/form-data"
        class="bg-putih rounded-2xl shadow-sm p-6 space-y-4 max-w-3xl">
        @csrf
        @method('PUT')

        <div>
            <label class="text-xs font-semibold uppercase text-hitam2">Nama Materi</label>
            <input type="text" name="nama" value="{{ old('nama', $materi->nama) }}" required class="{{ $input }}">
        </div>

        <div>
            <label class="text-xs font-semibold uppercase text-hitam2">Tipe</label>
            <div class="mt-2 flex gap-2">
                @foreach (['teks' => 'Teks', 'url' => 'Tautan', 'file' => 'File'] as $val => $label)
                    <label class="flex-1">
                        <input type="radio" name="tipe" value="{{ $val }}" class="materi-tipe-radio peer hidden" @checked($tipe === $val)>
                        <div class="peer-checked:bg-ink peer-checked:text-cream text-center py-2 rounded-lg border border-border cursor-pointer text-sm">{{ $label }}</div>
                    </label>
                @endforeach
            </div>
        </div>

        <div id="materi-field-teks" class="hidden">
            <label class="text-xs font-semibold uppercase text-hitam2">Konten</label>
            <textarea name="konten" rows="8" class="{{ $input }}">{{ old('konten', $materi->konten) }}</textarea>
        </div>

        <div id="materi-field-url" class="hidden">
            <label class="text-xs font-semibold uppercase text-hitam2">URL</label>
            <input type="url" name="url" value="{{ old('url', $materi->url) }}" placeholder="https://..." class="{{ $input }}">
        </div>

        <div id="materi-field-file" class="hidden">
            <label class="text-xs font-semibold uppercase text-hitam2">File</label>
            @if ($materi->file)
                <p class="text-xs text-hitam2 mt-1">
                    File saat ini: <a href="{{ \Storage::url($materi->file) }}" target="_blank" class="text-coral hover:underline">{{ basename($materi->file) }}</a>.
                    Kosongkan kalau tidak ingin menggantinya.
                </p>
            @endif
            <input type="file" name="file" id="materi-file-input" accept=".pdf,.doc,.docx,.ppt,.pptx"
                class="mt-2 w-full text-sm text-hitam2 file:mr-3 file:py-2 file:px-3 file:rounded-lg file:border-0 file:bg-cream2 file:text-ink file:text-sm">
            <p id="materi-file-warning" class="hidden text-xs text-amber mt-2">
                File Word/PowerPoint tidak mendukung fitur Dengarkan (Text-to-Speech). Hanya file PDF yang dapat dibacakan otomatis.
            </p>
        </div>

        <div class="flex justify-end gap-2 pt-2">
            <a href="{{ route('guru.mata-pelajaran-kelas.show', $materi->bahanAjar->mataPelajaranKelas) }}" class="text-sm font-medium text-hitam2 px-4 py-2 hover:text-ink">Batal</a>
            <button type="submit" class="bg-ink text-cream text-sm font-medium rounded-lg px-5 py-2.5 hover:bg-hitam">Simpan Perubahan</button>
        </div>
    </form>

    <script>
    $(function () {
        function tampil(tipe) {
            $('#materi-field-teks, #materi-field-url, #materi-field-file').addClass('hidden');
            $('#materi-field-' + tipe).removeClass('hidden');
        }

        tampil($('.materi-tipe-radio:checked').val());
        $('.materi-tipe-radio').on('change', function () { tampil($(this).val()); });

        $('#materi-file-input').on('change', function () {
            const nama = this.files[0]?.name.toLowerCase() || '';
            $('#materi-file-warning').toggleClass('hidden', !nama || nama.endsWith('.pdf'));
        });
    });
    </script>
</x-app-layout>