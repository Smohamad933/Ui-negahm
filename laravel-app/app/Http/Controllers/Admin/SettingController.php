<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Font;
use App\Models\Setting;
use App\Support\Uploads;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingController extends Controller
{
    public function edit(): View
    {
        $settings = Setting::current();
        $fonts = Font::query()->orderByDesc('created_at')->get();
        $customFamilies = $fonts->pluck('family_name')->unique()->values();

        return view('admin.settings', compact('settings', 'fonts', 'customFamilies'));
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'site_name' => ['required', 'string', 'max:190'],
            'tagline' => ['nullable', 'string', 'max:255'],
            'color_bg' => ['required', 'string', 'max:20'],
            'color_fg' => ['required', 'string', 'max:20'],
            'color_primary' => ['required', 'string', 'max:20'],
            'color_secondary' => ['required', 'string', 'max:20'],
            'color_accent' => ['required', 'string', 'max:20'],
            'color_muted' => ['required', 'string', 'max:20'],
            'font_family' => ['required', 'string', 'max:190'],
            'hero_title' => ['required', 'string', 'max:500'],
            'hero_subtitle' => ['nullable', 'string', 'max:1000'],
            'hero_cta_text' => ['nullable', 'string', 'max:100'],
            'hero_cta_link' => ['nullable', 'string', 'max:255'],
            'about_title' => ['required', 'string', 'max:500'],
            'about_body' => ['nullable', 'string', 'max:5000'],
            'contact_address' => ['nullable', 'string', 'max:255'],
            'contact_phone' => ['nullable', 'string', 'max:60'],
            'contact_email' => ['nullable', 'string', 'max:190'],
            'contact_map_embed' => ['nullable', 'string', 'max:5000'],
            'social_instagram' => ['nullable', 'string', 'max:255'],
            'social_telegram' => ['nullable', 'string', 'max:255'],
            'social_whatsapp' => ['nullable', 'string', 'max:255'],
            'social_linkedin' => ['nullable', 'string', 'max:255'],
            'footer_text' => ['nullable', 'string', 'max:255'],
            'logo' => ['nullable', 'image', 'max:12288'],
            'favicon' => ['nullable', 'image', 'max:2048'],
            'about_image' => ['nullable', 'image', 'max:12288'],
            'remove_logo' => ['nullable', 'boolean'],
            'remove_favicon' => ['nullable', 'boolean'],
            'remove_about_image' => ['nullable', 'boolean'],
        ]);

        $settings = Setting::current();
        unset($data['logo'], $data['favicon'], $data['about_image'], $data['remove_logo'], $data['remove_favicon'], $data['remove_about_image']);

        if ($request->hasFile('logo')) {
            Uploads::deletePublicPath($settings->logo_url);
            $data['logo_url'] = Uploads::store($request->file('logo'), 'images');
        } elseif ($request->boolean('remove_logo')) {
            Uploads::deletePublicPath($settings->logo_url);
            $data['logo_url'] = '';
        }

        if ($request->hasFile('favicon')) {
            Uploads::deletePublicPath($settings->favicon_url);
            $data['favicon_url'] = Uploads::store($request->file('favicon'), 'images');
        } elseif ($request->boolean('remove_favicon')) {
            Uploads::deletePublicPath($settings->favicon_url);
            $data['favicon_url'] = '';
        }

        if ($request->hasFile('about_image')) {
            Uploads::deletePublicPath($settings->about_image_url);
            $data['about_image_url'] = Uploads::store($request->file('about_image'), 'images');
        } elseif ($request->boolean('remove_about_image')) {
            Uploads::deletePublicPath($settings->about_image_url);
            $data['about_image_url'] = '';
        }

        $data['updated_at'] = now();
        $settings->update($data);

        return redirect()->route('admin.settings.edit', $request->only('section'))
            ->with('status', 'تنظیمات ذخیره شد.');
    }
}
