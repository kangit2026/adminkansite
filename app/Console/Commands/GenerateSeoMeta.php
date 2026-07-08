<?php

namespace App\Console\Commands;

use App\Models\News;
use App\Models\Project;
use Illuminate\Console\Command;

/**
 * One-off bulk generation of meta_title/meta_description for News and
 * Project records that don't have them yet (from title/text), so the site
 * doesn't ship with empty SEO fields while content is still being reviewed
 * in the admin. Safe to re-run: skips fields that already have a value
 * unless --force is passed.
 */
class GenerateSeoMeta extends Command
{
    protected $signature = 'seo:generate-meta {--type=all : news|projects|all} {--force : overwrite existing meta fields}';

    protected $description = 'Generate meta_title/meta_description for News and/or Project records from their title/text';

    private const TITLE_MAX = 70;

    private const DESCRIPTION_MAX = 157;

    public function handle(): int
    {
        $type = $this->option('type');
        $force = (bool) $this->option('force');

        if ($type === 'news' || $type === 'all') {
            $this->generateFor(News::query()->get(), $force, true);
        }

        if ($type === 'projects' || $type === 'all') {
            $this->generateFor(Project::query()->get(), $force, false);
        }

        return self::SUCCESS;
    }

    /**
     * @param  \Illuminate\Support\Collection<int, \App\Models\News|\App\Models\Project>  $items
     */
    private function generateFor($items, bool $force, bool $isNews): void
    {
        $label = $isNews ? 'News' : 'Project';
        $bar = $this->output->createProgressBar($items->count());
        $bar->start();
        $updated = 0;

        foreach ($items as $item) {
            $titleAttr = $isNews ? 'title' : 'name';
            $changed = false;

            foreach (['uk', 'en'] as $locale) {
                $title = trim((string) $item->getTranslation($titleAttr, $locale));
                $text = trim((string) $item->getTranslation('text', $locale));

                if ($title === '') {
                    continue; // немає перекладу на цю мову — нічого генерувати
                }

                $hasMetaTitle = trim((string) $item->getTranslation('meta_title', $locale)) !== '';
                $hasMetaDescription = trim((string) $item->getTranslation('meta_description', $locale)) !== '';

                if ($force || ! $hasMetaTitle) {
                    $item->setTranslation('meta_title', $locale, $this->buildTitle($title));
                    $changed = true;
                }

                if (($force || ! $hasMetaDescription) && $text !== '') {
                    $item->setTranslation('meta_description', $locale, $this->buildDescription($text));
                    $changed = true;
                }
            }

            if ($changed) {
                $item->save();
                $updated++;
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info("{$label}: updated {$updated} / {$items->count()}.");
    }

    private function buildTitle(string $title): string
    {
        $prefix = 'KAN | ';
        $full = $prefix.$title;

        if (mb_strlen($full) <= self::TITLE_MAX) {
            return $full;
        }

        $available = self::TITLE_MAX - mb_strlen($prefix);

        return $prefix.$this->truncate($title, $available);
    }

    private function buildDescription(string $text): string
    {
        return $this->truncate($text, self::DESCRIPTION_MAX);
    }

    private function truncate(string $text, int $maxLen): string
    {
        $text = trim(preg_replace('/\s+/u', ' ', strip_tags($text)));

        if (mb_strlen($text) <= $maxLen) {
            return $text;
        }

        $truncated = mb_substr($text, 0, max($maxLen - 1, 0));
        $lastSpace = mb_strrpos($truncated, ' ');

        if ($lastSpace !== false) {
            $truncated = mb_substr($truncated, 0, $lastSpace);
        }

        return rtrim($truncated, " .,;:—-").'…';
    }
}
