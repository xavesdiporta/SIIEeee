<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ata extends Model
{
    use HasFactory;

    protected $fillable = ['nome', 'dia', 'ficheiro', 'user_id'];

    protected $casts = [
        'dia' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
