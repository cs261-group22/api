<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

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
        'code',
        'ends_at',
        'is_draft',
        'starts_at',
        'description',
        'allow_guests',
        'max_sessions',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'ends_at' => 'datetime',
        'is_draft' => 'boolean',
        'starts_at' => 'datetime',
        'allow_guests' => 'boolean',
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

        $query->whereHas('hosts', fn (Builder $query) => $query->where('id', $user->id));
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

        return $this->hosts()->where('id', $user->id)->exists();
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
     * Get the hosts many-to-many relationship.
     *
     * @return BelongsToMany
     */
    public function hosts()
    {
        return $this->belongsToMany(
            User::class,
            'event_hosts',
            'event_id',
            'user_id'
        );
    }

    /**
     * Get the attendees many-to-many relationship.
     *
     * @return BelongsToMany
     */
    public function attendees()
    {
        return $this->belongsToMany(
            User::class,
            'event_attendees',
            'event_id',
            'user_id'
        );
    }

    /**
     * Get the questions one-to-many relationship.
     *
     * @return HasMany
     */
    public function questions()
    {
        return $this->hasMany(Question::class, 'event_id');
    }

    /**
     * Get the sessions one-to-many relationship.
     *
     * @return HasMany
     */
    public function sessions()
    {
        return $this->hasMany(Session::class, 'event_id');
    }

    /**
     * Get the responses one-to-many relationship.
     *
     * @return HasManyThrough
     */
    public function responses()
    {
        return $this->hasManyThrough(
            Response::class, Session::class, 'event_id', 'session_id'
        );
    }
}
