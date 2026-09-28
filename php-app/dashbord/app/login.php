<?php
require_once __DIR__ . '/../../lib/bootstrap.php';

if (current_admin() !== null) {
    redirect('/dashbord/app/index.php');
}

$error = '';
$username = '';

if (is_post()) {
    csrf_verify_or_die();

    $username = input('username');
    $password = input('password');

    if ($username === '' || $password === '') {
        $error = 'نام کاربری و رمز عبور را وارد کنید.';
    } elseif (! attempt_login($username, $password)) {
        $error = 'نام کاربری یا رمز عبور اشتباه است.';
    } else {
        redirect('/dashbord/app/index.php');
    }
}
?>
<!doctype html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ورود به پنل مدیریت | نگاه مدیا</title>
    <link rel="stylesheet" href="<?= e(asset('/css/app.css')) ?>">
    <link rel="stylesheet" href="<?= e(asset('/css/custom.css')) ?>">
</head>
<body class="admin-scope flex items-center justify-center min-h-dvh p-5">
    <div class="admin-card w-full max-w-sm p-8">
        <div class="flex items-center gap-2 mb-8 justify-center">
            <span class="h-10 w-10 rounded-xl flex items-center justify-center font-display font-extrabold" style="background: var(--a-primary); color:#fff">ن</span>
            <span class="font-display font-bold text-lg">پنل مدیریت نگاه مدیا</span>
        </div>

        <?php if ($error): ?>
            <div class="admin-card p-3 mb-5 text-sm text-center" style="border-color: var(--a-danger); color: var(--a-danger)"><?= e($error) ?></div>
        <?php endif; ?>

        <form method="POST" action="<?= e(url('/dashbord/app/login.php')) ?>" class="flex flex-col gap-4">
            <?= csrf_field() ?>
            <div>
                <label class="admin-label">نام کاربری</label>
                <input type="text" name="username" value="<?= e($username) ?>" class="admin-input" autofocus>
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
