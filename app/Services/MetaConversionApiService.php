<?php

namespace App\Services;

use App\Enums\TrackingEventName;
use App\Models\Order;
use App\Models\Product;
use App\Models\TrackingEvent;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class MetaConversionApiService
{
    public function __construct(private readonly SettingsService $settings) {}

    /**
     * Logs the event locally and (if configured) forwards it to Meta's Conversion API.
     * Returns the event_id so the caller can fire the matching client-side pixel event
     * with the same id, per Meta's browser+server dedup convention.
     */
    public function track(TrackingEventName $event, array $payload = [], ?Order $order = null, ?Product $product = null): string
    {
        $eventId = (string) Str::uuid();

        TrackingEvent::create([
            'event_name' => $event->value,
            'order_id' => $order?->id,
            'product_id' => $product?->id,
            'event_id' => $eventId,
            'payload' => $payload,
            'fbp' => request()->cookie('_fbp'),
            'fbc' => request()->cookie('_fbc'),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        $this->sendServerEvent($event, $eventId, $payload);

        return $eventId;
    }

    private function sendServerEvent(TrackingEventName $event, string $eventId, array $payload): void
    {
        $pixelId = $this->settings->get('meta_pixel_id');
        $token = $this->settings->get('conversion_api_token');

        if (! $pixelId || ! $token) {
            return;
        }

        try {
            Http::asJson()->connectTimeout(2)->timeout(4)->post("https://graph.facebook.com/v21.0/{$pixelId}/events", [
                'data' => [[
                    'event_name' => $event->value,
                    'event_time' => now()->timestamp,
                    'event_id' => $eventId,
                    'action_source' => 'website',
                    'event_source_url' => request()->fullUrl(),
                    'user_data' => [
                        'client_ip_address' => request()->ip(),
                        'client_user_agent' => request()->userAgent(),
                        'fbp' => request()->cookie('_fbp'),
                        'fbc' => request()->cookie('_fbc'),
                    ],
                    'custom_data' => $payload,
                ]],
                'access_token' => $token,
            ])->throw();
        } catch (\Throwable $e) {
            Log::warning('Meta Conversion API request failed.', [
                'exception' => $e::class,
            ]);
        }
    }
}
