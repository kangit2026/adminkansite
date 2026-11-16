<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\SiteAnalytics;

/** Сторінка «Статистика» (меню «Загальне»): відвідуваність сайту та заявки й відгуки. Лише для адміністраторів. */
class StatisticsController extends Controller
{
    public function index(SiteAnalytics $siteAnalytics)
    {
        return view('admin.statistics.index', ['analytics' => $siteAnalytics->report()]);
    }
}
