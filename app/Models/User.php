<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'role',
        'profile_picture',
        'bio',
        'phone_number',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function loginLogs()
    {
        return $this->hasMany(\App\Models\LoginLog::class);
    }

    /**
     * Get the reviews written by the user.
     */
    public function reviews()
    {
        return $this->hasMany(\App\Models\CoffeeShopReview::class);
    }

    /**
     * Get the community memberships for the user.
     */
    public function communityMembers()
    {
        return $this->hasMany(\App\Models\CommunityMember::class);
    }

    /**
     * Get the communities the user belongs to (via community_members).
     */
    public function komunitas()
    {
        return $this->belongsToMany(\App\Models\Komunitas::class, 'community_members')
                    ->withPivot('role', 'joined_at')
                    ->withTimestamps();
    }

    /**
     * Get the community posts created by the user.
     */
    public function communityPosts()
    {
        return $this->hasMany(\App\Models\CommunityPost::class);
    }

    /**
     * Get the community comments created by the user.
     */
    public function communityComments()
    {
        return $this->hasMany(\App\Models\CommunityComment::class);
    }

    /**
     * Get the gathering requests created by the user.
     */
    public function gatheringRequests()
    {
        return $this->hasMany(\App\Models\GatheringRequest::class, 'requested_by');
    }

}
