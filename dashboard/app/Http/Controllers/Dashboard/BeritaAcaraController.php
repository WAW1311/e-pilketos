<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\PenanggungJawab;
use App\Models\SiswaModel;
use App\Models\VoteCount;
use App\Models\VotePapper;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Spipu\Html2Pdf\Html2Pdf;

class BeritaAcaraController extends Controller
{
    public function index()
    {
        $votepapper = new VotePapper();
        $dataVotePapper = $votepapper->with(
            'paslonFirst.ketua',
            'paslonFirst.wakil',
            'paslonSecond.ketua',
            'paslonSecond.wakil',
            'paslonThird.ketua',
            'paslonThird.wakil',
        )->get();
        $data = json_decode(json_encode($dataVotePapper));
        // dd($data);
        return view('BeritaAcara.index', [
            'title' => 'Cetak Berita Acara',
            'datavote' => $data,
            'penanggungJawab' => PenanggungJawab::all()->keyBy('jabatan'),
        ]);
    }

    function tanggalIndonesia(string $tanggal): string
    {
        $date = Carbon::parse($tanggal);

        $hari = [
            'Sunday' => 'Minggu',
            'Monday' => 'Senin',
            'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday' => 'Kamis',
            'Friday' => 'Jumat',
            'Saturday' => 'Sabtu',
        ];

        $bulan = [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember',
        ];

        return $hari[$date->format('l')]
            . ' tanggal ' . $this->terbilang($date->day)
            . ' Bulan ' . $bulan[$date->month]
            . ' tahun ' . $this->terbilang($date->year);
    }

    function terbilang(int $angka): string
    {
        $angka = abs($angka);

        $bilangan = [
            '',
            'Satu',
            'Dua',
            'Tiga',
            'Empat',
            'Lima',
            'Enam',
            'Tujuh',
            'Delapan',
            'Sembilan',
            'Sepuluh',
            'Sebelas',
        ];

        if ($angka < 12) {
            return $bilangan[$angka];
        }

        if ($angka < 20) {
            return $this->terbilang($angka - 10) . ' Belas';
        }

        if ($angka < 100) {
            return $this->terbilang(intdiv($angka, 10))
                . ' Puluh'
                . ($angka % 10 ? ' ' . $this->terbilang($angka % 10) : '');
        }

        if ($angka < 200) {
            return 'Seratus' . ($angka % 100 ? ' ' . $this->terbilang($angka % 100) : '');
        }

        if ($angka < 1000) {
            return $this->terbilang(intdiv($angka, 100))
                . ' Ratus'
                . ($angka % 100 ? ' ' . $this->terbilang($angka % 100) : '');
        }

        if ($angka < 2000) {
            return 'Seribu' . ($angka % 1000 ? ' ' . $this->terbilang($angka % 1000) : '');
        }

        if ($angka < 1000000) {
            return $this->terbilang(intdiv($angka, 1000))
                . ' Ribu'
                . ($angka % 1000 ? ' ' . $this->terbilang($angka % 1000) : '');
        }

        if ($angka < 1000000000) {
            return $this->terbilang(intdiv($angka, 1000000))
                . ' Juta'
                . ($angka % 1000000 ? ' ' . $this->terbilang($angka % 1000000) : '');
        }

        return (string) $angka;
    }
    function invalidVoteCount($siswaCount, $voteId)
    {
        $voteCount = VoteCount::where('vote_id', $voteId)->count();

        return $voteCount > $siswaCount ? $voteCount - $siswaCount : "0";
    }
    function blankVoteCount($siswaCount, $voteId)
    {
        $voteCount = VoteCount::where('vote_id', $voteId)->count();

        return $voteCount < $siswaCount ? $siswaCount - $voteCount : 0;
    }

    function formatTanggalIndonesia(string $tanggal): string
    {
        $date = Carbon::parse($tanggal);

        $bulan = [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember',
        ];

        return $date->day . ' ' . $bulan[$date->month] . ' ' . $date->year;
    }

    public function cetak(String $id)
    {
        $votePapper = new VotePapper();
        $siswa = new SiswaModel();
        $data = $votePapper
            ->with([
                'paslonFirst.ketua',
                'paslonFirst.wakil',
                'paslonSecond.ketua',
                'paslonSecond.wakil',
                'paslonThird.ketua',
                'paslonThird.wakil',
                'votecounts',
            ])
            ->where('vote_id', $id)
            ->firstOrFail();

        $voteMap = $data->votecounts
            ->groupBy('paslon_id')
            ->map(fn($items) => $items->count());

        $paslons = collect([$data->paslonFirst, $data->paslonSecond, $data->paslonThird])
            ->filter();
        $paslonList = $paslons->map(function ($p) use ($voteMap) {
            $ketua = $p->getRelation('ketua');
            $wakil = $p->getRelation('wakil');
            return [
                'paslon_id' => $p->paslon_id,
                'nomor'     => (int) $p->nomor,
                'asset'     => $p->asset,
                'ketua'     => ucwords(strtolower($ketua->nama ?? '')),
                'wakil'     => ucwords(strtolower($wakil->nama ?? '')),
                'vote'      => (int) ($voteMap[$p->paslon_id] ?? 0),
            ];
        })->sortBy('nomor')->values();
        $paslonRank = $paslonList->sortByDesc('vote')->values();

        $now = now()->format('Y-m-d H:i:s');
        if($data->berakhir > $now) {
            return redirect()->back()->with('error', 'Berita Acara belum dapat dicetak karena pemilihan belum berakhir!');
        }

        // dd($data);
        return view('BeritaAcara.cetak', [
            'title' => 'Cetak Berita Acara',
            'AcademicYear' => explode('/', $data->periode),
            'eydDate' => $this->tanggalIndonesia(Now()->format('Y-m-d')),
            'siswaCount' => $siswa->count(),
            'paslonList' => $paslonList,
            'invalidVote' => $this->invalidVoteCount($siswa->count(), $data->vote_id),
            'blankVote' => $this->blankVoteCount($siswa->count(), $data->vote_id),
            'paslonRank' => $paslonRank,
            'idDate' => $this->formatTanggalIndonesia(Now()->format('Y-m-d')),
            'ketuaKPU' => PenanggungJawab::firstOrNew(['jabatan' => 'ketua_kpu']),
            'sekretarisKPU' => PenanggungJawab::firstOrNew(['jabatan' => 'sekretaris_kpu']),
            'kepalaSekolah' => PenanggungJawab::firstOrNew(['jabatan' => 'kepala_sekolah']),
            'wakaKesiswaan' => PenanggungJawab::firstOrNew(['jabatan' => 'waka_kesiswaan']),
        ]);
        // $html = view('BeritaAcara.cetak', [
        //     'title' => 'Cetak Berita Acara',
        //     'AcademicYear' => explode('/', $data->periode),
        //     'eydDate' => $this->tanggalIndonesia(Now()->format('Y-m-d')),
        //     'siswaCount' => $siswa->count(),
        //     'paslonList' => $paslonList,
        //     'invalidVote' => $this->invalidVoteCount($siswa->count(), $data->vote_id),
        //     'blankVote' => $this->blankVoteCount($siswa->count(), $data->vote_id),
        //     'paslonRank' => $paslonRank,
        //     'idDate' => $this->formatTanggalIndonesia(Now()->format('Y-m-d')),
        //     'ketuaKPU' => PenanggungJawab::firstOrNew(['jabatan' => 'ketua_kpu']),
        //     'sekretarisKPU' => PenanggungJawab::firstOrNew(['jabatan' => 'sekretaris_kpu']),
        //     'kepalaSekolah' => PenanggungJawab::firstOrNew(['jabatan' => 'kepala_sekolah']),
        //     'wakaKesiswaan' => PenanggungJawab::firstOrNew(['jabatan' => 'waka_kesiswaan']),
        // ])->render();

        // try {
        //     $html2pdf = new Html2Pdf(
        //         'P',
        //         'A4',
        //         'en',
        //     );
        //     $html2pdf->writeHTML($html);

        //     $fileName = "Berita Acara Pemilihan Ketua dan Wakil Ketua OSIS SMAN 1 ULUJAMI Pada Tanggal " . now()->format('Y-m-d') . " Masa Bhakti $data->periode.pdf";
        //     $html2pdf->output($fileName, 'I');
        // } catch (Exception $e) {
        //     echo 'Terjadi kesalahan saat mengonversi HTML ke PDF: ' . $e->getMessage();
        // }
        // return redirect()->route('berita_acara')->with('success', 'Berita Acara berhasil dicetak!');
    }
}
