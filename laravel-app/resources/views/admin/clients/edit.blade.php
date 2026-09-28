@php
    $tab = request('tab', 'info');
@endphp
<x-layouts.admin title="ویرایش: {{ $client->name }}">

    <div class="flex flex-wrap items-center justify-between gap-3 mb-8">
        <div class="flex gap-2">
            <a href="{{ route('admin.clients.edit', ['client' => $client, 'tab' => 'info']) }}" class="admin-btn {{ $tab === 'info' ? 'admin-btn-active' : '' }}">اطلاعات کلی</a>
            <a href="{{ route('admin.clients.edit', ['client' => $client, 'tab' => 'portfolio']) }}" class="admin-btn {{ $tab === 'portfolio' ? 'admin-btn-active' : '' }}">نمونه‌کارها</a>
        </div>
        <a href="{{ route('clients.show', $client) }}" target="_blank" class="admin-btn">مشاهده در سایت ↗</a>
    </div>

    @if($tab === 'info')
        <form method="POST" action="{{ route('admin.clients.update', $client) }}" enctype="multipart/form-data" class="admin-card p-6 flex flex-col gap-5 max-w-2xl">
            @csrf
            <div class="grid sm:grid-cols-2 gap-5">
                <div>
                    <label class="admin-label">نام کارفرما</label>
                    <input type="text" name="name" value="{{ old('name', $client->name) }}" class="admin-input">
                </div>
                <div>
                    <label class="admin-label">نامک (slug)</label>
                    <input type="text" name="slug" value="{{ old('slug', $client->slug) }}" class="admin-input" dir="ltr">
                </div>
            </div>

            <div class="grid sm:grid-cols-2 gap-5">
                <div>
                    <label class="admin-label">حوزه فعالیت</label>
                    <input type="text" name="industry" value="{{ old('industry', $client->industry) }}" class="admin-input">
                </div>
                <div>
                    <label class="admin-label">سال همکاری</label>
                    <input type="text" name="year" value="{{ old('year', $client->year) }}" class="admin-input">
                </div>
            </div>

            <div>
                <label class="admin-label">توضیح کوتاه</label>
                <textarea name="short_description" rows="3" class="admin-input">{{ old('short_description', $client->short_description) }}</textarea>
            </div>

            <div class="grid sm:grid-cols-2 gap-5">
                <div>
                    <label class="admin-label">آدرس وب‌سایت</label>
                    <input type="text" name="website_url" value="{{ old('website_url', $client->website_url) }}" class="admin-input" dir="ltr">
                </div>
                <div>
                    <label class="admin-label">رنگ اختصاصی (اختیاری)</label>
                    <input type="color" name="accent_color" value="{{ old('accent_color', $client->accent_color ?: '#ff3d74') }}" class="h-10 w-16 rounded border-0 bg-transparent">
                </div>
            </div>

            <div class="grid sm:grid-cols-2 gap-5">
                <div>
                    <label class="admin-label">لوگو</label>
                    @if($client->logo_url)
                        <div class="mb-2 flex items-center gap-3">
                            <img src="{{ $client->logo_url }}" class="h-10" alt="">
                            <label class="text-xs flex items-center gap-1" style="color: var(--a-danger)"><input type="checkbox" name="remove_logo" value="1"> حذف</label>
                        </div>
                    @endif
                    <input type="file" name="logo" accept="image/*" class="admin-input">
                </div>
                <div>
                    <label class="admin-label">تصویر کاور</label>
                    @if($client->cover_image_url)
                        <div class="mb-2 flex items-center gap-3">
                            <img src="{{ $client->cover_image_url }}" class="h-10" alt="">
                            <label class="text-xs flex items-center gap-1" style="color: var(--a-danger)"><input type="checkbox" name="remove_cover" value="1"> حذف</label>
                        </div>
                    @endif
                    <input type="file" name="cover" accept="image/*" class="admin-input">
                </div>
            </div>

            <div class="flex gap-6">
                <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="published" value="1" {{ $client->published ? 'checked' : '' }}> منتشر شده</label>
                <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="featured" value="1" {{ $client->featured ? 'checked' : '' }}> کارفرمای ویژه</label>
            </div>

            <div class="flex items-center justify-between mt-2">
                <button type="submit" class="admin-btn admin-btn-primary">ذخیره تغییرات</button>
                <form method="POST" action="{{ route('admin.clients.destroy', $client) }}" onsubmit="return confirm('این کارفرما و تمام نمونه‌کارهای آن حذف شود؟');">
                    @csrf
                    <button type="submit" class="admin-btn admin-btn-danger">حذف این کارفرما</button>
                </form>
            </div>
        </form>
    @endif

    @if($tab === 'portfolio')
        <div class="flex flex-col gap-8">
            @foreach($client->categories as $category)
                @php $ratio = $category->normalizedAspectRatio(); @endphp
                <div class="admin-card p-6">
                    <div class="flex flex-wrap items-start justify-between gap-4 mb-5">
                        <form method="POST" action="{{ route('admin.categories.update', $category) }}" enctype="multipart/form-data" class="grid sm:grid-cols-2 gap-4 flex-1 min-w-[260px]">
                            @csrf
                            <div>
                                <label class="admin-label">عنوان دسته‌بندی</label>
                                <input type="text" name="title" value="{{ $category->title }}" class="admin-input">
                            </div>
                            <div>
                                <label class="admin-label">نسبت تصویر</label>
                                <select name="aspect_ratio" class="admin-input">
                                    @foreach(\App\Support\Aspect::OPTIONS as $value => $label)
                                        <option value="{{ $value }}" {{ $ratio === $value ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="sm:col-span-2">
                                <label class="admin-label">توضیح</label>
                                <input type="text" name="description" value="{{ $category->description }}" class="admin-input">
                            </div>
                            <div>
                                <label class="admin-label">تصویر کاور دسته‌بندی</label>
                                @if($category->cover_image_url)
                                    <div class="mb-2 flex items-center gap-2">
                                        <img src="{{ $category->cover_image_url }}" class="h-10 {{ \App\Support\Aspect::TILE_CLASS[$ratio] }} object-cover rounded">
                                        <label class="text-xs flex items-center gap-1" style="color: var(--a-danger)"><input type="checkbox" name="remove_cover" value="1"> حذف</label>
                                    </div>
                                @endif
                                <input type="file" name="cover" accept="image/*" class="admin-input">
                            </div>
                            <div class="flex items-end">
                                <button type="submit" class="admin-btn admin-btn-primary w-full">ذخیره دسته‌بندی</button>
                            </div>
                        </form>

                        <form method="POST" action="{{ route('admin.categories.destroy', $category) }}" onsubmit="return confirm('این دسته‌بندی و تمام نمونه‌کارهای آن حذف شود؟');">
                            @csrf
                            <button type="submit" class="admin-btn admin-btn-danger">حذف دسته‌بندی</button>
                        </form>
                    </div>

                    <div class="hairline mb-5" style="border-top: 1px dashed var(--a-border)"></div>

                    <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-6 gap-3 mb-5">
                        @foreach($category->items as $item)
                            <div class="relative rounded-lg overflow-hidden border" style="border-color: var(--a-border)">
                                @if($item->media_type === 'video')
                                    <video src="{{ $item->media_url }}" class="w-full {{ \App\Support\Aspect::TILE_CLASS[$ratio] }} object-cover" muted></video>
                                @else
                                    <img src="{{ $item->media_url }}" class="w-full {{ \App\Support\Aspect::TILE_CLASS[$ratio] }} object-cover" alt="">
                                @endif
                                <div class="absolute inset-x-0 bottom-0 flex items-center justify-between gap-1 p-1.5" style="background: rgba(0,0,0,.6)">
                                    <form method="POST" action="{{ route('admin.portfolio-items.toggle-featured', $item) }}">
                                        @csrf
                                        <button type="submit" class="text-[10px] px-1.5 py-0.5 rounded" style="background: {{ $item->featured_home ? 'var(--a-primary)' : 'transparent' }}; color:#fff; border:1px solid #fff">ویژه</button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.portfolio-items.destroy', $item) }}" onsubmit="return confirm('این نمونه‌کار حذف شود؟');">
                                        @csrf
                                        <button type="submit" class="text-[10px] px-1.5 py-0.5 rounded" style="color:#fff; border:1px solid var(--a-danger); background: var(--a-danger)">حذف</button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <form method="POST" action="{{ route('admin.portfolio-items.store') }}" enctype="multipart/form-data" class="grid sm:grid-cols-5 gap-3 items-end">
                        @csrf
                        <input type="hidden" name="category_id" value="{{ $category->id }}">
                        <div class="sm:col-span-1">
                            <label class="admin-label">نوع</label>
                            <select name="media_type" class="admin-input">
                                <option value="image">تصویر</option>
                                <option value="video">ویدئو</option>
                            </select>
                        </div>
                        <div class="sm:col-span-2">
                            <label class="admin-label">فایل ({{ \App\Support\Aspect::OPTIONS[$ratio] }})</label>
                            <input type="file" name="media" accept="image/*,video/mp4,video/webm" class="admin-input">
                        </div>
                        <div class="sm:col-span-1">
                            <label class="admin-label">عنوان (اختیاری)</label>
                            <input type="text" name="title" class="admin-input">
                        </div>
                        <div class="sm:col-span-1">
                            <button type="submit" class="admin-btn admin-btn-primary w-full">افزودن</button>
                        </div>
                    </form>
                </div>
            @endforeach

            <div class="admin-card p-6">
                <h2 class="font-display font-bold mb-4">افزودن دسته‌بندی جدید</h2>
                <form method="POST" action="{{ route('admin.categories.store') }}" class="grid sm:grid-cols-4 gap-4 items-end">
                    @csrf
                    <input type="hidden" name="client_id" value="{{ $client->id }}">
                    <div class="sm:col-span-2">
                        <label class="admin-label">عنوان دسته‌بندی</label>
                        <input type="text" name="title" class="admin-input" placeholder="مثلاً: کمپین تبلیغاتی">
                    </div>
                    <div>
                        <label class="admin-label">نسبت تصویر</label>
                        <select name="aspect_ratio" class="admin-input">
                            @foreach(\App\Support\Aspect::OPTIONS as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="admin-btn admin-btn-primary">افزودن دسته‌بندی</button>
                </form>
            </div>
        </div>
    @endif

</x-layouts.admin>
