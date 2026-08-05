<?php

namespace App\Http\Controllers\Dashboard;

use Exception;
use App\Models\SiswaModel;
use App\Imports\ImportSiswa;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;

class SiswaController extends Controller
{
    protected $siswa;

    public function __construct($datasiswa = new SiswaModel()) {
        $this->siswa = $datasiswa;
    }

    public function index() {
        $dataSiswa = $this->fetchdata();
        return view('Siswa.index',['title' => 'Kelola Siswa','dataSiswa' => $dataSiswa]);
    }
    private function fetchdata($nis = null, $deletedcheck = true) {
        if($nis != null && $deletedcheck == true) {
            return $this->siswa->where(['nis' => $nis,'deleted' => false])->first();
        } else if ($nis != null && $deletedcheck == false){
            return $this->siswa->where(['nis' => $nis])->first();
        }
        return $this->siswa->where('deleted', false)->get();
    }
    public function InsertData(Request $request) {
        $excel = $request->query('excel');
        if ($request->method() == "GET" && $excel == 'false') {
            return view('Siswa.insertOne',['title' => 'Kelola Siswa',]);
        } else if($request->method() == "GET" && $excel == 'true') {
            return view('Siswa.insertExcel',['title' => 'Kelola Siswa']);
        }
        if($excel == 'true') {
            try {
                $request->validate([
                    'file' => 'required|mimes:csv,txt,xls,xlsx|mimetypes:text/plain,text/csv,application/vnd.ms-excel'
                ]);

                Excel::import(new ImportSiswa(), $request->file('file'));
                return redirect()->route('kelola_siswa')->with('success', 'Data siswa berhasil diimport!');
            } catch(Exception $e) {
                return redirect()->back()->with('error', 'Gagal mengimport data siswa!');
            }
        }
        try {
            $nis = $request->get('nis');
            $nama = $request->get('nama');
            $kelas = $request->get('kelas');
            $wali_kelas = $request->get('wali_kelas');
            $isAdded = $this->fetchdata($nis,false);
            if (!$isAdded) {
                $this->siswa->create([
                    'nis' => $nis,
                    'nama' => $nama,
                    'kelas' => $kelas,
                    'wali_kelas' => $wali_kelas,
                    'deleted' => false
                ]);
                return redirect()->route('kelola_siswa')->with('success', 'Berhasil menambahkan data siswa!');
            } else if($isAdded->deleted == true) {
                $this->siswa->where('nis', $nis)->update([
                    'nama' => $nama,
                    'kelas' => $kelas,
                    'wali_kelas' => $wali_kelas,
                    'deleted' => false
                ]);
                return redirect()->route('kelola_siswa')->with('success', 'Berhasil menambahkan data siswa!');
            }
            return redirect()->back()->with('error', 'Data siswa sudah ditambahkan sebelumnya!');
        } catch(Exception $e) {
            return redirect()->back()->with('error', 'Gagal menambahkan data siswa!');
        }
    }

    public function UpdateData(Request $request) {
        if($request->method() == 'GET') {
            $nis = $request->query('nis');
            $DataSiswa = $this->fetchdata($nis);
            return view('Siswa.update',['title' => 'Kelola Siswa','siswa' => $DataSiswa]);
        }
        $nis = $request->get('nis');
        $nama = $request->get('nama');
        $kelas = $request->get('kelas');
        $wali_kelas = $request->get('wali_kelas');
        try {
            $this->siswa->update([
                'nama' => $nama,
                'kelas' => $kelas,
                'wali_kelas' => $wali_kelas,
            ],['nis'=> $nis]);
            return redirect()->route('kelola_siswa')->with('success', 'berhasil memperbarui data siswa!');
        } catch(Exception $e) {
            return redirect()->back()->with('error', 'Gagal Mengupdate data siswa!');
        }
    }

    public function DeleteData(Request $request) {
        $nis = $request->query('nis');
        try {
            $this->siswa->where('nis', $nis)->update([
                'deleted' => true
            ]);
            return redirect()->back()->with('success', 'Berhasil menghapus data siswa!');
        } catch(Exception $e) {
            dd($e);
            return redirect()->back()->with('error','gagal menghapus data siswa!');
        }
    }
}
