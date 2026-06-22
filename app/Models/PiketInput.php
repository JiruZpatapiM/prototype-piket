<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PiketInput extends Model
{
    protected $fillable = [
        'user_id',
        'tanggal',
        'lokasi',
        'jenis_piket',
        'persentase',
        'status',
        'score',
        'catatan',
        'file_path',
        'alasan_tolak',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function details()
    {
        return $this->hasMany(PiketDetail::class);
    }
}
