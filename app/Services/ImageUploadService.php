<?php

namespace App\Services;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;

class ImageUploadService
{
    private ImageManager $manager;

    public function __construct()
    {
        $this->manager = ImageManager::gd();
    }

    /**
     * Resize, re-encode as WebP, and store locally or in Vercel Blob.
     * Returns a relative local path or the public Blob URL.
     */
    public function store(UploadedFile $file, string $directory, int $maxWidth = 1600): string
    {
        $path = trim($directory, '/').'/'.Str::uuid().'.webp';

        $image = $this->manager->read($file->getRealPath());
        $image->scaleDown(width: $maxWidth);

        $contents = (string) $image->toWebp(85);

        if (config('uploads.driver') === 'vercel-blob') {
            $response = $this->blobRequest()
                ->withHeaders([
                    'x-vercel-blob-access' => 'public',
                    'x-add-random-suffix' => '0',
                    'x-allow-overwrite' => '0',
                    'x-content-type' => 'image/webp',
                ])
                ->withBody($contents, 'image/webp')
                ->put('https://vercel.com/api/blob/?'.http_build_query(['pathname' => $path]))
                ->throw();

            $url = $response->json('url');
            if (! is_string($url) || ! $this->isBlobUrl($url)) {
                throw new \RuntimeException('The image storage service returned an invalid URL.');
            }

            return $url;
        }

        if (! Storage::disk('uploads')->put($path, $contents)) {
            throw new \RuntimeException('Unable to save the uploaded image.');
        }

        return $path;
    }

    public function delete(?string $path): void
    {
        if ($path && $this->isBlobUrl($path)) {
            $this->blobRequest()
                ->post('https://vercel.com/api/blob/delete', ['urls' => [$path]])
                ->throw();
        } elseif ($path) {
            Storage::disk('uploads')->delete($path);
        }
    }

    public static function url(string $path): string
    {
        return str_starts_with($path, 'https://') ? $path : asset('uploads/'.$path);
    }

    private function isBlobUrl(string $url): bool
    {
        return parse_url($url, PHP_URL_SCHEME) === 'https'
            && str_ends_with((string) parse_url($url, PHP_URL_HOST), '.public.blob.vercel-storage.com');
    }

    private function blobRequest(): PendingRequest
    {
        $token = config('uploads.blob_token');
        if (! is_string($token) || $token === '') {
            throw new \RuntimeException('BLOB_READ_WRITE_TOKEN is required for image storage.');
        }

        return Http::withToken($token)->withHeaders(['x-api-version' => '12'])
            ->connectTimeout(10)->timeout(30);
    }
}
