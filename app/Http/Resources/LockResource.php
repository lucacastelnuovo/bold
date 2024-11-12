<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LockResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->id,
            'bold_id'    => $this->bold_id,
            'bold_name'  => $this->bold_name,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            // 'activity'   => ActivityResource::collection($this->actions), // TODO: flip order
        ];
    }
}
