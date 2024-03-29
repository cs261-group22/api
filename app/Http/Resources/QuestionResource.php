<?php

namespace App\Http\Resources;

use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class QuestionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'type' => $this->type,
            'order' => $this->order,
            'prompt' => $this->prompt,
            'min_responses' => $this->min_responses,
            'max_responses' => $this->max_responses,

            'answers' => $this->when(
                $this->type === Question::TYPE_MULTIPLE_CHOICE,
                fn () => AnswerResource::collection($this->whenLoaded('answers'))
            ),
        ];
    }
}
