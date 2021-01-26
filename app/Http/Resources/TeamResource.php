<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TeamResource extends JsonResource
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
            'department' => $this->department,
            'users' => UserResource::collection($this->whenLoaded('users')),

            'is_leader' => $this->whenPivotLoaded('team_users', function () {
                return (bool) $this->pivot->is_leader;
            }),
        ];
    }
}
