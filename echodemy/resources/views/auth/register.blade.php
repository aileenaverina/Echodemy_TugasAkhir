<x-guest-layout>
    <div class="min-h-screen bg-cream px-4 py-10">
        <div class="max-w-2xl mx-auto">

            <!-- Header -->
            <p class="text-xs font-semibold tracking-widest text-hitam2 uppercase mb-1">Registrasi Sekolah</p>
            <h1 class="font-display font-semibold text-2xl mb-2">Daftarkan Sekolah Anda</h1>
            <p class="text-sm text-hitam2 mb-6">Lengkapi profil sekolah di bawah. Admin akan meninjau dan mengonfirmasi
                sebelum akun aktif.</p>

            <!-- Form Card -->
            <div class="bg-putih rounded-2xl shadow-sm p-6">
                <form method="POST" action="{{ route('register') }}" enctype="multipart/form-data" class="space-y-5">
                    @csrf

                    <!-- Logo upload -->
                    <div>
                        <x-input-label for="logo" value="Logo Sekolah"
                            class="text-xs font-semibold tracking-wide uppercase text-hitam2" />

                        <div id="dropzone"
                            class="mt-2 flex flex-col items-center justify-center gap-2 border border-dashed border-border rounded-lg py-6 text-sm text-hitam2 cursor-pointer hover:border-coral hover:text-coral transition">
                            <img id="logo-preview" src="" alt="Preview logo"
                                class="hidden w-16 h-16 rounded-lg object-cover mb-1">
                            <svg id="dropzone-icon" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M12 4v12m0 0l-4-4m4 4l4-4M4 20h16" />
                            </svg>
                            <span id="dropzone-text">Tarik file ke sini atau klik untuk unggah (PNG/JPG)</span>
                            <input id="logo" name="logo" type="file" accept="image/png,image/jpeg"
                                class="hidden" />
                        </div>
                        <x-input-error :messages="$errors->get('logo')" class="mt-2" />
                    </div>

                    <!-- Nama Sekolah -->
                    <div>
                        <x-input-label for="nama" value="Nama Sekolah"
                            class="text-xs font-semibold tracking-wide uppercase text-hitam2" />
                        <x-text-input id="nama" name="nama" type="text" placeholder="cth. SD Cahaya Bangsa"
                            class="mt-2 block w-full rounded-lg border-border bg-cream2 focus:border-coral focus:ring-coral text-sm"
                            :value="old('nama')" required autofocus />
                        <x-input-error :messages="$errors->get('nama')" class="mt-2" />
                    </div>

                    <!-- NPSN + Singkatan -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <x-input-label for="npsn" value="NPSN"
                                class="text-xs font-semibold tracking-wide uppercase text-hitam2" />
                            <x-text-input id="npsn" name="npsn" type="text" placeholder="cth. 20501234"
                                maxlength="8"
                                class="mt-2 block w-full rounded-lg border-border bg-cream2 focus:border-coral focus:ring-coral text-sm"
                                :value="old('npsn')" required />
                            <x-input-error :messages="$errors->get('npsn')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="singkatan" value="Singkatan"
                                class="text-xs font-semibold tracking-wide uppercase text-hitam2" />
                            <x-text-input id="singkatan" name="singkatan" type="text" placeholder="cth. CB"
                                maxlength="5"
                                class="mt-2 block w-full rounded-lg border-border bg-cream2 focus:border-coral focus:ring-coral text-sm"
                                :value="old('singkatan')" required />
                            <x-input-error :messages="$errors->get('singkatan')" class="mt-2" />
                        </div>
                    </div>

                    <!-- Jenjang -->
                    <div>
                        <x-input-label for="jenjang" value="Jenjang"
                            class="text-xs font-semibold tracking-wide uppercase text-hitam2" />
                        <select id="jenjang" name="jenjang" required
                            class="mt-2 block w-full rounded-lg border-border bg-cream2 focus:border-coral focus:ring-coral text-sm">
                            <option value="" disabled selected>Pilih jenjang</option>
                            <option value="SD" @selected(old('jenjang') === 'SD')>SD</option>
                            <option value="SMP" @selected(old('jenjang') === 'SMP')>SMP</option>
                            <option value="SMA" @selected(old('jenjang') === 'SMA')>SMA</option>
                            <option value="MA" @selected(old('jenjang') === 'MA')>MA</option>
                            <option value="MK" @selected(old('jenjang') === 'MK')>MK</option>
                            <option value="SMK" @selected(old('jenjang') === 'SMK')>SMK</option>
                        </select>
                        <x-input-error :messages="$errors->get('jenjang')" class="mt-2" />
                    </div>

                    <!-- Wilayah bertingkat: Provinsi -> Kab/Kota -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <x-input-label for="provinsi" value="Provinsi"
                                class="text-xs font-semibold tracking-wide uppercase text-hitam2" />
                            <select id="provinsi"
                                class="mt-2 block w-full rounded-lg border-border bg-cream2 focus:border-coral focus:ring-coral text-sm"
                                required>
                                <option value="">Memuat...</option>
                            </select>
                        </div>
                        <div>
                            <x-input-label for="kabupaten_kota" value="Kabupaten/Kota"
                                class="text-xs font-semibold tracking-wide uppercase text-hitam2" />
                            <select id="kabupaten_kota" disabled
                                class="mt-2 block w-full rounded-lg border-border bg-cream2 focus:border-coral focus:ring-coral text-sm disabled:opacity-50"
                                required>
                                <option value="">Pilih provinsi dulu</option>
                            </select>
                        </div>
                    </div>

                    <!-- Wilayah bertingkat: Kecamatan -> Kelurahan -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <x-input-label for="kecamatan" value="Kecamatan"
                                class="text-xs font-semibold tracking-wide uppercase text-hitam2" />
                            <select id="kecamatan" disabled
                                class="mt-2 block w-full rounded-lg border-border bg-cream2 focus:border-coral focus:ring-coral text-sm disabled:opacity-50"
                                required>
                                <option value="">Pilih kabupaten/kota dulu</option>
                            </select>
                        </div>
                        <div>
                            <x-input-label for="wilayah_kode" value="Kelurahan"
                                class="text-xs font-semibold tracking-wide uppercase text-hitam2" />
                            <select id="wilayah_kode" name="wilayah_kode" disabled
                                class="mt-2 block w-full rounded-lg border-border bg-cream2 focus:border-coral focus:ring-coral text-sm disabled:opacity-50"
                                required>
                                <option value="">Pilih kecamatan dulu</option>
                            </select>
                            <x-input-error :messages="$errors->get('wilayah_kode')" class="mt-2" />
                        </div>
                    </div>

                    <!-- Alamat -->
                    <div>
                        <x-input-label for="detail_alamat" value="Alamat Lengkap"
                            class="text-xs font-semibold tracking-wide uppercase text-hitam2" />
                        <x-text-input id="detail_alamat" name="detail_alamat" type="text"
                            placeholder="Jalan, Nomor, RT/RW, dsb."
                            class="mt-2 block w-full rounded-lg border-border bg-cream2 focus:border-coral focus:ring-coral text-sm"
                            :value="old('detail_alamat')" required />
                        <x-input-error :messages="$errors->get('detail_alamat')" class="mt-2" />
                    </div>

                    <!-- Nomor Telepon + Email -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <x-input-label for="nomor_telepon" value="Nomor Telepon"
                                class="text-xs font-semibold tracking-wide uppercase text-hitam2" />
                            <x-text-input id="nomor_telepon" name="nomor_telepon" type="text"
                                placeholder="081xxxxxxx"
                                class="mt-2 block w-full rounded-lg border-border bg-cream2 focus:border-coral focus:ring-coral text-sm"
                                :value="old('nomor_telepon')" required />
                            <x-input-error :messages="$errors->get('nomor_telepon')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="email" value="Email"
                                class="text-xs font-semibold tracking-wide uppercase text-hitam2" />
                            <x-text-input id="email" name="email" type="email"
                                placeholder="nama@sekolah.ac.id"
                                class="mt-2 block w-full rounded-lg border-border bg-cream2 focus:border-coral focus:ring-coral text-sm"
                                :value="old('email')" required />
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                        </div>
                    </div>

                    <p class="text-xs text-hitam2 -mt-1">Akun login sekolah akan dikirim ke email ini setelah
                        dikonfirmasi admin.</p>

                    <button type="submit"
                        class="w-full bg-ink text-cream font-display font-medium text-sm rounded-lg py-3 mt-2 hover:bg-hitam transition">
                        Registrasi Sekolah
                    </button>
                </form>
            </div>

            <p class="text-center text-sm text-hitam2 mt-6">
                Sudah punya akun?
                <a href="{{ route('login') }}" class="text-coral font-medium hover:underline">Masuk di sini</a>
            </p>
        </div>
    </div>

    <script>
        $(function() {
            const $dropzone = $('#dropzone');
            const $logoInput = $('#logo');
            const $preview = $('#logo-preview');
            const $icon = $('#dropzone-icon');
            const $text = $('#dropzone-text');

            function showPreview(file) {
                if (!file) return;
                const reader = new FileReader();
                reader.onload = function(e) {
                    $preview.attr('src', e.target.result).removeClass('hidden');
                    $icon.addClass('hidden');
                    $text.text(file.name);
                };
                reader.readAsDataURL(file);
            }

            $dropzone.on('click', function(e) {
                if (e.target.id === 'logo') return;
                document.getElementById('logo').click();
            });

            $logoInput.on('change', function() {
                if (this.files && this.files[0]) {
                    showPreview(this.files[0]);
                }
            });

            $dropzone.on('dragover', function(e) {
                e.preventDefault();
                e.stopPropagation();
                $dropzone.addClass('border-coral text-coral');
            });

            $dropzone.on('dragleave', function(e) {
                e.preventDefault();
                e.stopPropagation();
                $dropzone.removeClass('border-coral text-coral');
            });

            $dropzone.on('drop', function(e) {
                e.preventDefault();
                e.stopPropagation();
                $dropzone.removeClass('border-coral text-coral');

                const files = e.originalEvent.dataTransfer.files;
                if (files && files[0]) {
                    $logoInput[0].files = files;
                    showPreview(files[0]);
                }
            });

            const $provinsi = $('#provinsi');
            const $kabupaten = $('#kabupaten_kota');
            const $kecamatan = $('#kecamatan');
            const $kelurahan = $('#wilayah_kode');

            function resetSelect($el, placeholder) {
                $el.html('<option value="">' + placeholder + '</option>').prop('disabled', true);
            }

            function fillSelect($el, items) {
                $el.html('<option value="">Pilih salah satu</option>');
                items.forEach(function(item) {
                    $el.append($('<option>', {
                        value: item.kode,
                        text: item.nama
                    }));
                });
                $el.prop('disabled', false);
            }

            $.getJSON("{{ route('wilayah.provinsi') }}")
                .done(function(data) {
                    fillSelect($provinsi, data);
                })
                .fail(function() {
                    $provinsi.html('<option value="">Gagal memuat data</option>');
                });

            $provinsi.on('change', function() {
                resetSelect($kabupaten, 'Pilih kabupaten/kota');
                resetSelect($kecamatan, 'Pilih kecamatan dulu');
                resetSelect($kelurahan, 'Pilih kecamatan dulu');

                const kode = $(this).val();
                if (!kode) return;

                $.getJSON("{{ url('/wilayah/children') }}/" + kode)
                    .done(function(data) {
                        fillSelect($kabupaten, data);
                    });
            });

            $kabupaten.on('change', function() {
                resetSelect($kecamatan, 'Pilih kecamatan');
                resetSelect($kelurahan, 'Pilih kecamatan dulu');

                const kode = $(this).val();
                if (!kode) return;

                $.getJSON("{{ url('/wilayah/children') }}/" + kode)
                    .done(function(data) {
                        fillSelect($kecamatan, data);
                    });
            });

            $kecamatan.on('change', function() {
                resetSelect($kelurahan, 'Pilih kelurahan');

                const kode = $(this).val();
                if (!kode) return;

                $.getJSON("{{ url('/wilayah/children') }}/" + kode)
                    .done(function(data) {
                        fillSelect($kelurahan, data);
                    });
            });
        });
    </script>
</x-guest-layout>
