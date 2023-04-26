<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends \TCG\Voyager\Models\User
{
    use HasApiTokens, HasFactory, Notifiable;

    public const ROLE_ADMIN = 1;
    public const ROLE_PERFORMER = 2;
    public const ROLE_USER = 5;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function isActive(): bool
    {
        return (int)$this->is_active === 1;
    }

    public function performer_views(): HasMany
    {
        return $this->hasMany(UserView::class, 'performer_id');
    }

    public function portfolios(): HasMany
    {
        return $this->hasMany(Portfolio::class);
    }

    public function goodReviews()
    {
        return $this->hasMany(Review::class, 'user_id', 'id')->where('good_bad', 1)->whereHas('task');
    }

    public function badReviews()
    {
        return $this->hasMany(Review::class, 'user_id', 'id')->where('good_bad', 0)->whereHas('task');
    }
}
