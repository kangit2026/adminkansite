<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContactSubmission extends Model
{
    protected $fillable = [
        'name',
        'phone',
        'email',
        'message',
        'source_page',
        'ip_address',
        'user_agent',
        'status',
    ];
}
