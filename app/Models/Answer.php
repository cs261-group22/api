<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;

class Answer extends Model implements Sortable
{
    use HasFactory, SortableTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'order',
        'value',
        'question_id',
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
        return static::query()->where('question_id', $this->question_id);
    }

    /**
     * Get the question one-to-many relationship.
     *
     * @return BelongsTo
     */
    public function question()
    {
        return $this->belongsTo(Question::class, 'question_id');
    }

    /**
     * Get the responses one-to-many relationship.
     *
     * @return HasMany
     */
    public function responses()
    {
        return $this->hasMany(Response::class, 'answer_id');
    }
}
