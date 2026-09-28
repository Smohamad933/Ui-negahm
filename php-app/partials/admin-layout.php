<?php
/**
 * قالب مشترک پنل مدیریت. صفحه‌ی فراخواننده باید قبل از require کردن این
 * فایل مقادیر زیر را تعریف کرده باشد:
 *   $pageTitle   (string) عنوان صفحه
 *   $activeNav   (string) یکی از: dashboard|clients|settings|messages
 *   $content     (string) HTML محتوای اصلی (خروجی ob_get_clean())
 *
 * پیام‌های فلش «status» و «error» به‌صورت خودکار نمایش داده می‌شوند.
 */

$admin = current_admin();
$statusMsg = flash_get('status');
$errorMsg = flash_get('error');
?>
<!doctype html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($pageTitle) ?> | پنل مدیریت نگاه مدیا</title>
    <link rel="stylesheet" href="<?= e(asset('/css/app.css')) ?>">
</head>
<body class="admin-scope">
    <div class="flex min-h-dvh">
        <aside class="hidden md:flex w-64 shrink-0 flex-col gap-1 p-5 border-l" style="border-color: var(--a-border)">
            <div class="flex items-center gap-2 px-2 py-4 mb-2">
                <span class="h-9 w-9 rounded-xl flex items-center justify-center font-display font-extrabold" style="background: var(--a-primary); color:#fff">ن</span>
                <span class="font-display font-bold">نگاه مدیا</span>
            </div>

            <a href="<?= e(url('/dashbord/app/index.php')) ?>" class="admin-nav-link <?= $activeNav === 'dashboard' ? 'active' : '' ?>">داشبورد</a>
            <a href="<?= e(url('/dashbord/app/clients.php')) ?>" class="admin-nav-link <?= $activeNav === 'clients' ? 'active' : '' ?>">کارفرمایان</a>
            <a href="<?= e(url('/dashbord/app/settings.php')) ?>" class="admin-nav-link <?= $activeNav === 'settings' ? 'active' : '' ?>">تنظیمات سایت</a>
            <a href="<?= e(url('/dashbord/app/messages.php')) ?>" class="admin-nav-link <?= $activeNav === 'messages' ? 'active' : '' ?>">پیام‌ها</a>

            <div class="mt-auto pt-4">
                <a href="<?= e(url('/index.php')) ?>" target="_blank" class="admin-nav-link">مشاهده سایت ↗</a>
                <form method="POST" action="<?= e(url('/dashbord/app/logout.php')) ?>">
                    <?= csrf_field() ?>
                    <button type="submit" class="admin-nav-link w-full text-right">خروج از حساب (<?= e($admin['username'] ?? '') ?>)</button>
                </form>
            </div>
        </aside>

        <div class="flex-1 min-w-0">
            <header class="md:hidden flex items-center justify-between p-4 border-b" style="border-color: var(--a-border)">
                <span class="font-display font-bold"><?= e($pageTitle) ?></span>
                <nav class="flex gap-3 text-xs">
                    <a href="<?= e(url('/dashbord/app/index.php')) ?>">داشبورد</a>
                    <a href="<?= e(url('/dashbord/app/clients.php')) ?>">کارفرمایان</a>
                    <a href="<?= e(url('/dashbord/app/settings.php')) ?>">تنظیمات</a>
                    <a href="<?= e(url('/dashbord/app/messages.php')) ?>">پیام‌ها</a>
                </nav>
            </header>

            <main class="p-5 md:p-10 max-w-6xl">
                <h1 class="font-display font-extrabold text-2xl mb-8"><?= e($pageTitle) ?></h1>

                <?php if ($statusMsg): ?>
                    <div class="admin-card p-4 mb-6 text-sm" style="border-color: var(--a-success); color: var(--a-success)"><?= e($statusMsg) ?></div>
                <?php endif; ?>
                <?php if ($errorMsg): ?>
                    <div class="admin-card p-4 mb-6 text-sm" style="border-color: var(--a-danger); color: var(--a-danger)"><?= e($errorMsg) ?></div>
                <?php endif; ?>

                <?= $content ?>
            </main>
        </div>
    </div>
</body>
</html>
