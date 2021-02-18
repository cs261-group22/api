<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;

class Question extends Model implements Sortable
{
    use HasFactory, SortableTrait;

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
     * The model's ordering options.
     *
     * @var array
     */
    public $sortable = [
        'order_column_name' => 'order',
    ];

    /**
     * Build the query used to group questions when ordering.
     *
     * @return Builder
     */
    public function buildSortQuery()
    {
        return static::query()->where('event_id', $this->event_id);
    }

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
        return $this->hasMany(Answer::class, 'question_id')->ordered();
    }
}
