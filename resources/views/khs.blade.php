<!DOCTYPE html>
<html lang="en">

<style>
    .tabel-khs {
        border-collapse: collapse;
    }

    .tabel-khs td,
    .tabel-khs th {
        padding: .15em;
    }

    .tabel-khs thead tr th,
    .tabel-khs tfoot tr th,
    .bold {
        font-weight: bold;
    }

    @page {
        size: 609.45pt 935.43pt;
    }

    .page_break {
        page-break-after: always;
    }

    .text-center {
        text-align: center;
    }
</style>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>

@php
    switch ($dataMhsw[0]['ProdiID']) {
        case 'KEB':
            $prodi = 'Kebidanan';
            break;
        case 'KES':
            $prodi = 'Kesehatan Masyarakat';
            break;
        case 'KEP':
            $prodi = 'Ilmu Keperawatan';
            break;
        case 'NERS':
            $prodi = 'Profesi Ners';
            break;
        case 'RMIK':
            $prodi = 'Rekam Medis dan Informasi Kesehatan';
            break;
        case 'TI':
            $prodi = 'Teknik Informatika';
            break;
        case 'IF':
            $prodi = 'Informatika';
            break;
        default:
            $prodi = '-';
    }
    switch ($data[0]->semester) {
        case '1':
            $semester = '1 (Satu)';
            break;
        case '2':
            $semester = '2 (Dua)';
            break;
        case '3':
            $semester = '3 (Tiga)';
            break;
        case '4':
            $semester = '4 (Empat)';
            break;
        case '5':
            $semester = '5 (Lima)';
            break;
        case '6':
            $semester = '6 (Enam)';
            break;
        case '7':
            $semester = '7 (Tujuh)';
            break;
        case '8':
            $semester = '8 (Delapan)';
            break;
        case '9':
            $semester = '9 (Sembilan)';
            break;
        case '10':
            $semester = '10 (Sepuluh)';
            break;
        case '11':
            $semester = '11 (Sebelas)';
            break;
        case '12':
            $semester = '12 (Dua Belas)';
            break;
        case '13':
            $semester = '13 (Tiga Belas)';
            break;
        case '14':
            $semester = '14 (Empat Belas)';
            break;
        default:
            $semester = ' (Kosong)';
            break;
    }

    $jabatan = 'Dekan Fakultas Kesehatan';
    $ttd = 'DEKANFK';
    $pejabat = 'Jaenudin, SKM., MPH';

    if ($dataMhsw[0]['ProdiID'] == 'IF') {
        $jabatan = 'Dekan Fakultas Teknik';
        $ttd = 'DEKANFT';
        $pejabat = 'Fardhoni,ST., MM';
    }

@endphp

<body>
    <div style="font-family: Arial, Helvetica, sans-serif; font-size: 10pt; text-align: justify;" id="pdfContent">
        <img src= '{{ public_path('image/kop_header.jpg') }}' width="100%">
    </div>
    <div align=center>
        <h1>Kartu Hasil Studi (KHS)</h1>
        <table width="100%">
            <tr>
                <td width="11%">Nama</td>
                <td width="1%">:</td>
                <td width="30%" class="bold">{{ $dataMhsw[0]['Nama'] }}</td>
                <td width="13%">NPM</td>
                <td width="1%">:</td>
                <td width="8%" class="bold">{{ $dataMhsw[0]['MhswID'] }}</td>
            </tr>
            <tr>
                <td>Program Studi</td>
                <td>:</td>
                <td class="bold">{{ $prodi }}</td>
                <td>Semester</td>
                <td>:</td>
                <td class="bold">{{ $semester }}</td>
            </tr>
            <tr>
                <td>Dosen Wali</td>
                <td>:</td>
                <td class="bold">
                    {{ $dosen_wali[0]['GelarDepan'] }}{{ $dosen_wali[0]['Nama'] }}{{ $dosen_wali[0]['Gelar'] }}</td>
                <td>Tahun Akademik</td>
                <td>:</td>
                <td class="bold">{{ substr($data[0]->tahun, 0, 4) }}
                    {{ substr($data[0]->tahun, -1) == 1 ? ' Ganjil' : ' Genap' }}</td>
            </tr>
        </table>
        <br />
        <table width="100%" class="tabel-khs" border=1>
            <thead>
                <tr class="bg-info text-white">
                    <th class="text-center" width="13%">Kode</th>
                    <th class="text-center">Mata Kuliah</th>
                    <th class="text-center">SKS</th>
                    <th class="text-center">Skor</th>
                    <th class="text-center">Angka</th>
                    <th class="text-center">Grade</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data as $item)
                    <tr>
                        <td class="text-center">{{ $item->mk_kode }}</td>
                        <td style="width: auto">{{ $item->nama }}</td>
                        <td class="text-center" style="width: 10%">{{ $item->sks }}</td>
                        <td class="text-center" style="width: 10%">{{ $item->sks * $item->bobot }}</td>
                        <td class="text-center" style="width: 10%">{{ $item->bobot }}</td>
                        <td class="text-center" style="width: 10%">{{ $item->grade }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>

                <tr>
                    <th class="text-center" colspan="2">Jumlah</th>
                    <th class="text-center">{{ $data->sum('sks') }}</th>
                    <th class="text-center">{{ $totalSKSBobot }}</th>
                    <th></th>
                    <th></th>
                </tr>
            </tfoot>
        </table>
        <br />
        <table width="100%">
            <tr>
                <td width="65%">
                    Bobot Huruf Mutu<br>
                    A = <span class="bold">4</span><br>
                    B = <span class="bold">3</span><br>
                    C = <span class="bold">2</span><br>
                    D = <span class="bold">1</span><br>
                    E = <span class="bold">0</span>
                </td>
                {{-- @dd($data[0]->ip) --}}
                <td width="35%">
                    Index Prestasi Semester: <span class="bold">{{ $data[0]->ips }}</span><br>
                    Index Prestasi Kumulatif: <span class="bold">{{ $data[0]->ip }}</span><br><br><br><br><br>
                </td>
            </tr>
        </table>
        <br />
        <table width="100%">
            <tr>
                <td class="text-center" width="50%"></td>
                <td class="text-center" width="50%">Cirebon, {{ $date }}</td>
            </tr>
            <tr>
                <td class="text-center"></td>
                <td class="text-center">{{ $jabatan }},</td>
            </tr>
            <tr>
                <td></td>
                <td style="text-align: center;">
                    <img src="https://si5f0.mahardika.ac.id/ttd/<?= $ttd . '.ttd.gif' ?>" height="100" />

                </td>
            </tr>
            <tr>
                <td class="bold text-center"></td>
                <td class="bold text-center">{{ $pejabat }}</td>
            </tr>
            <tr>
                <td class="text-center"></td>
                <td class="text-center"><?= 'NIK. 12123' ?></td>
            </tr>
        </table>
    </div>
</body>

</html>
