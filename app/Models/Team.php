<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Team extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'department',
    ];

    /**
     * Filter a query to only include teams managed by the provided user.
     *
     * @param Builder $query
     * @param User $user
     * @return Builder|void
     */
    public function scopeWhereManagedByUser(Builder $query, User $user)
    {
        // Admin users manage all teams
        if ($user->is_admin) {
            return;
        }

        $query->whereHas('users', function (Builder $query) use ($user) {
            $query
                ->where('is_leader', true)
                ->where('id', $user->id);
        });
    }

    /**
     * Determines if the provided user manages this team.
     *
     * @param User $user
     * @return bool
     */
    public function managedByUser(User $user)
    {
        if ($user->is_admin) {
            return true;
        }

        return $this->users()
                ->wherePivot('is_leader', true)
                ->where('id', $user->id)
                ->count() > 0;
    }

    /**
     * Get the users many-to-many relationship.
     *
     * @return BelongsToMany
     */
    public function users()
    {
        return $this->belongsToMany(
            User::class,
            'team_users',
            'team_id',
            'user_id'
        )->withPivot('is_leader')->orderByDesc('email_verified_at');
    }
}
