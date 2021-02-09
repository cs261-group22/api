<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Response extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'value',
        'answer_id',
        'sentiment',
        'session_id',
        'question_id',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'sentiment' => 'json',
    ];

    /**
     * Get the session one-to-many relationship.
     *
     * @return BelongsTo
     */
    public function session()
    {
        return $this->belongsTo(Session::class, 'session_id');
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
     * Get the answer one-to-many relationship.
     *
     * @return BelongsTo
     */
    public function answer()
    {
        return $this->belongsTo(Answer::class, 'answer_id');
    }
}
