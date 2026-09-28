<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Font;
use App\Support\Uploads;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class FontController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'family_name' => ['required', 'string', 'max:190'],
            'weight' => ['required', 'string', 'max:20'],
            'style' => ['required', 'in:normal,italic'],
            'file' => ['required', 'file', 'max:12288'],
        ], [
            'family_name.required' => 'ابتدا نام فونت را وارد کنید.',
        ]);

        $extension = strtolower($request->file('file')->getClientOriginalExtension());
        if (! in_array($extension, Uploads::ALLOWED_FONT_EXTENSIONS, true)) {
            throw ValidationException::withMessages([
                'file' => 'فرمت فونت پشتیبانی نمی‌شود (woff2, woff, ttf, otf).',
            ]);
        }
        $url = Uploads::store($request->file('file'), 'fonts');

        Font::query()->create([
            'family_name' => $data['family_name'],
            'weight' => $data['weight'],
            'style' => $data['style'],
            'format' => $extension,
            'file_url' => $url,
        ]);

        return redirect()->route('admin.settings.edit', ['section' => 'theme'])
            ->with('status', 'فونت با موفقیت آپلود شد.');
    }

    public function destroy(Font $font): RedirectResponse
    {
        Uploads::deletePublicPath($font->file_url);
        $font->delete();

        return redirect()->route('admin.settings.edit', ['section' => 'theme'])
            ->with('status', 'فونت حذف شد.');
    }
}
