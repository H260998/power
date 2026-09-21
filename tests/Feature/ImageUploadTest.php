<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\ProductImage;
use App\Services\ImageUploadService;
use Illuminate\Http\Client\Request;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ImageUploadTest extends TestCase
{
    public function test_local_images_are_resized_stored_and_deleted(): void
    {
        config(['uploads.driver' => 'local']);
        Storage::fake('uploads');
        $service = app(ImageUploadService::class);
        $path = $service->store(UploadedFile::fake()->image('test.png', 2000, 1000), 'products');
        Storage::disk('uploads')->assertExists($path);
        $dimensions = getimagesizefromstring(Storage::disk('uploads')->get($path));
        $this->assertSame([1600, 800], [$dimensions[0], $dimensions[1]]);
        $this->assertSame('image/webp', $dimensions['mime']);
        $service->delete($path);
        Storage::disk('uploads')->assertMissing($path);
    }

    public function test_blob_uploads_keep_the_remote_url_and_delete_remotely(): void
    {
        config(['uploads.driver' => 'vercel-blob', 'uploads.blob_token' => 'test-token']);
        $url = 'https://test.public.blob.vercel-storage.com/products/test.webp';
        Http::preventStrayRequests();
        Http::fake([
            'https://vercel.com/api/blob/delete' => Http::response([], 200),
            'https://vercel.com/api/blob/*' => Http::response(['url' => $url], 200),
        ]);
        $service = app(ImageUploadService::class);
        $path = $service->store(UploadedFile::fake()->image('test.png'), 'products');
        $this->assertSame($url, $path);
        $this->assertSame($url, (new ProductImage(['path' => $path]))->url());
        $this->assertSame($url, (new Category(['image' => $path]))->imageUrl());
        $service->delete($path);
        Http::assertSent(fn (Request $request) => $request->method() === 'PUT'
            && $request->hasHeader('Authorization', 'Bearer test-token')
            && $request->hasHeader('x-content-type', 'image/webp'));
        Http::assertSent(fn (Request $request) => $request->method() === 'POST'
            && $request['urls'] === [$url]);
    }

    public function test_failed_blob_upload_does_not_return_a_broken_image_path(): void
    {
        config(['uploads.driver' => 'vercel-blob', 'uploads.blob_token' => 'test-token']);
        Http::fake(['https://vercel.com/api/blob/*' => Http::response([], 503)]);
        $this->expectException(RequestException::class);
        app(ImageUploadService::class)->store(UploadedFile::fake()->image('test.png'), 'products');
    }
}
