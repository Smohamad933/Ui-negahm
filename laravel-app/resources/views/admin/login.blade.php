<!doctype html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ورود به پنل مدیریت | نگاه مدیا</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body class="admin-scope flex items-center justify-center min-h-dvh p-5">
    <div class="admin-card w-full max-w-sm p-8">
        <div class="flex items-center gap-2 mb-8 justify-center">
            <span class="h-10 w-10 rounded-xl flex items-center justify-center font-display font-extrabold" style="background: var(--a-primary); color:#fff">ن</span>
            <span class="font-display font-bold text-lg">پنل مدیریت نگاه مدیا</span>
        </div>

        @if($errors->any())
            <div class="admin-card p-3 mb-5 text-sm text-center" style="border-color: var(--a-danger); color: var(--a-danger)">
                @foreach($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('admin.login.submit') }}" class="flex flex-col gap-4">
            @csrf
            <div>
                <label class="admin-label">نام کاربری</label>
                <input type="text" name="username" value="{{ old('username') }}" class="admin-input" autofocus>
            </div>
            <div>
                <label class="admin-label">رمز عبور</label>
                <input type="password" name="password" class="admin-input">
            </div>
            <button type="submit" class="admin-btn admin-btn-primary w-full mt-2">ورود</button>
        </form>
    </div>
</body>
</html>
