<x-mail::message>
# Akun Guru Kamu Sudah Aktif

Berikut akun login untuk mengakses Echodemy:

- **Email:** {{ $user->email }}
- **Password:** {{ $plainPassword }}

<x-mail::button :url="route('login')">
Login Sekarang
</x-mail::button>

Segera login dan disarankan mengganti password melalui fitur "Lupa Password" jika diperlukan.

Terima kasih,<br>
Tim Echodemy
</x-mail::message>