<?php
require_once __DIR__ . '/lib/bootstrap.php';

$settings = settings();
$slug = input('slug', '');
$client = $slug !== '' ? db_one('SELECT * FROM clients WHERE slug = ?', [$slug]) : null;

if ($client === null || ! $client['published']) {
    http_response_code(404);
    $content = '<section class="container-px py-24 text-center"><h1 class="h-section font-display">همراه موردنظر پیدا نشد.</h1>'
        . '<a href="' . e(url('/clients.php')) . '" class="btn-pill mt-8 inline-flex">بازگشت به همراهان ↗</a></section>';
    $pageTitle = 'یافت نشد | ' . $settings['site_name'];
    require_once __DIR__ . '/partials/site-layout.php';
    exit;
}

$categories = db_all('SELECT * FROM categories WHERE client_id = ? ORDER BY order_index ASC', [$client['id']]);

foreach ($categories as &$cat) {
    if (! $cat['cover_image_url']) {
        $first = db_one('SELECT media_url FROM portfolio_items WHERE category_id = ? ORDER BY order_index ASC LIMIT 1', [$cat['id']]);
        $cat['cover_image_url'] = $first['media_url'] ?? '';
    }
}
unset($cat);

ob_start();
?>

<section class="container-px pt-6 pb-10">
    <a href="<?= e(url('/clients.php')) ?>" class="tag-pill inline-flex mb-8">→ بازگشت به همراهان</a>

    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6">
        <div>
            <?php if ($client['industry']): ?><span class="eyebrow"><?= e($client['industry']) ?></span><?php endif; ?>
            <h1 class="h-hero font-display mt-6"><?= e($client['name']) ?></h1>
            <?php if ($client['short_description']): ?>
                <p class="max-w-2xl mt-6 text-base md:text-lg" style="color: var(--color-muted)"><?= e($client['short_description']) ?></p>
            <?php endif; ?>
        </div>
        <?php if ($client['website_url']): ?>
            <a href="<?= e($client['website_url']) ?>" target="_blank" rel="noopener" class="btn-pill shrink-0">وب‌سایت ↗</a>
        <?php endif; ?>
    </div>

    <div class="flex flex-wrap gap-3 mt-8 text-sm" style="color: var(--color-muted)">
        <?php if ($client['year']): ?><span class="tag-pill">سال <?= e($client['year']) ?></span><?php endif; ?>
    </div>
</section>

<section class="container-px pb-24">
    <?php if (! $categories): ?>
        <p class="reveal-up" style="color: var(--color-muted)">هنوز نمونه‌کاری برای این همراه ثبت نشده است.</p>
    <?php else: ?>
        <div class="masonry masonry-3">
            <?php foreach ($categories as $i => $category): $ratio = normalize_aspect($category['aspect_ratio']); ?>
                <a href="<?= e(url('/category.php?client=' . rawurlencode($client['slug']) . '&category=' . rawurlencode($category['slug']))) ?>"
                   class="client-card block reveal-up" style="animation-delay: <?= $i * 0.06 ?>s">
                    <?php if ($category['cover_image_url']): ?>
                        <div class="<?= e(ASPECT_TILE_CLASS[$ratio]) ?> overflow-hidden">
                            <img src="<?= e($category['cover_image_url']) ?>" alt="<?= e($category['title']) ?>" class="w-full h-full object-cover" loading="lazy">
                        </div>
                    <?php endif; ?>
                    <div class="p-4">
                        <span class="font-display font-bold"><?= e($category['title']) ?></span>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>

<?php
$content = ob_get_clean();
$pageTitle = $client['name'] . ' | ' . $settings['site_name'];
require_once __DIR__ . '/partials/site-layout.php';
