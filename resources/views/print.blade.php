<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
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
@php
    switch ($data_mhsw[0]['ProdiID']) {
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
    switch ($dataKrs[0]->semester) {
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
@endphp

<body style="font-family: Arial, Helvetica, sans-serif; font-size: 10pt; text-align: justify;" id="pdfContent">
    <div style="padding-top:0">
        <img src= '{{ public_path('image/kop_header.jpg') }}' width="100%">
    </div>
    <div align=center>
        <h1>Kartu Rencana Studi (KRS)</h1>
        <table width="100%">
            <tr>
                <td width="18%">Nama</td>
                <td width="2%">:</td>
                <td width="30%" class="bold">{{ $data_mhsw[0]['Nama'] }}</td>
                <td width="18%">Tahun Akademik</td>
                <td width="2%">:</td>
                <td width="30%" class="bold">{{ substr($tahun_aktif['TahunID'], 0, 4) }}
                    {{ substr($tahun_aktif['TahunID'], -1) == 1 ? ' Ganjil' : ' Genap' }}</td>
            </tr>
            <tr>
                <td>NPM</td>
                <td>:</td>
                <td class="bold">{{ $data_mhsw[0]['MhswID'] }}</td>
                <td>Program Studi</td>
                <td>:</td>
                <td class="bold">{{ $prodi }}</td>
            </tr>
            <tr>
                <td>Dosen Wali</td>
                <td>:</td>
                <td class="bold">{{ $dosen_wali[0]['GelarDepan'] }}{{ $dosen_wali[0]['Nama'] }}
                    {{ $dosen_wali[0]['Gelar'] }}</td>
                <td>Semester</td>
                <td>:</td>
                <td class="bold">{{ $semester }}</td>
            </tr>
        </table>
        <br>
        <table width="100%" class="tabel-khs" border=1>
            <thead>
                <tr class="bg-info text-white">
                    <th class="text-center">No.</th>
                    <th class="text-center">Mata Kuliah</th>
                    <th class="text-center">SKS</th>
                    <th class="text-center">Dosen</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $no = 1;
                ?>
                @foreach ($dataKrs as $item)
                    <tr>
                        <td class="text-center">{{ $no++ }}</td>
                        <td>{{ $item->Nama }}</td>
                        <td class="text-center">{{ $item->Sks }}</td>
                        <td>{{ $item->dosen }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td class="bold text-center" colspan="2"> Jumlah SKS</td>
                    <td class="bold text-center">
                        {{ $totalSks }}
                    </td>
                    <td></td>
                </tr>
            </tfoot>
        </table>
        <br />
        <table width="100%">
            <tr>
                <td class="text-center" width="100%" colspan="2">Telah disetujui oleh Dosen Wali pada tanggal
                    {{ $date }}
                    {{-- <?= tanggal_indonesia(date('Y-m-d')) ?><br><br><br></td> --}}
            </tr>
            <br />

            <tr>
                <td class="text-center" width="50%">Dosen Wali,</td>
                <td class="text-center" width="50%">Mahasiswa,</td>
            </tr>
            <tr>
                <td><br><br><br></td>
                <td><br><br><br></td>
            </tr>
            <tr>
                <td class="bold text-center"><?= $dosen_wali[0]['Nama'] ?></td>
                <td class="bold text-center"><?= $data_mhsw[0]['Nama'] ?></td>
            </tr>
            <tr>
                <td class="text-center">NIK. {{ $dosen_wali[0]['nik'] }}</td>
                <td class="text-center">NPM. {{ $data_mhsw[0]['MhswID'] }}</td>
            </tr>
        </table>
    </div>
</body>

</html>
