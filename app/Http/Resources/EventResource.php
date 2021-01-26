<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EventResource extends JsonResource
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
            'name' => $this->name,
            'code' => $this->code,
            'ends_at' => $this->ends_at,
            'starts_at' => $this->starts_at,
            'description' => $this->description,
            'allow_guests' => $this->allow_guests,
            'is_draft' => $this->is_draft,

            'host' => new UserResource($this->whenLoaded('host')),
            'users' => UserResource::collection($this->whenLoaded('users')),
        ];
    }
}
