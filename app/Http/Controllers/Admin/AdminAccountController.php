<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class AdminAccountController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $request->merge([
            'email' => Str::lower(trim((string) $request->input('email'))),
        ]);

        $validated = $request->validateWithBag('createAdmin', [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('admins', 'email')],
            'password' => ['required', 'confirmed', Password::min(12)->mixedCase()->numbers()],
        ]);

        $admin = Admin::create($validated);

        Log::notice('Administrator account created.', [
            'actor_admin_id' => $request->user('admin')->id,
            'created_admin_id' => $admin->id,
            'ip' => $request->ip(),
        ]);

        return back()->with('status', __('admin.settings_admin_created'));
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $validated = $request->validateWithBag('updatePassword', [
            'current_password' => ['required', 'current_password:admin'],
            'new_password' => ['required', 'confirmed', Password::min(12)->mixedCase()->numbers()],
        ]);

        $admin = $request->user('admin');
        $admin->update([
            'password' => $validated['new_password'],
        ]);

        if (config('session.driver') === 'database') {
            DB::table((string) config('session.table', 'sessions'))
                ->where('user_id', $admin->id)
                ->where('id', '!=', $request->session()->getId())
                ->delete();
        }

        $request->session()->regenerate();

        Log::notice('Administrator password changed.', [
            'admin_id' => $admin->id,
            'ip' => $request->ip(),
        ]);

        return back()->with('status', __('admin.settings_password_changed'));
    }
}
