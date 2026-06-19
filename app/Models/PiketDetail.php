<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PiketDetail extends Model
{
    protected $fillable = [
        'piket_input_id',
        'category',
        'subcategory',
        'item_name',
        'kondisi',
        'metode',
    ];

    public function input()
    {
        return $this->belongsTo(PiketInput::class, 'piket_input_id');
    }
}
