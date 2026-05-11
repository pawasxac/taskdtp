<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Komunitas extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_komunitas',
        'domisili',
        'ketua',
        'deskripsi',
        'tanggal_dibentuk',
        'jumlah_anggota',
        'kontak',
        'status',
    ];

    protected $casts = [
        'tanggal_dibentuk' => 'date',
        'jumlah_anggota' => 'integer',
    ];

    /**
     * Get the members of this community.
     */
    public function members(): HasMany
    {
        return $this->hasMany(CommunityMember::class, 'community_id');
    }

    /**
     * Get the users that belong to this community.
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'community_members')
                    ->withPivot('role', 'joined_at')
                    ->withTimestamps();
    }

    /**
     * Get the gathering requests for this community.
     */
    public function gatheringRequests(): HasMany
    {
        return $this->hasMany(GatheringRequest::class, 'community_id');
    }

    /**
     * Get the posts for this community.
     */
    public function posts(): HasMany
    {
        return $this->hasMany(CommunityPost::class, 'community_id');
    }
}

