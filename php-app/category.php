<?php
require_once __DIR__ . '/lib/bootstrap.php';

$settings = settings();
$clientSlug = input('client', '');
$categorySlug = input('category', '');

$client = $clientSlug !== '' ? db_one('SELECT * FROM clients WHERE slug = ?', [$clientSlug]) : null;
$category = null;

if ($client !== null && $categorySlug !== '') {
    $category = db_one('SELECT * FROM categories WHERE client_id = ? AND slug = ?', [$client['id'], $categorySlug]);
}

if ($client === null || ! $client['published'] || $category === null) {
    http_response_code(404);
    $content = '<section class="container-px py-24 text-center"><h1 class="h-section font-display">این صفحه پیدا نشد.</h1>'
        . '<a href="' . e(url('/clients.php')) . '" class="btn-pill mt-8 inline-flex">بازگشت به همراهان ↗</a></section>';
    $pageTitle = 'یافت نشد | ' . $settings['site_name'];
    require_once __DIR__ . '/partials/site-layout.php';
    exit;
}

$items = db_all('SELECT * FROM portfolio_items WHERE category_id = ? ORDER BY order_index ASC', [$category['id']]);
$otherCategories = db_all('SELECT * FROM categories WHERE client_id = ? AND id != ? ORDER BY order_index ASC', [$client['id'], $category['id']]);

$ratio = normalize_aspect($category['aspect_ratio']);
$tileClass = ASPECT_TILE_CLASS[$ratio];
$gridClass = ASPECT_GRID_CLASS[$ratio];

ob_start();
?>

<section class="container-px pt-6 pb-10">
    <a href="<?= e(url('/client.php?slug=' . rawurlencode($client['slug']))) ?>" class="tag-pill inline-flex mb-8">→ بازگشت به <?= e($client['name']) ?></a>

    <span class="eyebrow"><?= e($client['name']) ?></span>
    <h1 class="h-hero font-display mt-6"><?= e($category['title']) ?></h1>
    <?php if ($category['description']): ?>
        <p class="max-w-2xl mt-6 text-base md:text-lg" style="color: var(--color-muted)"><?= e($category['description']) ?></p>
    <?php endif; ?>

    <?php if ($otherCategories): ?>
        <div class="flex flex-wrap gap-3 mt-8">
            <?php foreach ($otherCategories as $other): ?>
                <a href="<?= e(url('/category.php?client=' . rawurlencode($client['slug']) . '&category=' . rawurlencode($other['slug']))) ?>" class="tag-pill"><?= e($other['title']) ?></a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>

<section class="container-px pb-24">
    <?php if (! $items): ?>
        <p class="reveal-up" style="color: var(--color-muted)">هنوز موردی در این دسته‌بندی ثبت نشده است.</p>
    <?php else: ?>
        <div class="<?= e($gridClass) ?>">
            <?php foreach ($items as $i => $item): ?>
                <a href="#lightbox-<?= (int) $item['id'] ?>" class="client-card block reveal-up <?= e($tileClass) ?> overflow-hidden" style="animation-delay: <?= $i * 0.05 ?>s">
                    <?php if ($item['media_type'] === 'video'): ?>
                        <video src="<?= e($item['media_url']) ?>" class="w-full h-full object-cover" muted loop playsinline autoplay></video>
                    <?php else: ?>
                        <img src="<?= e($item['media_url']) ?>" alt="<?= e($item['title'] ?: $category['title']) ?>" class="w-full h-full object-cover" loading="lazy">
                    <?php endif; ?>
                </a>
            <?php endforeach; ?>
        </div>

        <?php foreach ($items as $item): ?>
            <div id="lightbox-<?= (int) $item['id'] ?>" class="lightbox">
                <a href="#" class="lightbox-close">بستن ✕</a>
                <?php if ($item['media_type'] === 'video'): ?>
                    <video src="<?= e($item['media_url']) ?>" controls autoplay></video>
                <?php else: ?>
                    <img src="<?= e($item['media_url']) ?>" alt="<?= e($item['title'] ?: $category['title']) ?>">
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</section>

<?php
$content = ob_get_clean();
$pageTitle = $category['title'] . ' — ' . $client['name'] . ' | ' . $settings['site_name'];
require_once __DIR__ . '/partials/site-layout.php';
