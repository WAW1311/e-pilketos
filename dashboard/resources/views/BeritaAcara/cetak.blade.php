<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @vite(['resources/js/app.js'])
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
            padding: 3px 6px;
        }

        .table td p {
            margin: 0;
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
        <div style="padding: 2.5cm;">
            <div align="center">
                <div class="header">
                    <div
                        style="position: absolute; width: 100%; display: flex; justify-content: space-between; top: -8px;">
                        <div>
                            <img src="{{ asset('storage/image_kop/jateng1.png') }}" style="height: 2.50cm; width: 2.2cm;">
                            <img src="{{ asset('storage/image_kop/smanja1.png') }}" style="height: 2.42cm; width: 2.1cm;">
                        </div>
                        <div>
                            <img src="{{ asset('storage/image_kop/kpu1.png') }}" style="height: 2.42cm; width: 2.1cm;">
                        </div>
                    </div>
                    <div style="position: relative; top: 0;">
                        <p style="font-weight:semibold; font-size: 11pt;">PEMERINTAH PROVINSI JAWA TENGAH</p>
                        <p style="font-weight:bold;font-size: 14pt;">DINAS PENDIDIKAN</p>
                        <p style="font-weight:bold;font-size: 14pt;">SMA NEGERI 1 ULUJAMI</p>
                        <p style="font-size: 7pt;">Jalan Akasia Nomor 7, Ulujami, Pemalang, Jawa Tengah, Kode Pos 52371</p>
                        <p style="font-size: 7pt;">Telepon (0285) 5750533, Laman https://smanegeri1ulujami.sch.id.</p>
                        <p style="font-size: 7pt;">Pos-el smanegeri1ulujami@gmail.com</p>
                    </div>
                    <div style="width: 100%; height: 0; border-top: 3px solid #000;"></div>
                    <p style="font-weight:bold; font-size: 11pt;">BERITA ACARA</p>
                    <p style="font-weight:bold; font-size: 11pt;">PEMILIHAN KETUA DAN WAKIL KETUA OSIS</p>
                    <p style="font-weight:bold; font-size: 11pt;">SMAN 1 ULUJAMI</p>
                    <p style="font-weight:bold; font-size: 11pt;">MB.{{ $AcademicYear[0] }}/{{ $AcademicYear[1] }}</p>
                </div>
            </div>
            <div class="container">
                <div style="text-align: justify;">
                    <span style="font-size: 11pt; text-align: justify;">&nbsp;&nbsp;&nbsp;&nbsp;Pada Hari ini,
                        {{ $eydDate }}, di halaman
                        SMA Negeri 1 Ulujami,
                        telah
                        dilaksanakan pemilihan ketua dan wakil ketua OSIS SMAN 1 ULUJAMI Masa Bhakti
                        {{ $AcademicYear[0] }}-{{ $AcademicYear[1] }} dengan daftar pemilih tetap berjumlah
                        {{ $siswaCount }}, pelaksanaan pemilihan ini berjalan dengan lancar sehingga diperoleh hasil
                        sebagai berikut:</span>
                </div>
                <table class="table" style="margin: 8pt 0pt 8pt 0pt">
                    <thead>
                        <tr>
                            <th width="5%">No</th>
                            <th width="40%">Nama pasangan calon</th>
                            <th width="30%">Jabatan calon</th>
                            <th width="25%">Jumlah</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($paslonList as $paslon)
                            <tr>
                                <td align="center">{{ $loop->iteration }}</td>
                                <td align="center">
                                    <div>
                                        <span>{{ $paslon['ketua'] }}</span><br>
                                        <span>{{ $paslon['wakil'] }}</span>
                                    </div>
                                </td>
                                <td align="center">
                                    <div>
                                        <span>Ketua OSIS</span><br>
                                        <span>Wakil OSIS</span>
                                    </div>
                                </td>
                                <td align="center">{{ $paslon['vote'] }}</td>
                            </tr>
                        @endforeach
                        <tr>
                            <td align="center">4</td>
                            <td align="center">Suara Tidak Sah</td>
                            <td align="center">-</td>
                            <td align="center">{{ $invalidVote }}</td>
                        </tr>
                        <tr>
                            <td align="center">5</td>
                            <td align="center">Suara Tidak Digunakan</td>
                            <td align="center">-</td>
                            <td align="center">{{ $blankVote }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div style="margin-bottom: 8pt; text-align: justify;">
                <span style="font-size: 11pt;">Dengan Demikian, Pasangan Calon Nomor Urut {{ $paslonRank[0]['nomor'] }}
                    Atas Nama {{ $paslonRank[0]['ketua'] }} Dan {{ $paslonRank[0]['wakil'] }} Berhak Menjadi Ketua Dan
                    Wakil Ketua OSIS SMAN 1 Ulujami Masa Bhakti {{ $AcademicYear[0] }}-{{ $AcademicYear[1] }},
                    Pasangan Calon Nomor Urut {{ $paslonRank[1]['nomor'] }} Atas Nama {{ $paslonRank[1]['ketua'] }}
                    Dan {{ $paslonRank[1]['wakil'] }} Berhak Menjadi Sekretaris 1 Dan Sekretaris 2 OSIS SMAN 1 Ulujami
                    Masa Bhakti {{ $AcademicYear[0] }}-{{ $AcademicYear[1] }}, Serta Pasangan Calon Nomor Urut
                    {{ $paslonRank[2]['nomor'] }} Atas Nama {{ $paslonRank[2]['ketua'] }} Dan
                    {{ $paslonRank[2]['wakil'] }} Berhak Menjadi Bendahara 1 Dan Bendahara 2 Osis Sman 1 Ulujami Masa
                    Bhakti {{ $AcademicYear[0] }}-{{ $AcademicYear[1] }}.</span>
            </div>
            <div style="margin-bottom: 8pt">
                <span style="font-size: 11pt;">Demikian Berita Acara Ini Dibuat Untuk Dapat Dipergunakan Sebagaimana
                    Mestinya.
                </span>
            </div>
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
                            <td colspan="5">&nbsp;</td>
                        </tr>
                        <tr>
                            <td align="center">
                                <span
                                    style="text-decoration: underline; font-weight: bold;">{{ $ketuaKPU->nama }}</span><br>NIS.
                                {{ $ketuaKPU->nomor_induk ?? '-' }}
                            </td>
                            <td colspan="3">&nbsp;</td>
                            <td align="center">
                                <span
                                    style="text-decoration: underline; font-weight: bold;">{{ $sekretarisKPU->nama }}</span><br>NIS.
                                {{ $sekretarisKPU->nomor_induk ?? '-' }}
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
                            <td colspan="5">&nbsp;</td>
                        </tr>
                        <tr>
                            <td align="center">
                                <span
                                    style="text-decoration: underline; font-weight: bold;">{{ $kepalaSekolah->nama }}</span><br>NIP.
                                {{ $kepalaSekolah->nomor_induk ?? '-' }}.
                            </td>
                            <td colspan="3">&nbsp;</td>
                            <td align="center">
                                <span
                                    style="text-decoration: underline; font-weight: bold;">{{ $wakaKesiswaan->nama }}</span><br>NIP.
                                {{ $wakaKesiswaan->nomor_induk ?? '-' }}.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </page>
    <script>
        window.addEventListener('load', function() {
            window.print();
        });
    </script>
</body>

</html>
