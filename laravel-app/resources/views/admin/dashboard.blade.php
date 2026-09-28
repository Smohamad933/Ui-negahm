<x-layouts.admin title="داشبورد">
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-10">
        <div class="admin-card p-6">
            <span class="admin-label">کارفرمایان</span>
            <span class="text-3xl font-display font-extrabold">{{ $clientCount }}</span>
        </div>
        <div class="admin-card p-6">
            <span class="admin-label">دسته‌بندی‌ها</span>
            <span class="text-3xl font-display font-extrabold">{{ $categoryCount }}</span>
        </div>
        <div class="admin-card p-6">
            <span class="admin-label">نمونه‌کارها</span>
            <span class="text-3xl font-display font-extrabold">{{ $itemCount }}</span>
        </div>
        <a href="{{ route('admin.messages.index') }}" class="admin-card p-6 block">
            <span class="admin-label">پیام‌های خوانده‌نشده</span>
            <span class="text-3xl font-display font-extrabold" style="color: {{ $unread > 0 ? 'var(--a-danger)' : 'inherit' }}">{{ $unread }}</span>
        </a>
    </div>

    <div class="flex flex-wrap gap-3">
        <a href="{{ route('admin.clients.create') }}" class="admin-btn admin-btn-primary">+ افزودن کارفرمای جدید</a>
        <a href="{{ route('admin.clients.index') }}" class="admin-btn">مدیریت کارفرمایان</a>
        <a href="{{ route('admin.settings.edit') }}" class="admin-btn">تنظیمات سایت</a>
    </div>
</x-layouts.admin>
