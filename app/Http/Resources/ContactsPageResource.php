<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\ContactsPage */
class ContactsPageResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'heading' => $this->heading,
            'nameLabel' => $this->name_label,
            'phoneLabel' => $this->phone_label,
            'emailLabel' => $this->email_label,
            'messageLabel' => $this->message_label,
            'sendLabel' => $this->send_label,
            'successMessage' => $this->success_message,
            'errorMessage' => $this->error_message,
            'sendAgainLabel' => $this->send_again_label,
            'map' => [
                'enabled' => (bool) $this->map_enabled,
                'lat' => $this->map_lat,
                'lng' => $this->map_lng,
                'zoom' => $this->map_zoom ?? 16,
            ],
            'metaTitle' => $this->meta_title,
            'metaDescription' => $this->meta_description,
            'ogImage' => $this->og_image ? asset('storage/'.$this->og_image) : null,
        ];
    }
}
