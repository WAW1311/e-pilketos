<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }}</title>
    <style>
        body {
            margin: 0;
            background: #eee;
        }

        page {
            background: white;
            display: block;
            margin: 0 auto;
            width: 21cm;
            height: 29.7cm;
            box-shadow: 0 0 0.5cm rgba(0, 0, 0, 0.5);
        }

        @media print {

            body,
            page {
                background: white;
                box-shadow: none;
                margin: 0;
            }
        }

        .header {
            position: relative;
            line-height: 1.15pt;

        }

        .table {
            width: 100%;
            border-collapse: collapse;
        }

        .table th,
        .table td {
            border: 1px solid black;
        }

        .table2 {
            width: 100%;
            border-collapse: collapse;
        }

        .table2 th {
            font-weight: normal;
            font-size: 11pt;
        }
    </style>
</head>

<body>
    <page>
        <div style="padding: 2.54cm;">
            <div align="center">
                <div class="header">
                    <div
                        style="position: absolute; width: 108%; display: flex; justify-content: space-between; top: -23px; left: -48px;">
                        <div>
                            <img src="{{ public_path('storage/image_kop/jateng.png') }}">
                            <img src="{{ public_path('storage/image_kop/smanja.png') }}">
                        </div>
                        <div>
                            <img src="{{ public_path('storage/image_kop/kpu.png') }}">
                        </div>
                    </div>
                    <div style="position: relative; top: 0;">
                        <p style="font-weight:bold; font-size: 14pt;">PEMERINTAH PROVINSI JAWA TENGAH</p>
                        <p style="font-weight:bold;font-size: 14pt;">DINAS PENDIDIKAN DAN KEBUDAYAAN</p>
                        <p style="font-weight:bold;font-size: 14pt;">SEKOLAH MENENGAH ATAS NEGERI 1 ULUJAMI</p>
                        <p style="font-size: 11pt;">Jalan Akasia Nomor 7, Ulujami Pemalang Kode Pos 52371 Telepon
                            0285-5750533</p>
                        <p style="font-size: 12pt;">Surat Elektronik smanegeri1ulujami@gmail.com</p>
                    </div>
                    <div style="height: 3px; width: 100%; background-color: black;"></div>
                    <p style="font-weight:bold; font-size: 11pt;">BERITA ACARA</p>
                    <p style="font-weight:bold; font-size: 11pt;">PEMILIHAN KETUA DAN WAKIL KETUA OSIS</p>
                    <p style="font-weight:bold; font-size: 11pt;">SMAN 1 ULUJAMI</p>
                    <p style="font-weight:bold; font-size: 11pt;">MB.{{ $AcademicYear[0] }}/{{ $AcademicYear[1] }}</p>
                </div>
            </div>
            <div class="container">
                <p style="font-size: 11pt;">Pada Hari ini, {{ $eydDate }}, di halaman SMA Negeri 1 Ulujami, telah
                    dilaksanakan pemilihan ketua dan wakil ketua OSIS SMAN 1 ULUJAMI Masa Bhakti
                    {{ $AcademicYear[0] }}-{{ $AcademicYear[1] }} dengan daftar pemilih tetap berjumlah
                    {{ $siswaCount }}, pelaksanaan pemilihan ini berjalan dengan lancar sehingga diperoleh hasil
                    sebagai berikut:</p>
                <table class="table">
                    <thead>
                        <tr>
                            <th>NO</th>
                            <th>Nama pasangan calon</th>
                            <th>Jabatan calon</th>
                            <th>Jumlah</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($paslonList as $paslon)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    <div>
                                        <p>{{ $paslon['ketua'] }}</p>
                                        <p>{{ $paslon['wakil'] }}</p>
                                    </div>
                                </td>
                                <td>
                                    <div>
                                        <p>Ketua OSIS</p>
                                        <p>Wakil OSIS</p>
                                    </div>
                                </td>
                                <td>{{ $paslon['vote'] }}</td>
                            </tr>
                        @endforeach
                        <tr>
                            <td>Suara Tidak Sah</td>
                            <td>-</td>
                            <td>{{ $invalidVote }}</td>
                        </tr>
                        <tr>
                            <td>Suara Tidak Digunakan</td>
                            <td>-</td>
                            <td>{{ $blankVote }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div>
                <p style="font-size: 11pt;">Dengan Demikian, Pasangan Calon Nomor Urut {{ $paslonRank[0]['nomor'] }}
                    Atas Nama {{ $paslonRank[0]['ketua'] }} Dan {{ $paslonRank[0]['wakil'] }} Berhak Menjadi Ketua Dan
                    Wakil Ketua OSIS SMAN 1 Ulujami Masa Bhakti {{ $AcademicYear[0] }}-{{ $AcademicYear[1] }},
                    Pasangan Calon Nomor Urut {{ $paslonRank[1]['nomor'] }} Atas Nama {{ $paslonRank[1]['ketua'] }}
                    Dan {{ $paslonRank[1]['wakil'] }} Berhak Menjadi Sekretaris 1 Dan Sekretaris 2 OSIS SMAN 1 Ulujami
                    Masa Bhakti {{ $AcademicYear[0] }}-{{ $AcademicYear[1] }}, Serta Pasangan Calon Nomor Urut
                    {{ $paslonRank[2]['nomor'] }} Atas Nama {{ $paslonRank[2]['ketua'] }} Dan
                    {{ $paslonRank[2]['wakil'] }} Berhak Menjadi Bendahara 1 Dan Bendahara 2 Osis Sman 1 Ulujami Masa
                    Bhakti {{ $AcademicYear[0] }}-{{ $AcademicYear[1] }}.</p>
            </div>
            <p style="font-size: 11pt;">Demikian Berita Acara Ini Dibuat Untuk Dapat Dipergunakan Sebagaimana Mestinya.
            </p>
            <div class="container">
                <table class="table2">
                    <thead>
                        <tr>
                            <th>&nbsp;</th>
                            <th>&nbsp;</th>
                            <th>&nbsp;</th>
                            <th>&nbsp;</th>
                            <th>Ulujami, {{ $idDate }}</th>
                        </tr>
                        <tr>
                            <th>Ketua KPU</th>
                            <th colspan="3">&nbsp;</th>
                            <th>Sekretaris KPU</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="5">&nbsp;</td>
                        </tr>
                        <tr>
                            <td colspan="5">&nbsp;</td>
                        </tr>
                        <tr>
                            <td colspan="5">&nbsp;</td>
                        </tr>
                        <tr>
                            <td align="center">
                                {{ $ketuaKPU->nama }}<br>NIS. {{ $ketuaKPU->nomor_induk ?? '-' }}
                            </td>
                            <td colspan="3">&nbsp;</td>
                            <td align="center">
                                {{ $sekretarisKPU->nama }}<br>NIS. {{ $sekretarisKPU->nomor_induk ?? '-' }}
                            </td>
                        </tr>
                        <tr>
                            <td></td>
                            <td colspan="3" align="center">
                                Mengetahui,
                            </td>
                            <td></td>
                        </tr>
                        <tr>
                            <td align="center">
                                Kepala Sekolah
                            </td>
                            <td colspan="3">&nbsp;</td>
                            <td align="center">
                                Waka Kesiswaan
                            </td>
                        </tr>
                        <tr>
                            <td colspan="5">&nbsp;</td>
                        </tr>
                        <tr>
                            <td colspan="5">&nbsp;</td>
                        </tr>
                        <tr>
                            <td colspan="5">&nbsp;</td>
                        </tr>
                        <tr>
                            <td align="center">
                                <p style="text-decoration: underline;">{{ $kepalaSekolah->nama }}</p><br>NIP.
                                {{ $kepalaSekolah->nomor_induk ?? '-' }}.
                            </td>
                            <td colspan="3">&nbsp;</td>
                            <td align="center">
                                <p style="text-decoration: underline;">{{ $wakaKesiswaan->nama }}</p><br>NIP.
                                {{ $wakaKesiswaan->nomor_induk ?? '-' }}.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </page>
</body>

</html>
