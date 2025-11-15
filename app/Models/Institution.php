<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Institution extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'type', 'city'];

    // Relasi: satu institusi punya banyak penelitian
    public function researches()
    {
        return $this->hasMany(Research::class);
    }

    // Relasi: satu institusi punya banyak user (peneliti)
    public function users()
    {
        return $this->hasMany(User::class);
    }
}
