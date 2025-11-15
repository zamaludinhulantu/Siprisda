<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * Kolom yang boleh diisi otomatis (mass assignment)
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'institution_id', // ditambahkan untuk hubungan dengan kampus/lembaga
        'role',
    ];

    /**
     * Kolom yang disembunyikan saat data user diubah ke JSON
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Tipe data yang di-cast otomatis oleh Laravel
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed', // otomatis hash saat disimpan
        ];
    }

    /**
     * Relasi: User -> Institution (banyak user dari satu lembaga)
     */
    public function institution(): BelongsTo
    {
        return $this->belongsTo(Institution::class);
    }

    /**
     * Relasi: User -> Research (satu user bisa mengunggah banyak penelitian)
     */
    public function researches(): HasMany
    {
        return $this->hasMany(Research::class, 'submitted_by');
    }

    /**
     * Relasi: User -> ResearchReview (user bisa jadi reviewer)
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(ResearchReview::class, 'reviewer_id');
    }

    /**
     * Helper untuk memeriksa role pengguna.
     */
    public function hasRole(string|array $roles): bool
    {
        $roles = is_array($roles) ? $roles : func_get_args();
        return in_array($this->role, $roles, true);
    }

    public function hasAdminAccess(): bool
    {
        return $this->hasRole(['admin', 'superadmin']);
    }

    public function hasKesbangAccess(): bool
    {
        return $this->hasRole(['kesbangpol', 'superadmin']);
    }

    public function isSuperAdmin(): bool
    {
        return $this->role === 'superadmin';
    }
}
