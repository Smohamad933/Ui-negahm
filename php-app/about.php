<?php
require_once __DIR__ . '/lib/bootstrap.php';

$settings = settings();
$clientCount = (int) db_value('SELECT COUNT(*) FROM clients WHERE published = 1');
$projectCount = (int) db_value('SELECT COUNT(*) FROM categories');
$itemCount = (int) db_value('SELECT COUNT(*) FROM portfolio_items');

$process = [
    ['num' => '۰۱', 'title' => 'شناخت', 'desc' => 'ارزیابی دقیق هدف، پرسونا، مسئله و فرصت برند.'],
    ['num' => '۰۲', 'title' => 'استراتژی', 'desc' => 'تدوین خط ارتباطی و ایده مرکزی پروژه.'],
    ['num' => '۰۳', 'title' => 'خلق', 'desc' => 'تجسم‌بخشی به ایده در قالب طراحی، محتوا و تجربه.'],
    ['num' => '۰۴', 'title' => 'اجرا', 'desc' => 'پیاده‌سازی دقیق، انتشار و بهینه‌سازی مداوم خروجی‌ها.'],
];

ob_start();
?>

<section class="container-px pt-6 pb-16">
    <div class="grid md:grid-cols-2 gap-12 items-center">
        <div>
            <span class="eyebrow">درباره ما</span>
            <h1 class="h-hero font-display mt-6"><?= e($settings['about_title']) ?></h1>
            <p class="mt-8 text-base md:text-lg leading-8" style="color: var(--color-muted)"><?= nl2br(e($settings['about_body'])) ?></p>
        </div>
        <?php if ($settings['about_image_url']): ?>
            <div class="rounded-[32px] overflow-hidden frame-pop aspect-1-1 reveal-up">
                <img src="<?= e($settings['about_image_url']) ?>" alt="<?= e($settings['site_name']) ?>" class="w-full h-full object-cover">
            </div>
        <?php endif; ?>
    </div>
</section>

<section class="container-px py-16 reveal-up">
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
        <div class="client-card p-8 text-center">
            <span class="h-hero font-display text-pop block" style="font-size:clamp(2.5rem,5vw,4rem)"><?= $clientCount ?></span>
            <span class="tag-pill mt-4 inline-flex">همراه فعال</span>
        </div>
        <div class="client-card p-8 text-center">
            <span class="h-hero font-display text-pop block" style="font-size:clamp(2.5rem,5vw,4rem)"><?= $projectCount ?></span>
            <span class="tag-pill mt-4 inline-flex">پروژه اجرا شده</span>
        </div>
        <div class="client-card p-8 text-center">
            <span class="h-hero font-display text-pop block" style="font-size:clamp(2.5rem,5vw,4rem)"><?= $itemCount ?></span>
            <span class="tag-pill mt-4 inline-flex">قطعه محتوا</span>
        </div>
    </div>
</section>

<section class="container-px py-16 reveal-up">
    <span class="eyebrow">مسیر همکاری</span>
    <h2 class="h-section font-display mt-4 mb-10 max-w-2xl">از شناخت برند تا اجرای نهایی، در ۴ گام</h2>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <?php foreach ($process as $i => $step): ?>
            <div class="client-card p-6 flex flex-col gap-3 reveal-up" style="animation-delay: <?= $i * 0.06 ?>s">
                <span class="font-display font-extrabold text-2xl" style="color: var(--color-secondary)"><?= e($step['num']) ?></span>
                <h3 class="font-display font-bold text-lg"><?= e($step['title']) ?></h3>
                <p class="text-sm" style="color: var(--color-muted)"><?= e($step['desc']) ?></p>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<section class="container-px py-24 reveal-up">
    <div class="rounded-[36px] p-10 md:p-16 text-center frame-pop" style="background: var(--color-secondary); color:#fff;">
        <h2 class="h-section font-display" style="color:#fff">بیایید با هم یک روایت تازه بسازیم.</h2>
        <a href="<?= e(url('/contact.php')) ?>" class="btn-pill btn-invert mt-8 inline-flex" style="background:#fff;color:var(--color-fg)">تماس با ما ↗</a>
    </div>
</section>

<?php
$content = ob_get_clean();
$pageTitle = 'درباره ما | ' . $settings['site_name'];
require_once __DIR__ . '/partials/site-layout.php';
