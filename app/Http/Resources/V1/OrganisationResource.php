<?php

namespace App\Http\Resources\V1;

use App\Models\Organisation;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Organisation
 */
class OrganisationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'is_owner' => $this->owner_id === $request->user()?->getKey(),
            'created_at' => $this->created_at,
        ];
    }
}
