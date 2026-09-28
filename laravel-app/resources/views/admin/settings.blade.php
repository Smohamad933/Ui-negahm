@php
    $section = request('section', session('open_section', 'general'));
    $tabs = [
        'general' => 'عمومی',
        'theme' => 'رنگ و فونت',
        'content' => 'محتوای صفحات',
        'security' => 'رمز عبور',
    ];
@endphp
<x-layouts.admin title="تنظیمات سایت">

    <div class="flex flex-wrap gap-2 mb-8">
        @foreach($tabs as $key => $label)
            <a href="{{ route('admin.settings.edit', ['section' => $key]) }}" class="admin-btn {{ $section === $key ? 'admin-btn-active' : '' }}">{{ $label }}</a>
        @endforeach
    </div>

    @if($section === 'general')
        <form method="POST" action="{{ route('admin.settings.update') }}" enctype="multipart/form-data" class="admin-card p-6 flex flex-col gap-5 max-w-2xl">
            @csrf
            <input type="hidden" name="section" value="general">
            @foreach(['color_bg','color_fg','color_primary','color_secondary','color_accent','color_muted','font_family','hero_title','about_title'] as $keep)
                <input type="hidden" name="{{ $keep }}" value="{{ old($keep, $settings->$keep) }}">
            @endforeach

            <div>
                <label class="admin-label">نام سایت</label>
                <input type="text" name="site_name" value="{{ old('site_name', $settings->site_name) }}" class="admin-input">
            </div>
            <div>
                <label class="admin-label">شعار / تگ‌لاین</label>
                <input type="text" name="tagline" value="{{ old('tagline', $settings->tagline) }}" class="admin-input">
            </div>

            <div class="grid sm:grid-cols-2 gap-5">
                <div>
                    <label class="admin-label">لوگو</label>
                    @if($settings->logo_url)
                        <div class="mb-2 flex items-center gap-3">
                            <img src="{{ $settings->logo_url }}" class="h-10" alt="لوگو">
                            <label class="text-xs flex items-center gap-1" style="color: var(--a-danger)"><input type="checkbox" name="remove_logo" value="1"> حذف</label>
                        </div>
                    @endif
                    <input type="file" name="logo" accept="image/*" class="admin-input">
                </div>
                <div>
                    <label class="admin-label">فاوآیکون</label>
                    @if($settings->favicon_url)
                        <div class="mb-2 flex items-center gap-3">
                            <img src="{{ $settings->favicon_url }}" class="h-8" alt="فاوآیکون">
                            <label class="text-xs flex items-center gap-1" style="color: var(--a-danger)"><input type="checkbox" name="remove_favicon" value="1"> حذف</label>
                        </div>
                    @endif
                    <input type="file" name="favicon" accept="image/*" class="admin-input">
                </div>
            </div>

            <button type="submit" class="admin-btn admin-btn-primary self-start mt-2">ذخیره تغییرات</button>
        </form>
    @endif

    @if($section === 'theme')
        <form method="POST" action="{{ route('admin.settings.update') }}" class="admin-card p-6 flex flex-col gap-5 max-w-2xl mb-10">
            @csrf
            <input type="hidden" name="section" value="theme">
            @foreach(['site_name','tagline','hero_title','about_title'] as $keep)
                <input type="hidden" name="{{ $keep }}" value="{{ old($keep, $settings->$keep) }}">
            @endforeach

            <div class="grid grid-cols-2 sm:grid-cols-3 gap-5">
                @foreach(['color_bg' => 'پس‌زمینه', 'color_fg' => 'متن اصلی', 'color_primary' => 'اصلی', 'color_secondary' => 'ثانویه', 'color_accent' => 'تاکیدی', 'color_muted' => 'خنثی'] as $key => $label)
                    <div>
                        <label class="admin-label">{{ $label }}</label>
                        <div class="flex items-center gap-2">
                            <input type="color" name="{{ $key }}" value="{{ old($key, $settings->$key) }}" class="h-10 w-12 rounded border-0 bg-transparent" oninput="this.nextElementSibling.value=this.value">
                            <input type="text" value="{{ old($key, $settings->$key) }}" class="admin-input" dir="ltr" oninput="this.previousElementSibling.value=this.value" onchange="this.previousElementSibling.value=this.value">
                        </div>
                    </div>
                @endforeach
            </div>

            <div>
                <label class="admin-label">فونت اصلی سایت</label>
                <select name="font_family" class="admin-input">
                    <option value="Vazirmatn Variable" {{ $settings->font_family === 'Vazirmatn Variable' ? 'selected' : '' }}>Vazirmatn (پیش‌فرض)</option>
                    @foreach($customFamilies as $family)
                        <option value="{{ $family }}" {{ $settings->font_family === $family ? 'selected' : '' }}>{{ $family }}</option>
                    @endforeach
                </select>
            </div>

            <button type="submit" class="admin-btn admin-btn-primary self-start mt-2">ذخیره تغییرات</button>
        </form>

        <div class="admin-card p-6 max-w-2xl">
            <h2 class="font-display font-bold mb-4">فونت‌های سفارشی</h2>

            @if($fonts->isNotEmpty())
                <div class="flex flex-col gap-2 mb-6">
                    @foreach($fonts as $font)
                        <div class="flex items-center justify-between gap-3 p-3 rounded-lg" style="background: var(--a-panel-2)">
                            <span class="text-sm">{{ $font->family_name }} <span style="color: var(--a-muted)">({{ $font->weight }}, {{ $font->style }}, {{ $font->format }})</span></span>
                            <form method="POST" action="{{ route('admin.fonts.destroy', $font) }}" onsubmit="return confirm('این فونت حذف شود؟');">
                                @csrf
                                <button type="submit" class="admin-btn admin-btn-danger">حذف</button>
                            </form>
                        </div>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('admin.fonts.store') }}" enctype="multipart/form-data" class="grid sm:grid-cols-2 gap-4">
                @csrf
                <div>
                    <label class="admin-label">نام خانواده فونت</label>
                    <input type="text" name="family_name" class="admin-input" placeholder="مثلاً: Peyda" required>
                </div>
                <div>
                    <label class="admin-label">وزن (weight)</label>
                    <input type="text" name="weight" value="400" class="admin-input" required>
                </div>
                <div>
                    <label class="admin-label">سبک</label>
                    <select name="style" class="admin-input">
                        <option value="normal">Normal</option>
                        <option value="italic">Italic</option>
                    </select>
                </div>
                <div>
                    <label class="admin-label">فایل فونت (woff2, woff, ttf, otf)</label>
                    <input type="file" name="file" accept=".woff2,.woff,.ttf,.otf" class="admin-input" required>
                </div>
                <button type="submit" class="admin-btn admin-btn-primary sm:col-span-2">آپلود فونت</button>
            </form>
        </div>
    @endif

    @if($section === 'content')
        <form method="POST" action="{{ route('admin.settings.update') }}" enctype="multipart/form-data" class="admin-card p-6 flex flex-col gap-8 max-w-2xl">
            @csrf
            <input type="hidden" name="section" value="content">
            @foreach(['site_name','tagline','color_bg','color_fg','color_primary','color_secondary','color_accent','color_muted','font_family'] as $keep)
                <input type="hidden" name="{{ $keep }}" value="{{ old($keep, $settings->$keep) }}">
            @endforeach

            <div class="flex flex-col gap-4">
                <h2 class="font-display font-bold">صفحه اصلی (هیرو)</h2>
                <div>
                    <label class="admin-label">عنوان اصلی</label>
                    <textarea name="hero_title" rows="2" class="admin-input">{{ old('hero_title', $settings->hero_title) }}</textarea>
                </div>
                <div>
                    <label class="admin-label">زیرعنوان</label>
                    <textarea name="hero_subtitle" rows="3" class="admin-input">{{ old('hero_subtitle', $settings->hero_subtitle) }}</textarea>
                </div>
                <div class="grid sm:grid-cols-2 gap-4">
                    <div>
                        <label class="admin-label">متن دکمه</label>
                        <input type="text" name="hero_cta_text" value="{{ old('hero_cta_text', $settings->hero_cta_text) }}" class="admin-input">
                    </div>
                    <div>
                        <label class="admin-label">لینک دکمه</label>
                        <input type="text" name="hero_cta_link" value="{{ old('hero_cta_link', $settings->hero_cta_link) }}" class="admin-input" dir="ltr">
                    </div>
                </div>
            </div>

            <div class="flex flex-col gap-4">
                <h2 class="font-display font-bold">درباره ما</h2>
                <div>
                    <label class="admin-label">عنوان</label>
                    <textarea name="about_title" rows="2" class="admin-input">{{ old('about_title', $settings->about_title) }}</textarea>
                </div>
                <div>
                    <label class="admin-label">متن</label>
                    <textarea name="about_body" rows="5" class="admin-input">{{ old('about_body', $settings->about_body) }}</textarea>
                </div>
                <div>
                    <label class="admin-label">تصویر درباره ما</label>
                    @if($settings->about_image_url)
                        <div class="mb-2 flex items-center gap-3">
                            <img src="{{ $settings->about_image_url }}" class="h-16 rounded" alt="">
                            <label class="text-xs flex items-center gap-1" style="color: var(--a-danger)"><input type="checkbox" name="remove_about_image" value="1"> حذف</label>
                        </div>
                    @endif
                    <input type="file" name="about_image" accept="image/*" class="admin-input">
                </div>
            </div>

            <div class="flex flex-col gap-4">
                <h2 class="font-display font-bold">اطلاعات تماس</h2>
                <div class="grid sm:grid-cols-2 gap-4">
                    <div>
                        <label class="admin-label">آدرس</label>
                        <input type="text" name="contact_address" value="{{ old('contact_address', $settings->contact_address) }}" class="admin-input">
                    </div>
                    <div>
                        <label class="admin-label">تلفن</label>
                        <input type="text" name="contact_phone" value="{{ old('contact_phone', $settings->contact_phone) }}" class="admin-input" dir="ltr">
                    </div>
                    <div>
                        <label class="admin-label">ایمیل</label>
                        <input type="text" name="contact_email" value="{{ old('contact_email', $settings->contact_email) }}" class="admin-input" dir="ltr">
                    </div>
                </div>
                <div>
                    <label class="admin-label">کد Embed نقشه (اختیاری)</label>
                    <textarea name="contact_map_embed" rows="3" class="admin-input" dir="ltr">{{ old('contact_map_embed', $settings->contact_map_embed) }}</textarea>
                </div>
            </div>

            <div class="flex flex-col gap-4">
                <h2 class="font-display font-bold">شبکه‌های اجتماعی و فوتر</h2>
                <div class="grid sm:grid-cols-2 gap-4">
                    <div><label class="admin-label">اینستاگرام</label><input type="text" name="social_instagram" value="{{ old('social_instagram', $settings->social_instagram) }}" class="admin-input" dir="ltr"></div>
                    <div><label class="admin-label">تلگرام</label><input type="text" name="social_telegram" value="{{ old('social_telegram', $settings->social_telegram) }}" class="admin-input" dir="ltr"></div>
                    <div><label class="admin-label">واتس‌اپ</label><input type="text" name="social_whatsapp" value="{{ old('social_whatsapp', $settings->social_whatsapp) }}" class="admin-input" dir="ltr"></div>
                    <div><label class="admin-label">لینکدین</label><input type="text" name="social_linkedin" value="{{ old('social_linkedin', $settings->social_linkedin) }}" class="admin-input" dir="ltr"></div>
                </div>
                <div>
                    <label class="admin-label">متن فوتر</label>
                    <input type="text" name="footer_text" value="{{ old('footer_text', $settings->footer_text) }}" class="admin-input">
                </div>
            </div>

            <button type="submit" class="admin-btn admin-btn-primary self-start">ذخیره تغییرات</button>
        </form>
    @endif

    @if($section === 'security')
        <form method="POST" action="{{ route('admin.password.update') }}" class="admin-card p-6 flex flex-col gap-5 max-w-md">
            @csrf
            <div>
                <label class="admin-label">رمز عبور فعلی</label>
                <input type="password" name="current_password" class="admin-input">
            </div>
            <div>
                <label class="admin-label">رمز عبور جدید</label>
                <input type="password" name="new_password" class="admin-input">
            </div>
            <div>
                <label class="admin-label">تکرار رمز عبور جدید</label>
                <input type="password" name="new_password_confirmation" class="admin-input">
            </div>
            <button type="submit" class="admin-btn admin-btn-primary self-start">تغییر رمز عبور</button>
        </form>
    @endif

</x-layouts.admin>
