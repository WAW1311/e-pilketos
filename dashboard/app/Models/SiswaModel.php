<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SiswaModel extends Model
{
    use HasFactory;

    protected $primaryKey = 'nis';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'nis','nama','kelas','wali_kelas','deleted'
    ];

    public function ketua() {
        return $this->hasMany(Paslon::class, 'ketua','nis');
    }
    public function wakil() {
        return $this->hasMany(Paslon::class, 'wakil', 'nis');
    }
    public function fingerprint() {
        return $this->hasOne(FingerprintModel::class, 'siswa', 'nis');
    }
}
