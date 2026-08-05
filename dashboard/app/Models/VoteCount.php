<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VoteCount extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'vote_id',
        'id_fp',
        'paslon_id',
    ];

    public function paslon()
    {
        return $this->belongsTo(Paslon::class, 'paslon_id', 'paslon_id');
    }
    public function votepapper()
    {
        return $this->belongsTo(VotePapper::class, 'vote_id', 'vote_id');
    }
    public function fingerprint()
    {
        return $this->belongsTo(FingerprintModel::class, 'id_fp', 'id');
    }
}
