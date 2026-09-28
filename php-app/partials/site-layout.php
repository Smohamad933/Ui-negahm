<?php
/**
 * لایوت مشترک سایت عمومی. انتظار می‌رود قبل از include این فایل، متغیرهای
 * $settings (آرایه‌ی تنظیمات) و $content (HTML بدنه‌ی صفحه) و به‌صورت
 * اختیاری $pageTitle تعریف شده باشند.
 */

$pageTitle = $pageTitle ?? ($settings['site_name'] . ' | ' . $settings['tagline']);
?>
<!doctype html>
<html lang="fa" dir="rtl" class="no-js">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($pageTitle) ?></title>
    <meta name="description" content="<?= e($settings['tagline']) ?>">
    <?php if (! empty($settings['favicon_url'])): ?>
        <link rel="icon" href="<?= e($settings['favicon_url']) ?>">
    <?php endif; ?>
    <link rel="stylesheet" href="<?= e(asset('/css/app.css')) ?>">
    <style><?= theme_css($settings) ?></style>
    <script>document.documentElement.classList.remove('no-js');</script>
</head>
<body>
    <div class="grain-overlay"></div>

    <?php require_once __DIR__ . '/site-header.php'; ?>

    <main class="pt-24">
        <?= $content ?>
    </main>

    <?php require_once __DIR__ . '/site-footer.php'; ?>

    <script src="<?= e(asset('/js/app.js')) ?>" defer></script>
</body>
</html>
