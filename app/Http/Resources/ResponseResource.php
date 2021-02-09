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
