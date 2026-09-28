@props(['title' => 'پنل مدیریت'])
<!doctype html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title }} | پنل مدیریت نگاه مدیا</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body class="admin-scope">
    @php($currentRoute = request()->route()?->getName())
    <div class="flex min-h-dvh">
        <aside class="hidden md:flex w-64 shrink-0 flex-col gap-1 p-5 border-l" style="border-color: var(--a-border)">
            <div class="flex items-center gap-2 px-2 py-4 mb-2">
                <span class="h-9 w-9 rounded-xl flex items-center justify-center font-display font-extrabold" style="background: var(--a-primary); color:#fff">ن</span>
                <span class="font-display font-bold">نگاه مدیا</span>
            </div>

            <a href="{{ route('admin.dashboard') }}" class="admin-nav-link {{ $currentRoute === 'admin.dashboard' ? 'active' : '' }}">داشبورد</a>
            <a href="{{ route('admin.clients.index') }}" class="admin-nav-link {{ str_starts_with($currentRoute ?? '', 'admin.clients') ? 'active' : '' }}">کارفرمایان</a>
            <a href="{{ route('admin.settings.edit') }}" class="admin-nav-link {{ str_starts_with($currentRoute ?? '', 'admin.settings') || str_starts_with($currentRoute ?? '', 'admin.fonts') ? 'active' : '' }}">تنظیمات سایت</a>
            <a href="{{ route('admin.messages.index') }}" class="admin-nav-link {{ str_starts_with($currentRoute ?? '', 'admin.messages') ? 'active' : '' }}">پیام‌ها</a>

            <div class="mt-auto pt-4">
                <a href="{{ route('home') }}" target="_blank" class="admin-nav-link">مشاهده سایت ↗</a>
                <form method="POST" action="{{ route('admin.logout') }}">
                    @csrf
                    <button type="submit" class="admin-nav-link w-full text-right">خروج از حساب</button>
                </form>
            </div>
        </aside>

        <div class="flex-1 min-w-0">
            <header class="md:hidden flex items-center justify-between p-4 border-b" style="border-color: var(--a-border)">
                <span class="font-display font-bold">{{ $title }}</span>
                <nav class="flex gap-3 text-xs">
                    <a href="{{ route('admin.dashboard') }}">داشبورد</a>
                    <a href="{{ route('admin.clients.index') }}">کارفرمایان</a>
                    <a href="{{ route('admin.settings.edit') }}">تنظیمات</a>
                    <a href="{{ route('admin.messages.index') }}">پیام‌ها</a>
                </nav>
            </header>

            <main class="p-5 md:p-10 max-w-6xl">
                <h1 class="font-display font-extrabold text-2xl mb-8">{{ $title }}</h1>

                @if(session('status'))
                    <div class="admin-card p-4 mb-6 text-sm" style="border-color: var(--a-success); color: var(--a-success)">{{ session('status') }}</div>
                @endif
                @if($errors->any())
                    <div class="admin-card p-4 mb-6 text-sm" style="border-color: var(--a-danger); color: var(--a-danger)">
                        <ul class="list-disc pr-5">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{ $slot }}
            </main>
        </div>
    </div>
</body>
</html>
