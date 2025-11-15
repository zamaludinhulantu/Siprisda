<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ResearchReview extends Model
{
    use HasFactory;

    protected $fillable = ['research_id', 'reviewer_id', 'decision', 'notes'];

    public function research()
    {
        return $this->belongsTo(Research::class);
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }
}
