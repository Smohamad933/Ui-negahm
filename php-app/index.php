<?php
require_once __DIR__ . '/lib/bootstrap.php';

$settings = settings();

$clients = db_all(
    'SELECT * FROM clients WHERE published = 1 ORDER BY order_index ASC, id DESC'
);

$featuredClients = db_all(
    'SELECT * FROM clients WHERE published = 1 AND featured = 1 ORDER BY order_index ASC'
);

$featured = db_all(
    'SELECT portfolio_items.*,
            clients.name AS client_name,
            clients.slug AS client_slug,
            categories.slug AS category_slug,
            categories.title AS category_title,
            categories.aspect_ratio AS category_aspect_ratio
     FROM portfolio_items
     JOIN categories ON categories.id = portfolio_items.category_id
     JOIN clients ON clients.id = categories.client_id
     WHERE portfolio_items.featured_home = 1 AND clients.published = 1
     ORDER BY portfolio_items.order_index ASC
     LIMIT 7'
);

$services = [
    ['num' => '۰۱', 'title' => 'هویت بصری و برندینگ', 'desc' => 'طراحی لوگو، سیستم گرافیکی و زبان بصری منسجم.'],
    ['num' => '۰۲', 'title' => 'تولید محتوای خلاق', 'desc' => 'ایده‌پردازی، عکاسی، تصویربرداری، طراحی و شبکه‌های اجتماعی.'],
    ['num' => '۰۳', 'title' => 'دیجیتال مارکتینگ', 'desc' => 'استراتژی محتوا، مدیریت سوشال مدیا و تبلیغات دیجیتال.'],
    ['num' => '۰۴', 'title' => 'کمپین تبلیغاتی', 'desc' => 'تدوین کانسپت، سناریونویسی، تولید و انتشار یکپارچه.'],
    ['num' => '۰۵', 'title' => 'طراحی وب', 'desc' => 'طراحی رابط و تجربه کاربری (UI/UX) منطبق بر هویت بصری برند.'],
    ['num' => '۰۶', 'title' => 'استراتژی و مشاوره', 'desc' => 'تحلیل مسئله و تبدیل اهداف بیزینس به نقشه راه اجرایی.'],
];

ob_start();
?>

<section class="relative container-px pt-6 pb-24 overflow-hidden">
    <div class="blob hero-blob" style="width:420px;height:420px;top:-120px;left:-140px;background:var(--color-accent);opacity:.55"></div>
    <div class="blob hero-blob" style="width:280px;height:280px;bottom:-80px;right:-60px;background:var(--color-secondary);opacity:.3;animation-delay:.4s"></div>

    <span class="eyebrow reveal-up is-visible"><?= e($settings['tagline']) ?></span>

    <h1 class="h-hero font-display mt-6 max-w-5xl">
        <?php $lines = explode('؛', $settings['hero_title']); foreach ($lines as $i => $line): ?>
            <span class="reveal-line hero-line"><span><?= e(trim($line)) ?><?= $i < count($lines) - 1 ? '؛' : '' ?></span></span>
        <?php endforeach; ?>
    </h1>

    <div class="mt-10 flex flex-col md:flex-row md:items-end justify-between gap-8">
        <p class="hero-fade max-w-xl text-base md:text-lg" style="color: var(--color-muted)">
            <?= e($settings['hero_subtitle']) ?>
        </p>
        <a href="<?= e($settings['hero_cta_link'] ?: url('/clients.php')) ?>" class="hero-fade btn-pill btn-solid shrink-0">
            <?= e($settings['hero_cta_text'] ?: 'دیدن نمونه‌کارها') ?> ↗
        </a>
    </div>
</section>

<?php if ($clients): ?>
    <section class="marquee-band py-6 mb-24">
        <div class="marquee-row">
            <div class="marquee-track">
                <?php for ($r = 0; $r < 2; $r++): foreach ($clients as $c): ?>
                    <span class="font-display font-extrabold text-2xl md:text-4xl px-8 whitespace-nowrap opacity-90"><?= e($c['name']) ?> <span class="opacity-40">✦</span></span>
                <?php endforeach; endfor; ?>
            </div>
        </div>
    </section>
<?php endif; ?>

<section class="container-px py-16 reveal-up">
    <span class="eyebrow">خدمات ما</span>
    <h2 class="h-section font-display mt-4 mb-10 max-w-2xl">از ایده تا اجرا، یکپارچه در کنار برند شما</h2>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php foreach ($services as $i => $service): ?>
            <div class="client-card p-6 flex flex-col gap-3 reveal-up" style="animation-delay: <?= $i * 0.05 ?>s">
                <span class="font-display font-extrabold text-2xl" style="color: var(--color-primary)"><?= e($service['num']) ?></span>
                <h3 class="font-display font-bold text-lg"><?= e($service['title']) ?></h3>
                <p class="text-sm" style="color: var(--color-muted)"><?= e($service['desc']) ?></p>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<?php if ($featured): ?>
    <section class="container-px py-10 reveal-up">
        <div class="flex items-end justify-between gap-6 mb-10 flex-wrap">
            <div>
                <span class="eyebrow">نمونه‌کارهای منتخب</span>
                <h2 class="h-section font-display mt-4">چند نگاه از کارهایی که ساختیم</h2>
            </div>
            <a href="<?= e(url('/clients.php')) ?>" class="btn-pill">همه همراهان ↗</a>
        </div>

        <div class="masonry masonry-3">
            <?php foreach ($featured as $i => $item): ?>
                <a href="<?= e(url('/category.php?client=' . rawurlencode($item['client_slug']) . '&category=' . rawurlencode($item['category_slug']))) ?>"
                   class="client-card block reveal-up" style="animation-delay: <?= $i * 0.06 ?>s">
                    <?php if ($item['media_type'] === 'video'): ?>
                        <video src="<?= e($item['media_url']) ?>" class="w-full h-auto block" muted loop playsinline autoplay></video>
                    <?php else: ?>
                        <img src="<?= e($item['media_url']) ?>" alt="<?= e($item['title'] ?: $item['client_name']) ?>" class="w-full h-auto block" loading="lazy">
                    <?php endif; ?>
                    <div class="p-4 flex items-center justify-between gap-3">
                        <span class="font-display font-bold text-sm"><?= e($item['client_name']) ?></span>
                        <span class="tag-pill"><?= e($item['category_title']) ?></span>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    </section>
<?php endif; ?>

<?php if ($featuredClients): ?>
    <section class="container-px py-16 reveal-up">
        <span class="eyebrow">همراهان ویژه</span>
        <h2 class="h-section font-display mt-4 mb-10">برندهایی که با هم بزرگ شدیم</h2>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php foreach ($featuredClients as $i => $client): ?>
                <a href="<?= e(url('/client.php?slug=' . rawurlencode($client['slug']))) ?>" class="client-card p-6 flex flex-col gap-4 reveal-up" style="animation-delay: <?= $i * 0.07 ?>s">
                    <?php if ($client['cover_image_url']): ?>
                        <div class="rounded-2xl overflow-hidden frame-pop aspect-16-9">
                            <img src="<?= e($client['cover_image_url']) ?>" alt="<?= e($client['name']) ?>" class="w-full h-full object-cover">
                        </div>
                    <?php endif; ?>
                    <div class="flex items-center justify-between gap-3">
                        <span class="font-display font-extrabold text-lg"><?= e($client['name']) ?></span>
                        <?php if ($client['industry']): ?><span class="tag-pill"><?= e($client['industry']) ?></span><?php endif; ?>
                    </div>
                    <?php if ($client['short_description']): ?>
                        <p class="text-sm" style="color: var(--color-muted)"><?= e(mb_strimwidth($client['short_description'], 0, 100, '…')) ?></p>
                    <?php endif; ?>
                </a>
            <?php endforeach; ?>
        </div>
    </section>
<?php endif; ?>

<section class="container-px py-16 reveal-up">
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
        <div class="client-card p-8 text-center">
            <span class="h-hero font-display text-pop block" style="font-size:clamp(2.5rem,5vw,4rem)"><?= e($settings['stat1_value']) ?></span>
            <span class="tag-pill mt-4 inline-flex"><?= e($settings['stat1_label']) ?></span>
        </div>
        <div class="client-card p-8 text-center">
            <span class="h-hero font-display text-pop block" style="font-size:clamp(2.5rem,5vw,4rem)"><?= e($settings['stat2_value']) ?></span>
            <span class="tag-pill mt-4 inline-flex"><?= e($settings['stat2_label']) ?></span>
        </div>
        <div class="client-card p-8 text-center">
            <span class="h-hero font-display text-pop block" style="font-size:clamp(2.5rem,5vw,4rem)"><?= e($settings['stat3_value']) ?></span>
            <span class="tag-pill mt-4 inline-flex"><?= e($settings['stat3_label']) ?></span>
        </div>
    </div>
</section>

<section class="container-px py-24 reveal-up">
    <div class="rounded-[36px] p-10 md:p-16 text-center frame-pop" style="background: var(--color-primary); color:#fff;">
        <h2 class="h-section font-display" style="color:#fff"><?= e($settings['about_title']) ?></h2>
        <a href="<?= e(url('/contact.php')) ?>" class="btn-pill btn-invert mt-8 inline-flex" style="background:#fff;color:var(--color-fg)">شروع گفت‌وگو ↗</a>
    </div>
</section>

<?php
$content = ob_get_clean();
$pageTitle = $settings['site_name'] . ' | ' . $settings['tagline'];
require_once __DIR__ . '/partials/site-layout.php';
