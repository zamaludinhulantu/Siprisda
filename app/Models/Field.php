<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Field extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    // Relasi: satu bidang bisa punya banyak penelitian
    public function researches()
    {
        return $this->hasMany(Research::class);
    }

}
