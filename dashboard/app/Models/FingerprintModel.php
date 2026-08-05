<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FingerprintModel extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_siswa',
        'vote_id',
        'template'
    ];

    public function siswa() {
        return $this->belongsTo(SiswaModel::class, 'id_siswa', 'nis');
    }
    public function vote() {
        return $this->belongsTo(VotePapper::class, 'vote_id', 'vote_id');
    }
    public function count() {
        return $this->hasMany(VoteCount::class, 'id_fp', 'id');
    }

}
