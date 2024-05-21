<?php

namespace App\Http\Resources\Customer;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CustomerIndexResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'name' => $this->whenNotNull($this->name),
            'contact' => $this->whenNotNull($this->contact),
            'alt_contact' => $this->whenNotNull($this->alt_contact),
            'email' => $this->whenNotNull($this->email),
            'has_whatsapp' => $this->when($this->has_whatsapp, $this->has_whatsapp),
            'referrer' => $this->whenNotNull($this->referrer),
            // 'createOn' => $this->whenNotNull($this->created_at),
        ];
    }
}
