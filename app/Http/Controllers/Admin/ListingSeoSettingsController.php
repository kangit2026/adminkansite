<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use Illuminate\Http\Request;
use InvalidArgumentException;

class ListingSeoSettingsController extends Controller
{
    /** @return array<string, array{title: string, path: string, prefix: string, tabs: string}> */
    public static function sections(): array
    {
        return [
            'news' => [
                'title' => 'Новини',
                'path' => '/news',
                'prefix' => 'news_meta',
                'tabs' => 'admin._news_section_tabs',
            ],
            'projects' => [
                'title' => 'Проєкти',
                'path' => '/projects',
                'prefix' => 'projects_meta',
                'tabs' => 'admin._projects_section_tabs',
            ],
        ];
    }

    public function edit(string $section)
    {
        $config = $this->config($section);
        $settings = SiteSetting::current();

        return view('admin.listing-seo.edit', [
            'settings' => $settings,
            'config' => $config,
            'section' => $section,
        ]);
    }

    public function update(Request $request, string $section)
    {
        $config = $this->config($section);
        $prefix = $config['prefix'];

        $data = $request->validate([
            "{$prefix}_title.uk" => ['nullable', 'string', 'max:70'],
            "{$prefix}_title.en" => ['nullable', 'string', 'max:70'],
            "{$prefix}_description.uk" => ['nullable', 'string', 'max:160'],
            "{$prefix}_description.en" => ['nullable', 'string', 'max:160'],
        ]);

        SiteSetting::current()->update($data);

        return back()->with('status', 'SEO збережено.');
    }

    /** @return array{title: string, path: string, prefix: string, tabs: string} */
    private function config(string $section): array
    {
        $sections = static::sections();

        if (! isset($sections[$section])) {
            throw new InvalidArgumentException("Unknown listing SEO section: {$section}");
        }

        return $sections[$section];
    }
}
