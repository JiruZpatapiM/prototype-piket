<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Template extends Model
{
    use HasFactory;

    protected $fillable = ['jenis_piket', 'content'];

    protected function casts(): array
    {
        return [
            'content' => 'array',
        ];
    }
}
