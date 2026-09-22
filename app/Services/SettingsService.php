<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;

class SettingsService
{
    private const CACHE_KEY = 'settings.all';

    private const ENCRYPTED_PREFIX = 'enc:v1:';

    private const SECRET_KEYS = ['conversion_api_token'];

    public function get(string $key, mixed $default = null): mixed
    {
        $value = $this->all()->get($key, $default);

        if (! in_array($key, self::SECRET_KEYS, true) || ! is_string($value) || $value === '') {
            return $value;
        }

        if (! str_starts_with($value, self::ENCRYPTED_PREFIX)) {
            $this->set($key, $value);

            return $value;
        }

        try {
            return Crypt::decryptString(substr($value, strlen(self::ENCRYPTED_PREFIX)));
        } catch (DecryptException) {
            Log::error('Unable to decrypt a protected setting.', ['setting' => $key]);

            return $default;
        }
    }

    public function set(string $key, mixed $value): void
    {
        Setting::updateOrCreate(['key' => $key], ['value' => $this->protect($key, $value)]);
        Cache::forget(self::CACHE_KEY);
    }

    public function setMany(array $values): void
    {
        foreach ($values as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $this->protect($key, $value)]);
        }
        Cache::forget(self::CACHE_KEY);
    }

    public function all(): Collection
    {
        return Cache::rememberForever(
            self::CACHE_KEY,
            fn () => Setting::all()->pluck('value', 'key')
        );
    }

    private function protect(string $key, mixed $value): mixed
    {
        if (! in_array($key, self::SECRET_KEYS, true) || ! is_string($value) || $value === '') {
            return $value;
        }

        if (str_starts_with($value, self::ENCRYPTED_PREFIX)) {
            return $value;
        }

        return self::ENCRYPTED_PREFIX.Crypt::encryptString($value);
    }
}
