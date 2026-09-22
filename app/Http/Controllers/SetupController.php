<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use Database\Seeders\CategorySeeder;
use Database\Seeders\ProductSeeder;
use Database\Seeders\SettingSeeder;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use Throwable;

class SetupController extends Controller
{
    public function show(): Response
    {
        $this->abortIfInstalled();

        return $this->render();
    }

    public function store(Request $request): Response
    {
        $this->abortIfInstalled();

        $values = [
            'admin_name' => trim((string) $request->input('admin_name')),
            'admin_email' => mb_strtolower(trim((string) $request->input('admin_email'))),
        ];

        $validator = Validator::make($request->all(), [
            'setup_token' => ['required', 'string'],
            'admin_name' => ['required', 'string', 'max:255'],
            'admin_email' => ['required', 'email', 'max:255'],
            'admin_password' => ['required', 'confirmed', Password::min(12)->mixedCase()->numbers()],
        ], [], [
            'setup_token' => __('setup.token'),
            'admin_name' => __('setup.admin_name'),
            'admin_email' => __('setup.admin_email'),
            'admin_password' => __('setup.admin_password'),
        ]);

        if ($validator->fails()) {
            return $this->render($validator->errors()->all(), $values, status: 422);
        }

        $configuredToken = (string) config('setup.token');

        if (strlen($configuredToken) < 32) {
            return $this->render([__('setup.token_not_configured')], $values, status: 503);
        }

        if (! hash_equals($configuredToken, (string) $request->input('setup_token'))) {
            Log::warning('Invalid setup token submitted.', ['ip' => $request->ip()]);

            return $this->render([__('setup.token_invalid')], $values, status: 403);
        }

        if (! $this->appKeyIsConfigured()) {
            return $this->render([__('setup.app_key_missing')], $values, status: 503);
        }

        try {
            $this->prepareDatabase();

            Artisan::call('migrate', ['--force' => true]);
        } catch (Throwable $exception) {
            report($exception);

            return $this->render([__('setup.installation_failed')], $values, status: 500);
        }

        if (Admin::query()->exists()) {
            abort(404);
        }

        try {
            foreach ([CategorySeeder::class, ProductSeeder::class, SettingSeeder::class] as $seeder) {
                Artisan::call('db:seed', [
                    '--class' => $seeder,
                    '--force' => true,
                ]);
            }

            $admin = Admin::create([
                'name' => $values['admin_name'],
                'email' => $values['admin_email'],
                'password' => (string) $request->input('admin_password'),
            ]);

            Log::notice('Application setup completed.', [
                'admin_id' => $admin->id,
                'ip' => $request->ip(),
            ]);
        } catch (Throwable $exception) {
            report($exception);

            return $this->render([__('setup.installation_failed')], $values, status: 500);
        }

        return $this->render(values: $values, complete: true);
    }

    private function prepareDatabase(): void
    {
        $connectionName = (string) config('database.default');
        $connection = DB::connection($connectionName);
        $connection->getPdo();

        if ($connection->getDriverName() !== 'pgsql') {
            return;
        }

        $schema = (string) config("database.connections.{$connectionName}.search_path", 'public');

        if ($schema === 'public') {
            return;
        }

        if (! preg_match('/^[A-Za-z_][A-Za-z0-9_]*$/', $schema)) {
            throw new \RuntimeException('DB_SCHEMA must contain one valid PostgreSQL identifier.');
        }

        $quotedSchema = '"'.str_replace('"', '""', $schema).'"';
        $connection->statement("create schema if not exists {$quotedSchema}");

        DB::purge($connectionName);
        DB::reconnect($connectionName);
    }

    private function abortIfInstalled(): void
    {
        $installed = false;

        try {
            $installed = Schema::hasTable('admins') && Admin::query()->exists();
        } catch (Throwable) {
            // A missing or unreachable database is exactly what setup handles.
        }

        if ($installed) {
            abort(404);
        }
    }

    /** @return array{ready: bool, driver: string, schema: string|null} */
    private function databaseStatus(): array
    {
        $connectionName = (string) config('database.default');
        $schema = config("database.connections.{$connectionName}.search_path");

        try {
            DB::connection($connectionName)->getPdo();

            return ['ready' => true, 'driver' => $connectionName, 'schema' => is_string($schema) ? $schema : null];
        } catch (Throwable) {
            return ['ready' => false, 'driver' => $connectionName, 'schema' => is_string($schema) ? $schema : null];
        }
    }

    private function appKeyIsConfigured(): bool
    {
        return trim((string) config('app.key')) !== '';
    }

    /** @param list<string> $errors */
    private function render(
        array $errors = [],
        array $values = [],
        bool $complete = false,
        int $status = 200,
    ): Response {
        return response()
            ->view('setup', [
                'complete' => $complete,
                'errors' => $errors,
                'values' => $values,
                'database' => $this->databaseStatus(),
                'tokenReady' => strlen((string) config('setup.token')) >= 32,
                'appKeyReady' => $this->appKeyIsConfigured(),
            ], $status)
            ->header('Cache-Control', 'no-store, private')
            ->header('X-Robots-Tag', 'noindex, nofollow, noarchive');
    }
}
