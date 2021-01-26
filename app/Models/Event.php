<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Event extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'ends_at',
        'host_id',
        'starts_at',
        'description',
        'allow_guests',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'ends_at' => 'datetime',
        'starts_at' => 'datetime',
        'allow_guests' => 'boolean',
        'is_draft' => 'boolean',
    ];

    /**
     * Filter a query to only include events hosted by the provided user.
     *
     * @param Builder $query
     * @param User $user
     */
    public function scopeWhereHostedByUser(Builder $query, User $user)
    {
        // Admin users have host access to all events
        if ($user->is_admin) {
            return;
        }

        $query->where('host_id', $user);
    }

    /**
     * Determines if the provider user has host access to this event.
     *
     * @param User $user
     * @return bool
     */
    public function hostedByUser(User $user)
    {
        // Admin users have host access to all events
        if ($user->is_admin) {
            return true;
        }

        return $this->user->id === $user->id;
    }

    /**
     * Generates a unique 6-digit code for the event.
     *
     * @return string
     */
    public static function generateUniqueEventCode()
    {
        while (true) {
            $dictionary = str_split('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789');

            // Randomly reorder the dictionary of allowed characters
            shuffle($dictionary);

            // Pick the first 6 characters of the randomised dictionary
            $code = implode(array_slice($dictionary, 0, 6));

            if (self::where('code', $code)->count() === 0) {
                return $code;
            }
        }
    }

    /**
     * Get the host one-to-many relationship.
     *
     * @return BelongsTo
     */
    public function host()
    {
        return $this->belongsTo(User::class, 'host_id');
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
            'event_users',
            'event_id',
            'user_id'
        );
    }
}
