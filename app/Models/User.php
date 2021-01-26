<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Auth\MustVerifyEmail as MustVerifyEmailTrait;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory;
    use Notifiable;
    use HasApiTokens;
    use MustVerifyEmailTrait;

    // The number of days an email verification token is valid for
    const MAX_EMAIL_VERIFICATION_TOKEN_AGE = 2;

    // The number of minutes a password reset token is valid for
    const MAX_PASSWORD_RESET_TOKEN_AGE = 60;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'is_admin',
        'is_guest',
        'password',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'is_admin' => 'boolean',
        'email_verified_at' => 'datetime',
    ];

    /**
     * Apply filters to the query.
     *
     * @param Builder $query
     * @param array $filters
     * @return void
     */
    public function scopeApplyFilters(Builder $query, array $filters)
    {
        // Search by name
        if ($name = $filters['name'] ?? false) {
            $name = '%'.addcslashes($name, '%_').'%';

            $query->where('name', 'like', $name);
        }

        // Filter admin users
        if ($isAdmin = $filters['is_admin'] ?? false) {
            $query->where('is_admin', (bool) $isAdmin);
        }
    }

    /**
     * Determines if the email address of this user has been verified.
     *
     * @return bool
     */
    public function getEmailVerifiedAttribute()
    {
        return ! is_null($this->email_verified_at);
    }

    /**
     * Determines if this user is the leader of at least one team.
     *
     * @return bool
     */
    public function getIsTeamLeaderAttribute()
    {
        return $this->teams()->wherePivot('is_leader', true)->count() > 0;
    }

    /**
     * Determines if the timestamp of the provided activation token is valid.
     *
     * @param string $timestamp
     * @return bool
     */
    public function activationTokenValid(string $timestamp)
    {
        return Carbon::createFromTimestamp($timestamp)->diffInDays(now()) <= self::MAX_EMAIL_VERIFICATION_TOKEN_AGE;
    }

    /**
     * Determines if the timestamp of the provided password reset token is valid.
     *
     * @param string $timestamp
     * @return bool
     */
    public function passwordResetTokenValid(string $timestamp)
    {
        return Carbon::createFromTimestamp($timestamp)->diffInMinutes(now()) <= self::MAX_PASSWORD_RESET_TOKEN_AGE;
    }

    /**
     * Get the teams many-to-many relationship.
     *
     * @return BelongsToMany
     */
    public function teams()
    {
        return $this->belongsToMany(
            Team::class,
            'team_users',
            'user_id',
            'team_id'
        )->withPivot('is_leader');
    }

    /**
     * Get the events many-to-many relationship.
     *
     * @return BelongsToMany
     */
    public function events()
    {
        return $this->belongsToMany(
            Event::class,
            'event_users',
            'user_id',
            'event_id'
        );
    }
}
