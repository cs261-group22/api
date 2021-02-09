<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Question extends Model
{
    use HasFactory;

    // Question type constants
    const TYPE_FREE_TEXT = 'free_text';
    const TYPE_MULTIPLE_CHOICE = 'multiple_choice';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'type',
        'order',
        'prompt',
        'event_id',
        'min_responses',
        'max_responses',
    ];

    /**
     * Get the event one-to-many relationship.
     *
     * @return BelongsTo
     */
    public function event()
    {
        return $this->belongsTo(
            Event::class, 'event_id'
        );
    }

    /**
     * Get the responses one-to-many relationship.
     *
     * @return HasMany
     */
    public function responses()
    {
        return $this->hasMany(
            Response::class, 'question_id'
        );
    }

    /**
     * Get the answers one-to-many relationship.
     *
     * @return HasMany
     */
    public function answers()
    {
        return $this->hasMany(
            Answer::class, 'question_id'
        );
    }
}
