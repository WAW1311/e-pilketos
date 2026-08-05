<?php

namespace App\Imports;

use App\Models\SiswaModel;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ImportSiswa implements ToModel,WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */

    public function model(array $row)
    {
        return new SiswaModel([
            'nis'=> $row['nis'],
            'nama'=> $row['nama'],
            'kelas'=> $row['kelas'],
            'wali_kelas'=> $row['wali_kelas'],
        ]);
    }
}
