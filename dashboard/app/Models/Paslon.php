<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Paslon extends Model
{
    use HasFactory;

    protected $primaryKey = 'paslon_id';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'paslon_id',
        'ketua',
        'wakil',
        'nomor',
        'asset',
    ];

    public function ketua() {
        return $this->belongsTo(SiswaModel::class, 'ketua', 'nis');
    }
    public function wakil() {
        return $this->belongsTo(SiswaModel::class, 'wakil', 'nis');
    }

    public function paslonFirst()
    {
        return $this->hasMany(VotePapper::class, 'paslon1','paslon_id');
    }

    public function paslonSecond()
    {
        return $this->hasMany(VotePapper::class, 'paslon2','paslon_id');
    }

    public function paslonThird()
    {
        return $this->hasMany(VotePapper::class, 'paslon3','paslon_id');
    }

    public function Votecount() {
        return $this->hasMany(VoteCount::class,'paslon_id','paslon_id');
    }
}
