<?php
require_once __DIR__ . '/../../lib/bootstrap.php';
require_admin();

$clientCount = (int) db_value('SELECT COUNT(*) FROM clients');
$categoryCount = (int) db_value('SELECT COUNT(*) FROM categories');
$itemCount = (int) db_value('SELECT COUNT(*) FROM portfolio_items');
$unread = (int) db_value('SELECT COUNT(*) FROM messages WHERE is_read = 0');

ob_start();
?>

<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-10">
    <div class="admin-card p-6">
        <span class="admin-label">کارفرمایان</span>
        <span class="text-3xl font-display font-extrabold"><?= $clientCount ?></span>
    </div>
    <div class="admin-card p-6">
        <span class="admin-label">دسته‌بندی‌ها</span>
        <span class="text-3xl font-display font-extrabold"><?= $categoryCount ?></span>
    </div>
    <div class="admin-card p-6">
        <span class="admin-label">نمونه‌کارها</span>
        <span class="text-3xl font-display font-extrabold"><?= $itemCount ?></span>
    </div>
    <a href="<?= e(url('/dashbord/app/messages.php')) ?>" class="admin-card p-6 block">
        <span class="admin-label">پیام‌های خوانده‌نشده</span>
        <span class="text-3xl font-display font-extrabold" style="color: <?= $unread > 0 ? 'var(--a-danger)' : 'inherit' ?>"><?= $unread ?></span>
    </a>
</div>

<div class="flex flex-wrap gap-3">
    <a href="<?= e(url('/dashbord/app/client-form.php')) ?>" class="admin-btn admin-btn-primary">+ افزودن کارفرمای جدید</a>
    <a href="<?= e(url('/dashbord/app/clients.php')) ?>" class="admin-btn">مدیریت کارفرمایان</a>
    <a href="<?= e(url('/dashbord/app/settings.php')) ?>" class="admin-btn">تنظیمات سایت</a>
</div>

<?php
$content = ob_get_clean();
$pageTitle = 'داشبورد';
$activeNav = 'dashboard';
require_once __DIR__ . '/../../partials/admin-layout.php';
