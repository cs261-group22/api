<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Answer extends Model
{
    use HasFactory;

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
