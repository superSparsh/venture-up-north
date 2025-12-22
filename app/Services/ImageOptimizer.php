<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class ImageOptimizer
{
    /**
     * Optimize and store an uploaded image.
     *
     * @param UploadedFile $file
     * @param string $folder
     * @param string $disk
     * @param int|null $maxWidth
     * @param int|null $quality
     * @return string The path to the stored file.
     */
    public function optimizeAndStore(UploadedFile $file, string $folder, string $disk = 'public', ?int $maxWidth = 1920, int $quality = 80): string
    {
        // Create new manager instance with desired driver
        $manager = new ImageManager(new Driver());

        $image = $manager->read($file);

        // Resize if width is greater than max width
        if ($maxWidth && $image->width() > $maxWidth) {
            $image->scale(width: $maxWidth);
        }

        // Encode the image (defaulting to keeping original format but optimizing)
        // We can enforce WebP for better performance if desired, but sticking to original is safer for now
        // unless we want to change extensions. 
        // Let's optimize as WebP for clear performance gain, as all modern browsers support it.
        // However, we need to ensure the filename reflects that.

        $encoded = $image->toWebp(quality: $quality);
        $extension = 'webp';

        $filename = $file->hashName(); // Generates random name with extension usually, but we want new extension
        $filename = pathinfo($filename, PATHINFO_FILENAME) . '.' . $extension;

        $path = $folder . '/' . $filename;

        Storage::disk($disk)->put($path, (string) $encoded);

        return $path;
    }
}
