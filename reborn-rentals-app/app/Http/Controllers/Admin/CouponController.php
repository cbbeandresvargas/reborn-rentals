<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Cupon;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    public function index()
    {
        $coupons = Cupon::latest()->paginate(15);
        return view('admin.coupons.index', compact('coupons'));
    }

    public function create()
    {
        return view('admin.coupons.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:255|unique:cupons,code',
            'discount_type' => 'required|in:percentage,fixed',
            'discount_value' => 'required|numeric|min:0',
            'max_uses' => 'nullable|integer|min:1',
            'min_order_total' => 'nullable|numeric|min:0',
            'starts_at' => 'nullable|date',
            'expires_at' => 'nullable|date|after:starts_at',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        Cupon::create($validated);

        return redirect()->route('admin.coupons.index')
            ->with('success', 'Coupon created successfully');
    }

    public function show(Cupon $coupon)
    {
        return view('admin.coupons.show', compact('coupon'));
    }

    public function edit(Cupon $coupon)
    {
        return view('admin.coupons.edit', compact('coupon'));
    }

    public function update(Request $request, Cupon $coupon)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:255|unique:cupons,code,' . $coupon->id,
            'discount_type' => 'required|in:percentage,fixed',
            'discount_value' => 'required|numeric|min:0',
            'max_uses' => 'nullable|integer|min:1',
            'min_order_total' => 'nullable|numeric|min:0',
            'starts_at' => 'nullable|date',
            'expires_at' => 'nullable|date|after:starts_at',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $coupon->update($validated);

        return redirect()->route('admin.coupons.index')
            ->with('success', 'Coupon updated successfully');
    }

    public function destroy(Cupon $coupon)
    {
        $coupon->delete();

        return redirect()->route('admin.coupons.index')
            ->with('success', 'Coupon deleted successfully');
    }
}
