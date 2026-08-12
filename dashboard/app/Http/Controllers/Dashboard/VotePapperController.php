<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\Paslon;
use App\Models\SiswaModel;
use App\Models\VotePapper;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class VotePapperController extends Controller
{
    protected $paslon;
    protected $votePapper;
    protected $siswa;

    public function __construct($datapaslon = new Paslon(), $datavotePapper = new VotePapper(), $datasiswa = new SiswaModel())
    {
        $this->paslon = $datapaslon;
        $this->votePapper = $datavotePapper;
        $this->siswa = $datasiswa;
    }
    public function fixData($data)
    {
        $data = json_decode(json_encode($data));
        return $data;
    }
    public function index()
    {
        $dataVotePapper = $this->votePapper->with(
                'paslonFirst.ketua',
                'paslonFirst.wakil',
                'paslonSecond.ketua',
                'paslonSecond.wakil',
                'paslonThird.ketua',
                'paslonThird.wakil',
        )->get();
        $data = $this->fixData($dataVotePapper);
        // dd($data);
        return view('VotePapper.index', ['title' => 'Kelola Surat Suara', 'datavote' => $data]);
    }
    public function InsertData(Request $request)
    {
        if ($request->method() == 'GET') {
            return view('VotePapper.tambah', ['title' => 'Kelola Surat Suara','siswa' => $this->siswa->where(['deleted'=>false])->get()]);
        }
        $id_paslon  = [];
        for ($index = 1; $index <= 3; $index++) {
            $generateid = $this->generateId();
            $file = $request->file("foto$index");
            $uuid = Str::uuid()->toString();
            $ext = $file->getClientOriginalExtension();
            $filename = $uuid . '.' . $ext;
            $file->storeAs('public', $filename);

            $this->paslon->create([
                'paslon_id' => $generateid,
                'ketua' => $request->get("ketua$index"),
                'wakil' => $request->get("wakil$index"),
                'asset' => $filename,
                'nomor' => $index,
            ]);
            $id_paslon[] = $generateid;
        }
        $this->votePapper->create([
            'vote_id' => $this->generateId(),
            'paslon1' => $id_paslon[0],
            'paslon2' => $id_paslon[1],
            'paslon3' => $id_paslon[2],
            'periode' => $request->get('periode'),
            'dimulai' => $request->get('dimulai'),
            'berakhir' => $request->get('berakhir'),
        ]);
        return redirect()->route('kelola_surat')->with('success', 'Surat Suara berhasil dibuat!');
    }
    public function UpdateData(Request $request)
    {
        if ($request->method() == 'GET') {
            $dataVotePapper = $this->votePapper->with(
                'paslonFirst.ketua',
                'paslonFirst.wakil',
                'paslonSecond.ketua',
                'paslonSecond.wakil',
                'paslonThird.ketua',
                'paslonThird.wakil',
            )->find($request->query('id'));
            $data = $this->fixData($dataVotePapper);

            return view('VotePapper.update', ['title' => 'Kelola Surat Suara','datavote' => $data, 'datasiswa' => $this->siswa->all()]);
        }
        $dataVotePapper = $this->votePapper->with(
            'paslonFirst',
            'paslonSecond',
            'paslonThird',
        )->find($request->query('id'))->toArray();
        // dd($dataVotePapper);
        $DateNow = now()->format('Y-m-d H:i:s');
        if ($dataVotePapper['dimulai'] <= $DateNow) {
            return redirect()->route('kelola_surat')->with('error', 'Tidak dapat mengubah data, Voting sudah dimulai!');
        }
        $key = ['first', 'second', 'third'];
        for ($index = 0; $index <= 2; $index++) {
            $paslon_index = $key[$index];
            $file = $request->file("foto_$paslon_index");
            $filename = null;
            if ($file) {
                Storage::delete('public/' . $dataVotePapper["paslon_$paslon_index"]['asset']);
                $uuid = Str::uuid()->toString();
                $ext = $file->getClientOriginalExtension();
                $filename = $uuid . '.' . $ext;
                $file->storeAs('public', $filename);
            } else {
                $filename = $dataVotePapper["paslon_$paslon_index"]['asset'];
            }
            $this->paslon->where('paslon_id', $dataVotePapper["paslon_$paslon_index"]['paslon_id'])->update([
                'ketua' => $request->get("ketua_$paslon_index"),
                'wakil' => $request->get("wakil_$paslon_index"),
                'asset' => $filename,
                'nomor' => $dataVotePapper["paslon_$paslon_index"]['nomor'],
            ]);
        }
        $this->votePapper->where('vote_id', $request->query('id'))->update([
            'periode' => $request->get('periode'),
            'dimulai' => $request->get('dimulai'),
            'berakhir' => $request->get('berakhir'),
        ]);
        return redirect()->route('kelola_surat')->with('success', 'Surat Suara berhasil diperbarui!');
    }
    public function DeleteData(Request $request)
    {
        $dataVotePapper = $this->votePapper->find($request->query('id'));
        $datapaslonId = [$dataVotePapper["paslon_first"], $dataVotePapper["paslon_second"], $dataVotePapper["paslon_third"]];
        // foreach ($datapaslonId as $paslons) {
        //     $paslon = $this->paslon->where('paslon_id', $paslons)->first();
        //     Storage::delete('public/' . $paslon['asset']);
        //     $paslon->where('paslon_id', $paslon['paslon_id'])->delete();
        // }
        $this->votePapper->where('vote_id', $request->query('id'))->delete();
        return redirect()->route('kelola_surat')->with('success', 'Surat Suara berhasil dihapus!');
    }
}
