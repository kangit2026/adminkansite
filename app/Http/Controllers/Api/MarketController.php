<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\MarketPageResource;
use App\Models\MarketPage;

class MarketController extends Controller
{
    public function show()
    {
        return new MarketPageResource(MarketPage::current());
    }
}
