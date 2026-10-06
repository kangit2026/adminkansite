<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class ContactsPage extends Model
{
    use HasTranslations;

    protected $table = 'contacts_page';

    public array $translatable = [
        'heading',
        'name_label',
        'phone_label',
        'email_label',
        'message_label',
        'send_label',
        'success_message',
        'error_message',
        'send_again_label',
        'meta_title',
        'meta_description',
    ];

    protected $fillable = [
        'heading',
        'name_label',
        'phone_label',
        'email_label',
        'message_label',
        'send_label',
        'success_message',
        'error_message',
        'send_again_label',
        'map_enabled',
        'map_lat',
        'map_lng',
        'map_zoom',
        'meta_title',
        'meta_description',
        'og_image',
    ];

    protected $casts = [
        'map_enabled' => 'boolean',
        'map_lat' => 'float',
        'map_lng' => 'float',
        'map_zoom' => 'integer',
    ];

    public static function current(): self
    {
        return static::query()->firstOrCreate(['id' => 1]);
    }
}
