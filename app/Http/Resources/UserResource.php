<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     *
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'is_admin' => $this->is_admin,
            'email_verified_at' => $this->email_verified_at,

            'is_leader' => $this->whenPivotLoaded('team_users', function () {
                return (bool) $this->pivot->is_leader;
            }),

            'teams' => TeamResource::collection($this->whenLoaded('teams')),
        ];
    }
}
