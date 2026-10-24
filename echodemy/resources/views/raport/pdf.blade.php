@php
    $fmt = fn ($v) => $v === null ? '-' : number_format((float) $v, 1, ',', '');
    $tanggal = \Carbon\Carbon::parse($d['diterbitkan_pada'])->locale('id')->translatedFormat('d F Y');
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <style>
        @page { margin: 36px 42px; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #1C1A17; }
        .kop { width: 100%; border-bottom: 2px solid #1C1A17; padding-bottom: 8px; margin-bottom: 14px; }
        .kop td { vertical-align: middle; }
        h1 { font-size: 15px; margin: 0 0 12px; text-align: center; letter-spacing: 1px; }
        table.info { width: 100%; margin-bottom: 14px; }
        table.info td { padding: 2px 0; }
        table.nilai { width: 100%; border-collapse: collapse; }
        table.nilai th, table.nilai td { border: 1px solid #B9B4A5; padding: 6px 8px; }
        table.nilai th { background: #F3EDE0; text-align: center; font-size: 10px; }
        td.c { text-align: center; }
        tr.total td { font-weight: bold; background: #FBF7EF; }
        table.ttd { width: 100%; margin-top: 36px; }
        table.ttd td { width: 50%; text-align: center; vertical-align: top; }
        .muted { color: #5B5A55; }
    </style>
</head>
<body>
    <table class="kop">
        <tr>
            <td style="width: 70px;">
                @if ($logo)
                    <img src="{{ $logo }}" style="width: 56px; height: 56px;">
                @endif
            </td>
            <td>
                <div style="font-size: 15px; font-weight: bold;">{{ $d['sekolah']['nama'] }}</div>
                <div class="muted">{{ $d['sekolah']['alamat'] }}</div>
                <div class="muted">NPSN {{ $d['sekolah']['npsn'] }}</div>
            </td>
        </tr>
    </table>

    <h1>LAPORAN HASIL BELAJAR</h1>

    <table class="info">
        <tr>
            <td style="width: 16%;">Nama</td><td style="width: 2%;">:</td><td style="width: 38%;">{{ $d['siswa']['nama'] }}</td>
            <td style="width: 16%;">Kelas</td><td style="width: 2%;">:</td><td>{{ $d['kelas'] }}</td>
        </tr>
        <tr>
            <td>NIS</td><td>:</td><td>{{ $d['siswa']['nis'] ?? '-' }}</td>
            <td>Semester</td><td>:</td><td>{{ $d['semester'] }}</td>
        </tr>
        <tr>
            <td>Kode Siswa</td><td>:</td><td>{{ $d['siswa']['kode'] }}</td>
            <td>Tahun Ajaran</td><td>:</td><td>{{ $d['tahun_ajaran'] }}</td>
        </tr>
    </table>

    <table class="nilai">
        <thead>
            <tr>
                <th style="width: 28px;">No</th>
                <th>Mata Pelajaran</th>
                <th>Harian</th>
                <th>Tengah Semester</th>
                <th>Akhir Semester</th>
                <th>Nilai Akhir</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($d['mapel'] as $i => $m)
                <tr>
                    <td class="c">{{ $i + 1 }}</td>
                    <td>{{ $m['nama'] }}</td>
                    <td class="c">{{ $fmt($m['harian']) }}</td>
                    <td class="c">{{ $fmt($m['tengah_semester']) }}</td>
                    <td class="c">{{ $fmt($m['akhir_semester']) }}</td>
                    <td class="c"><strong>{{ $fmt($m['nilai_akhir']) }}</strong></td>
                </tr>
            @endforeach
            <tr class="total">
                <td colspan="5" style="text-align: right;">Rata-rata</td>
                <td class="c">{{ $fmt($d['rata_rata']) }}</td>
            </tr>
        </tbody>
    </table>

    <table class="ttd">
        <tr>
            <td>
                Orang Tua / Wali
                <br><br><br><br>
                ( ................................ )
            </td>
            <td>
                {{ $tanggal }}<br>
                Wali Kelas
                <br><br><br>
                <u>{{ $d['wali_kelas'] }}</u>
            </td>
        </tr>
    </table>
</body>
</html>