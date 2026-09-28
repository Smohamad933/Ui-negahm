<x-layouts.admin title="افزودن کارفرمای جدید">
    <form method="POST" action="{{ route('admin.clients.store') }}" enctype="multipart/form-data" class="admin-card p-6 flex flex-col gap-5 max-w-2xl">
        @csrf
        <div>
            <label class="admin-label">نام کارفرما</label>
            <input type="text" name="name" value="{{ old('name') }}" class="admin-input" autofocus>
        </div>
        <div class="grid sm:grid-cols-2 gap-5">
            <div>
                <label class="admin-label">حوزه فعالیت</label>
                <input type="text" name="industry" value="{{ old('industry') }}" class="admin-input">
            </div>
            <div>
                <label class="admin-label">سال همکاری</label>
                <input type="text" name="year" value="{{ old('year') }}" class="admin-input">
            </div>
        </div>
        <div>
            <label class="admin-label">توضیح کوتاه</label>
            <textarea name="short_description" rows="3" class="admin-input">{{ old('short_description') }}</textarea>
        </div>
        <div>
            <label class="admin-label">آدرس وب‌سایت</label>
            <input type="text" name="website_url" value="{{ old('website_url') }}" class="admin-input" dir="ltr">
        </div>
        <div class="grid sm:grid-cols-2 gap-5">
            <div>
                <label class="admin-label">لوگو</label>
                <input type="file" name="logo" accept="image/*" class="admin-input">
            </div>
            <div>
                <label class="admin-label">تصویر کاور</label>
                <input type="file" name="cover" accept="image/*" class="admin-input">
            </div>
        </div>
        <button type="submit" class="admin-btn admin-btn-primary self-start">ایجاد و ادامه ویرایش</button>
    </form>
</x-layouts.admin>
