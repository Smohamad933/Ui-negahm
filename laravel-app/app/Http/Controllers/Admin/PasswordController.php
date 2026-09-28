<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class PasswordController extends Controller
{
    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'current_password' => ['required', 'string'],
            'new_password' => ['required', 'string', 'min:6', 'confirmed'],
        ], [
            'new_password.confirmed' => 'رمز جدید و تکرار آن یکسان نیستند.',
            'new_password.min' => 'رمز جدید باید حداقل ۶ کاراکتر باشد.',
        ]);

        $admin = Auth::guard('admin')->user();

        if (! Hash::check($data['current_password'], $admin->password_hash)) {
            return back()
                ->withErrors(['current_password' => 'رمز فعلی اشتباه است.'])
                ->with('open_section', 'security');
        }

        $admin->update(['password_hash' => Hash::make($data['new_password'])]);

        return redirect()->route('admin.settings.edit', ['section' => 'security'])
            ->with('status', 'رمز عبور با موفقیت تغییر کرد.');
    }
}
