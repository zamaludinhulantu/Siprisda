<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Research extends Model
{
    use HasFactory;
    protected $table = 'researches';

    protected $fillable = [
        'title',
        'author',
        'researcher_nik',
        'researcher_phone',
        'institution_id',
        'field_id',
        'year',
        'start_date',
        'end_date',
        'abstract',
        'keywords',
        'pdf_path',
        'status',
        'submitted_by',
        'submitted_at',
        'kesbang_verified_at',
        'kesbang_verified_by',
        'approved_at',
        'rejected_at',
        'rejection_message',
        'results_uploaded_at',
        'approved_by',
        'rejected_by'
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'kesbang_verified_at' => 'datetime',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
        'results_uploaded_at' => 'datetime',
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    // Relasi ke institusi
    public function institution()
    {
        return $this->belongsTo(Institution::class);
    }

    // Relasi ke bidang
    public function field()
    {
        return $this->belongsTo(Field::class);
    }

    // Relasi ke user pengunggah
    public function submitter()
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }

    // Relasi ke review penelitian
    public function reviews()
    {
        return $this->hasMany(ResearchReview::class);
    }
}
