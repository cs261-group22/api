<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SessionResource extends JsonResource
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
            'mood' => $this->mood,
            'started_at' => $this->started_at,
            'is_submitted' => $this->is_submitted,

            'event' => new EventResource(
                $this->whenLoaded('event')
            ),

            'user' => new UserResource(
                $this->whenLoaded('user')
            ),

            'responses' => ResponseResource::collection(
                $this->whenLoaded('responses')
            ),
        ];
    }
}
