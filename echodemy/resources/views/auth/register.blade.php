<x-guest-layout>
    <div class="min-h-screen bg-cream px-4 py-10">
        <div class="max-w-2xl mx-auto">

            <!-- Logo header -->
            <div class="flex items-center gap-3 bg-white rounded-xl px-5 py-4 mb-8 shadow-sm">
                <div class="w-10 h-10 rounded-lg bg-ink flex items-center justify-center">
                    <svg class="w-5 h-5 text-amber" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" d="M9 18V6l7 3-7 3M4 6v12M20 6v12" />
                    </svg>
                </div>
                <div>
                    <p class="font-display font-semibold text-base leading-none">Echodemy</p>
                    <p class="text-xs text-ink/50 mt-1">Belajar dengan Audio</p>
                </div>
            </div>

            <!-- Header -->
            <p class="text-xs font-semibold tracking-widest text-coral uppercase mb-1">Registrasi Sekolah</p>
            <h1 class="font-display font-semibold text-2xl mb-2">Daftarkan Sekolah Anda</h1>
            <p class="text-sm text-ink/60 mb-6">Lengkapi profil sekolah di bawah. Admin akan meninjau dan mengonfirmasi sebelum akun aktif.</p>

            <!-- Form Card -->
            <div class="bg-white rounded-2xl shadow-sm p-6">
                <form method="POST" action="{{ route('register') }}" enctype="multipart/form-data" class="space-y-5">
                    @csrf

                    <!-- Logo upload -->
                    <div>
                        <x-input-label for="logo" value="Logo Sekolah" class="text-xs font-semibold tracking-wide uppercase text-ink/70" />
                        <label for="logo"
                            class="mt-2 flex items-center justify-center gap-2 border border-dashed border-ink/20 rounded-lg py-3 text-sm text-ink/60 cursor-pointer hover:border-coral hover:text-coral transition">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v12m0 0l-4-4m4 4l4-4M4 20h16" />
                            </svg>
                            <span>Tarik file ke sini atau klik untuk unggah (PNG/JPG)</span>
                            <input id="logo" name="logo" type="file" accept="image/png,image/jpeg" class="hidden" />
                        </label>
                        <x-input-error :messages="$errors->get('logo')" class="mt-2" />
                    </div>

                    <!-- Nama Sekolah -->
                    <div>
                        <x-input-label for="nama" value="Nama Sekolah" class="text-xs font-semibold tracking-wide uppercase text-ink/70" />
                        <x-text-input id="nama" name="nama" type="text" placeholder="cth. SD Cahaya Bangsa"
                            class="mt-2 block w-full rounded-lg border-ink/15 bg-cream/40 focus:border-coral focus:ring-coral text-sm"
                            :value="old('nama')" required autofocus />
                        <x-input-error :messages="$errors->get('nama')" class="mt-2" />
                    </div>

                    <!-- NPSN + Singkatan -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <x-input-label for="npsn" value="NPSN" class="text-xs font-semibold tracking-wide uppercase text-ink/70" />
                            <x-text-input id="npsn" name="npsn" type="text" placeholder="cth. 20501234" maxlength="8"
                                class="mt-2 block w-full rounded-lg border-ink/15 bg-cream/40 focus:border-coral focus:ring-coral text-sm"
                                :value="old('npsn')" required />
                            <x-input-error :messages="$errors->get('npsn')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="singkatan" value="Singkatan" class="text-xs font-semibold tracking-wide uppercase text-ink/70" />
                            <x-text-input id="singkatan" name="singkatan" type="text" placeholder="cth. CB" maxlength="5"
                                class="mt-2 block w-full rounded-lg border-ink/15 bg-cream/40 focus:border-coral focus:ring-coral text-sm"
                                :value="old('singkatan')" required />
                            <x-input-error :messages="$errors->get('singkatan')" class="mt-2" />
                        </div>
                    </div>

                    <!-- Jenjang + Wilayah -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <x-input-label for="jenjang" value="Jenjang" class="text-xs font-semibold tracking-wide uppercase text-ink/70" />
                            <select id="jenjang" name="jenjang" required
                                class="mt-2 block w-full rounded-lg border-ink/15 bg-cream/40 focus:border-coral focus:ring-coral text-sm">
                                <option value="" disabled selected>Pilih jenjang (SD/SMP/SMA)</option>
                                <option value="SD" @selected(old('jenjang') === 'SD')>SD</option>
                                <option value="SMP" @selected(old('jenjang') === 'SMP')>SMP</option>
                                <option value="SMA" @selected(old('jenjang') === 'SMA')>SMA</option>
                            </select>
                            <x-input-error :messages="$errors->get('jenjang')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="wilayah_kode" value="Wilayah (Kota/Kabupaten)" class="text-xs font-semibold tracking-wide uppercase text-ink/70" />
                            <select id="wilayah_kode" name="wilayah_kode"
                                class="mt-2 block w-full rounded-lg border-ink/15 bg-cream/40 focus:border-coral focus:ring-coral text-sm">
                                <option value="" selected>Pilih wilayah</option>
                                @foreach ($wilayahs as $wilayah)
                                    <option value="{{ $wilayah->kode }}" @selected(old('wilayah_kode') === $wilayah->kode)>
                                        {{ $wilayah->nama }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('wilayah_kode')" class="mt-2" />
                        </div>
                    </div>

                    <!-- Alamat -->
                    <div>
                        <x-input-label for="detail_alamat" value="Alamat Lengkap" class="text-xs font-semibold tracking-wide uppercase text-ink/70" />
                        <x-text-input id="detail_alamat" name="detail_alamat" type="text" placeholder="Jl., Kecamatan, Kota"
                            class="mt-2 block w-full rounded-lg border-ink/15 bg-cream/40 focus:border-coral focus:ring-coral text-sm"
                            :value="old('detail_alamat')" />
                        <x-input-error :messages="$errors->get('detail_alamat')" class="mt-2" />
                    </div>

                    <!-- Nomor Telepon + Email -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <x-input-label for="nomor_telepon" value="Nomor Telepon" class="text-xs font-semibold tracking-wide uppercase text-ink/70" />
                            <x-text-input id="nomor_telepon" name="nomor_telepon" type="text" placeholder="031-xxxxxxx"
                                class="mt-2 block w-full rounded-lg border-ink/15 bg-cream/40 focus:border-coral focus:ring-coral text-sm"
                                :value="old('nomor_telepon')" />
                            <x-input-error :messages="$errors->get('nomor_telepon')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="email" value="Email" class="text-xs font-semibold tracking-wide uppercase text-ink/70" />
                            <x-text-input id="email" name="email" type="email" placeholder="nama@sekolah.sch.id"
                                class="mt-2 block w-full rounded-lg border-ink/15 bg-cream/40 focus:border-coral focus:ring-coral text-sm"
                                :value="old('email')" required />
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                        </div>
                    </div>

                    <p class="text-xs text-ink/50 -mt-1">Akun login sekolah akan dikirim ke email ini setelah dikonfirmasi admin.</p>

                    <button type="submit"
                        class="w-full bg-ink text-cream font-display font-medium text-sm rounded-lg py-3 mt-2 hover:bg-ink/90 transition">
                        Registrasi Sekolah
                    </button>
                </form>
            </div>

            <p class="text-center text-sm text-ink/50 mt-6">
                Sudah punya akun?
                <a href="{{ route('login') }}" class="text-coral font-medium hover:underline">Masuk di sini</a>
            </p>
        </div>
    </div>
</x-guest-layout>