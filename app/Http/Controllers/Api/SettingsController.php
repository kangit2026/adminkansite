<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\SiteSettingResource;
use App\Models\SiteSetting;

class SettingsController extends Controller
{
    public function show()
    {
        return new SiteSettingResource(SiteSetting::current());
    }
}
