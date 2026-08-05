<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VotePapper extends Model
{
    use HasFactory;

    protected $primaryKey = 'vote_id';
    protected $keyType = 'string';

    protected $fillable = [
        'vote_id',
        'paslon1',
        'paslon2',
        'paslon3',
        'periode',
        'dimulai',
        'berakhir',
    ];

    public function paslonFirst()
    {
        return $this->belongsTo(Paslon::class, 'paslon1', 'paslon_id');
    }
    public function paslonSecond()
    {
        return $this->belongsTo(Paslon::class, 'paslon2', 'paslon_id');
    }
    public function paslonThird()
    {
        return $this->belongsTo(Paslon::class, 'paslon3', 'paslon_id');
    }

    public function votecounts()
    {
        return $this->hasMany(VoteCount::class, 'vote_id', 'vote_id');
    }
    public function fingerprint()
    {
        return $this->hasMany(FingerprintModel::class, 'vote_id', 'vote_id');
    }
}
