<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PromoCodeRequest;
use App\Models\PromoCode;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PromoCodeController extends Controller
{
    public function index(): View
    {
        $promoCodes = PromoCode::latest()->paginate(15);

        return view('admin.promo-codes.index', compact('promoCodes'));
    }

    public function create(): View
    {
        return view('admin.promo-codes.form', ['promoCode' => new PromoCode]);
    }

    public function store(PromoCodeRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['code'] = Str::upper($data['code']);
        $data['is_active'] = $request->boolean('is_active');

        PromoCode::create($data);

        return redirect()->route('admin.promo-codes.index')->with('status', __('admin.promo_created'));
    }

    public function edit(PromoCode $promoCode): View
    {
        return view('admin.promo-codes.form', compact('promoCode'));
    }

    public function update(PromoCodeRequest $request, PromoCode $promoCode): RedirectResponse
    {
        $data = $request->validated();
        $data['code'] = Str::upper($data['code']);
        $data['is_active'] = $request->boolean('is_active');

        $promoCode->update($data);

        return redirect()->route('admin.promo-codes.index')->with('status', __('admin.promo_updated'));
    }

    public function destroy(PromoCode $promoCode): RedirectResponse
    {
        $promoCode->delete();

        return redirect()->route('admin.promo-codes.index')->with('status', __('admin.promo_deleted'));
    }

    public function toggle(PromoCode $promoCode): RedirectResponse
    {
        $promoCode->update(['is_active' => ! $promoCode->is_active]);

        return back()->with('status', $promoCode->is_active ? __('admin.promo_activated') : __('admin.promo_deactivated'));
    }
}
