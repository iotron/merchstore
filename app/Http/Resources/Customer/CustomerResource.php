<?php

namespace App\Http\Resources\Customer;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CustomerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'name' => $this->when(isset($this->name),$this->name),
            'email' => $this->when(isset($this->email),$this->email),
            'contact' => $this->when(isset($this->contact),$this->contact),
            'whatsapp' => $this->when(isset($this->whatsapp),$this->whatsapp),
            'alt_contact' => $this->when(isset($this->alt_contact),$this->alt_contact),
            'email_verified_at' => $this->when(isset($this->email_verified_at),$this->email_verified_at),
            'contact_verified_at' => $this->when(isset($this->contact_verified_at),$this->contact_verified_at),
            'status' => $this->when(isset($this->status),$this->status),
            'last_login' => $this->when(isset($this->last_login),$this->last_login),
            'referrer' => $this->when(isset($this->referrer),$this->referrer),
            'has_verified_email' => $this->when(isset($this->email_verified_at),$this->email_verified_at),
            'has_verified_contact' => $this->when(isset($this->contact_verified_at),$this->contact_verified_at),
            'has_password' => !is_null($this->password),
            'has_social' => !empty($this->socials->count()),
           // 'socials' => CustomerSocialResource::collection($this->whenLoaded('socials')),
        ];
    }
}
