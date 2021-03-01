<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ResponseResource extends JsonResource
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
            'value' => $this->value,
            'sentiment' => $this->sentiment,
            'answer_id' => $this->answer_id,
            'session_id' => $this->session_id,
            'question_id' => $this->question_id,

            'answer' => new AnswerResource(
                $this->whenLoaded('answer')
            ),

            'session' => new SessionResource(
                $this->whenLoaded('session')
            ),

            'question' => new QuestionResource(
                $this->whenLoaded('question')
            ),
        ];
    }
}
