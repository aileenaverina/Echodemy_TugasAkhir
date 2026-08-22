@php $role = auth()->user()->role?->value; @endphp

@if ($role === 'sekolah')
    {{-- Read-only, sesuai keputusan: semua data sekolah tidak bisa diubah dari sini --}}
    <div class="space-y-3 text-sm">
        <div class="flex justify-between"><span class="text-hitam2">Nama Sekolah</span><span class="font-medium">{{ $user->sekolah->nama }}</span></div>
        <div class="flex justify-between"><span class="text-hitam2">NPSN</span><span class="font-medium">{{ $user->sekolah->npsn }}</span></div>
        <div class="flex justify-between"><span class="text-hitam2">Singkatan</span><span class="font-medium">{{ $user->sekolah->singkatan }}</span></div>
        <div class="flex justify-between"><span class="text-hitam2">Jenjang</span><span class="font-medium">{{ $user->sekolah->jenjang }}</span></div>
        <div class="flex justify-between"><span class="text-hitam2">Alamat</span><span class="font-medium text-right max-w-[60%]">{{ $user->sekolah->detail_alamat }}</span></div>
        <div class="flex justify-between"><span class="text-hitam2">Telepon</span><span class="font-medium">{{ $user->sekolah->nomor_telepon }}</span></div>
        <div class="flex justify-between"><span class="text-hitam2">Email</span><span class="font-medium">{{ $user->email }}</span></div>
        <div class="flex justify-between"><span class="text-hitam2">Status</span><span class="font-medium">{{ $user->sekolah->status === \App\Models\Sekolah::STATUS_VERIFIED ? 'Terverifikasi' : 'Menunggu' }}</span></div>
    </div>
    <p class="text-xs text-hitam2 mt-4">Data sekolah hanya bisa diubah oleh admin melalui menu Manajemen Sekolah.</p>

@elseif ($role === 'admin')
    <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="space-y-4">
        @csrf
        @method('patch')

        <div>
            <x-input-label for="foto_profil" value="Foto Profil" class="text-xs font-semibold tracking-wide uppercase text-hitam2" />
            <input type="file" name="foto_profil" accept="image/png,image/jpeg"
                class="mt-2 w-full text-sm text-hitam2 file:mr-3 file:py-2 file:px-3 file:rounded-lg file:border-0 file:bg-cream2 file:text-ink file:text-sm">
            <x-input-error class="mt-2" :messages="$errors->get('foto_profil')" />
        </div>

        <div>
            <x-input-label for="nama_lengkap" value="Nama Lengkap" class="text-xs font-semibold tracking-wide uppercase text-hitam2" />
            <x-text-input id="nama_lengkap" name="nama_lengkap" type="text"
                class="mt-2 block w-full rounded-lg border-border bg-cream2 focus:border-coral focus:ring-coral text-sm"
                :value="old('nama_lengkap', $user->nama_lengkap)" required autofocus />
            <x-input-error class="mt-2" :messages="$errors->get('nama_lengkap')" />
        </div>

        <div>
            <x-input-label for="kode_user" value="Kode User" class="text-xs font-semibold tracking-wide uppercase text-hitam2" />
            <x-text-input id="kode_user" name="kode_user" type="text"
                class="mt-2 block w-full rounded-lg border-border bg-cream2 focus:border-coral focus:ring-coral text-sm"
                :value="old('kode_user', $user->kode_user)" required />
            <x-input-error class="mt-2" :messages="$errors->get('kode_user')" />
        </div>

        <div>
            <x-input-label for="email" value="Email" class="text-xs font-semibold tracking-wide uppercase text-hitam2" />
            <x-text-input id="email" name="email" type="email"
                class="mt-2 block w-full rounded-lg border-border bg-cream2 focus:border-coral focus:ring-coral text-sm"
                :value="old('email', $user->email)" required />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />
        </div>

        <div>
            <x-input-label for="nomor_telepon" value="Nomor Telepon" class="text-xs font-semibold tracking-wide uppercase text-hitam2" />
            <x-text-input id="nomor_telepon" name="nomor_telepon" type="text"
                class="mt-2 block w-full rounded-lg border-border bg-cream2 focus:border-coral focus:ring-coral text-sm"
                :value="old('nomor_telepon', $user->detailUser?->nomor_telepon)" />
            <x-input-error class="mt-2" :messages="$errors->get('nomor_telepon')" />
        </div>

        <div class="flex items-center gap-4">
            <button type="submit" class="bg-ink text-cream text-sm font-medium rounded-lg px-4 py-2.5 hover:bg-hitam">Simpan</button>
            @if (session('status') === 'profile-updated')
                <p class="text-sm text-teal">Tersimpan.</p>
            @endif
        </div>
    </form>

@elseif ($role === 'siswa' || $role === 'guru')
    <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="space-y-4">
        @csrf
        @method('patch')

        <div>
            <x-input-label for="foto_profil" value="Foto Profil" class="text-xs font-semibold tracking-wide uppercase text-hitam2" />
            <input type="file" name="foto_profil" accept="image/png,image/jpeg"
                class="mt-2 w-full text-sm text-hitam2 file:mr-3 file:py-2 file:px-3 file:rounded-lg file:border-0 file:bg-cream2 file:text-ink file:text-sm">
            <x-input-error class="mt-2" :messages="$errors->get('foto_profil')" />
        </div>

        <div>
            <x-input-label for="nama_lengkap" value="Nama Lengkap" class="text-xs font-semibold tracking-wide uppercase text-hitam2" />
            <x-text-input id="nama_lengkap" name="nama_lengkap" type="text"
                class="mt-2 block w-full rounded-lg border-border bg-cream2 focus:border-coral focus:ring-coral text-sm"
                :value="old('nama_lengkap', $user->nama_lengkap)" required autofocus />
            <x-input-error class="mt-2" :messages="$errors->get('nama_lengkap')" />
        </div>

        <!-- Read-only: kode_user & email -->
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="text-xs font-semibold tracking-wide uppercase text-hitam2">Kode User</label>
                <p class="mt-2 text-sm text-hitam2 bg-cream2 rounded-lg px-3 py-2.5">{{ $user->kode_user }}</p>
            </div>
            <div>
                <label class="text-xs font-semibold tracking-wide uppercase text-hitam2">Email</label>
                <p class="mt-2 text-sm text-hitam2 bg-cream2 rounded-lg px-3 py-2.5">{{ $user->email }}</p>
            </div>
        </div>

        @if ($role === 'siswa')
            <div>
                <x-input-label for="nis" value="NIS" class="text-xs font-semibold tracking-wide uppercase text-hitam2" />
                <x-text-input id="nis" name="nis" type="text"
                    class="mt-2 block w-full rounded-lg border-border bg-cream2 focus:border-coral focus:ring-coral text-sm"
                    :value="old('nis', $user->detailUser?->nis)" required />
                <x-input-error class="mt-2" :messages="$errors->get('nis')" />
            </div>
        @else
            <div>
                <x-input-label for="nip" value="NIP (opsional)" class="text-xs font-semibold tracking-wide uppercase text-hitam2" />
                <x-text-input id="nip" name="nip" type="text"
                    class="mt-2 block w-full rounded-lg border-border bg-cream2 focus:border-coral focus:ring-coral text-sm"
                    :value="old('nip', $user->detailUser?->nip)" />
                <x-input-error class="mt-2" :messages="$errors->get('nip')" />
            </div>
        @endif

        <div class="grid grid-cols-2 gap-4">
            <div>
                <x-input-label for="jenis_kelamin" value="Jenis Kelamin" class="text-xs font-semibold tracking-wide uppercase text-hitam2" />
                <select id="jenis_kelamin" name="jenis_kelamin"
                    class="mt-2 block w-full rounded-lg border-border bg-cream2 focus:border-coral focus:ring-coral text-sm">
                    <option value="">Pilih</option>
                    <option value="l" @selected(old('jenis_kelamin', $user->detailUser?->jenis_kelamin) === 'l')>Laki-laki</option>
                    <option value="p" @selected(old('jenis_kelamin', $user->detailUser?->jenis_kelamin) === 'p')>Perempuan</option>
                </select>
                <x-input-error class="mt-2" :messages="$errors->get('jenis_kelamin')" />
            </div>
            <div>
                <x-input-label for="tanggal_lahir" value="Tanggal Lahir" class="text-xs font-semibold tracking-wide uppercase text-hitam2" />
                <x-text-input id="tanggal_lahir" name="tanggal_lahir" type="date"
                    class="mt-2 block w-full rounded-lg border-border bg-cream2 focus:border-coral focus:ring-coral text-sm"
                    :value="old('tanggal_lahir', $user->detailUser?->tanggal_lahir?->format('Y-m-d'))" />
                <x-input-error class="mt-2" :messages="$errors->get('tanggal_lahir')" />
            </div>
        </div>

        <div>
            <x-input-label for="detail_alamat" value="Alamat" class="text-xs font-semibold tracking-wide uppercase text-hitam2" />
            <x-text-input id="detail_alamat" name="detail_alamat" type="text"
                class="mt-2 block w-full rounded-lg border-border bg-cream2 focus:border-coral focus:ring-coral text-sm"
                :value="old('detail_alamat', $user->detailUser?->detail_alamat)" />
            <x-input-error class="mt-2" :messages="$errors->get('detail_alamat')" />
        </div>

        <div>
            <x-input-label for="nomor_telepon" value="Nomor Telepon" class="text-xs font-semibold tracking-wide uppercase text-hitam2" />
            <x-text-input id="nomor_telepon" name="nomor_telepon" type="text"
                class="mt-2 block w-full rounded-lg border-border bg-cream2 focus:border-coral focus:ring-coral text-sm"
                :value="old('nomor_telepon', $user->detailUser?->nomor_telepon)" />
            <x-input-error class="mt-2" :messages="$errors->get('nomor_telepon')" />
        </div>

        @if ($role === 'siswa')
            <div>
                <x-input-label for="nama_ortu" value="Nama Orang Tua/Wali" class="text-xs font-semibold tracking-wide uppercase text-hitam2" />
                <x-text-input id="nama_ortu" name="nama_ortu" type="text"
                    class="mt-2 block w-full rounded-lg border-border bg-cream2 focus:border-coral focus:ring-coral text-sm"
                    :value="old('nama_ortu', $user->detailUser?->nama_ortu)" />
                <x-input-error class="mt-2" :messages="$errors->get('nama_ortu')" />
            </div>

            <div>
                <x-input-label for="nomor_telepon_ortu_wali" value="Nomor Telepon Orang Tua/Wali" class="text-xs font-semibold tracking-wide uppercase text-hitam2" />
                <x-text-input id="nomor_telepon_ortu_wali" name="nomor_telepon_ortu_wali" type="text"
                    class="mt-2 block w-full rounded-lg border-border bg-cream2 focus:border-coral focus:ring-coral text-sm"
                    :value="old('nomor_telepon_ortu_wali', $user->detailUser?->nomor_telepon_ortu_wali)" />
                <x-input-error class="mt-2" :messages="$errors->get('nomor_telepon_ortu_wali')" />
            </div>
        @endif

        <!-- Status: read-only -->
        <div>
            <label class="text-xs font-semibold tracking-wide uppercase text-hitam2">Status</label>
            <p class="mt-2 text-sm text-hitam2 bg-cream2 rounded-lg px-3 py-2.5 capitalize">{{ $user->detailUser?->status ?? '-' }}</p>
        </div>

        <div class="flex items-center gap-4">
            <button type="submit" class="bg-ink text-cream text-sm font-medium rounded-lg px-4 py-2.5 hover:bg-hitam">Simpan</button>
            @if (session('status') === 'profile-updated')
                <p class="text-sm text-teal">Tersimpan.</p>
            @endif
        </div>
    </form>
@endif