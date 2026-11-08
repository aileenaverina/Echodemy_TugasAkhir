<x-mail::message>
# Pendaftaran Belum Dapat Disetujui

Halo, terima kasih sudah mendaftarkan **{{ $sekolah->nama }}** di Echodemy. Setelah ditinjau, pendaftaran belum bisa kami setujui saat ini.

**Alasan:**
{{ $reason }}

Kamu bisa memperbaiki data sesuai catatan di atas dan mendaftar ulang kapan saja.

<x-mail::button :url="route('register')">
Daftar Ulang
</x-mail::button>

Terima kasih,<br>
Tim Echodemy
</x-mail::message>