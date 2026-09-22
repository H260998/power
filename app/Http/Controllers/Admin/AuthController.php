<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AdminLoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    public function showLoginForm(): Response
    {
        return response()->view('admin.auth.login')
            ->header('Cache-Control', 'no-store, private');
    }

    public function login(AdminLoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        Log::notice('Administrator logged in.', [
            'admin_id' => $request->user('admin')->id,
            'ip' => $request->ip(),
        ]);

        return redirect()->intended(route('admin.dashboard'));
    }

    public function logout(Request $request): RedirectResponse
    {
        $adminId = $request->user('admin')?->id;
        Auth::guard('admin')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        Log::notice('Administrator logged out.', [
            'admin_id' => $adminId,
            'ip' => $request->ip(),
        ]);

        return redirect()->route('admin.login');
    }
}
