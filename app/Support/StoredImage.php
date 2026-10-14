<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Imagick;
use Throwable;

class StoredImage
{
    public const MAX_EDGE = 1920;

    public const JPEG_QUALITY = 82;

    public static function store(UploadedFile $file, string $directory): string
    {
        $path = $file->store($directory, 'public');
        self::backupAndResize($path);

        return $path;
    }

    /**
     * Копія оригіналу лежить поза публічним диском:
     * storage/app/originals/...
     * Повторний запуск бере пікселі з копії, а не з уже зменшеного файлу.
     */
    public static function backupAndResize(string $relativePath): string
    {
        try {
            return self::process($relativePath);
        } catch (Throwable $e) {
            report($e);

            return 'failed';
        }
    }

    public static function process(string $relativePath): string
    {
        $relativePath = ltrim($relativePath, '/');
        $public = Storage::disk('public')->path($relativePath);

        if (! is_file($public)) {
            return 'missing';
        }

        $backup = storage_path('app/originals/'.$relativePath);

        if (! is_file($backup)) {
            File::ensureDirectoryExists(dirname($backup));
            if (! copy($public, $backup)) {
                throw new \RuntimeException('Не вдалося зберегти оригінал '.$relativePath);
            }
        }

        $before = filesize($public);
        self::writeWebSize($backup, $public);
        $after = filesize($public);

        return $before.' -> '.$after;
    }

    private static function writeWebSize(string $source, string $dest): void
    {
        $image = new Imagick($source.'[0]');
        $image->autoOrient();

        if ($image->getImageColorspace() === Imagick::COLORSPACE_CMYK) {
            $image->transformImageColorspace(Imagick::COLORSPACE_SRGB);
        }

        $width = $image->getImageWidth();
        $height = $image->getImageHeight();

        if ($width > self::MAX_EDGE || $height > self::MAX_EDGE) {
            $image->thumbnailImage(self::MAX_EDGE, self::MAX_EDGE, true);
        }

        $format = strtolower($image->getImageFormat());

        if (in_array($format, ['jpeg', 'jpg'], true)) {
            $image->setImageFormat('jpeg');
            $image->setImageCompression(Imagick::COMPRESSION_JPEG);
            $image->setImageCompressionQuality(self::JPEG_QUALITY);
            $image->setInterlaceScheme(Imagick::INTERLACE_PLANE);
        } elseif ($format === 'png') {
            $image->setImageFormat('png');
            $image->setOption('png:compression-level', '9');
        }

        $image->stripImage();

        $extension = pathinfo($dest, PATHINFO_EXTENSION) ?: $format;
        $tmp = $dest.'.resizing.'.$extension;
        $image->writeImage($tmp);
        $image->clear();

        if (filesize($tmp) >= filesize($source)) {
            @unlink($tmp);
            if (! is_file($dest) || filesize($dest) !== filesize($source)) {
                copy($source, $dest);
            }

            return;
        }

        if (! rename($tmp, $dest)) {
            @unlink($tmp);
            throw new \RuntimeException('Не вдалося замінити '.$dest);
        }

        @chmod($dest, 0644);
        @chown($dest, 'www-data');
        @chgrp($dest, 'www-data');
    }
}
