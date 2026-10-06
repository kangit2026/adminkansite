<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ContactsPageResource;
use App\Models\ContactsPage;

class ContactsController extends Controller
{
    public function show()
    {
        return new ContactsPageResource(ContactsPage::current());
    }
}
