<?php
require_once __DIR__ . '/lib/bootstrap.php';

$settings = settings();
$clients = db_all('SELECT * FROM clients WHERE published = 1 ORDER BY order_index ASC, id DESC');

$industries = [];
foreach ($clients as $c) {
    if ($c['industry'] && ! in_array($c['industry'], $industries, true)) {
        $industries[] = $c['industry'];
    }
}
$active = input('industry', '');

ob_start();
?>

<section class="container-px pt-6 pb-10">
    <span class="eyebrow">نمونه‌کارها</span>
    <h1 class="h-hero font-display mt-6">همراهانی که با هم ساختیم</h1>

    <?php if ($industries): ?>
        <div class="flex flex-wrap gap-3 mt-10">
            <a href="<?= e(url('/clients.php')) ?>" class="tag-pill" style="<?= $active === '' ? 'background:var(--color-fg);color:var(--color-bg)' : '' ?>">همه</a>
            <?php foreach ($industries as $industry): ?>
                <a href="<?= e(url('/clients.php?industry=' . rawurlencode($industry))) ?>" class="tag-pill" style="<?= $active === $industry ? 'background:var(--color-fg);color:var(--color-bg)' : '' ?>"><?= e($industry) ?></a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>

<section class="container-px pb-24">
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php $i = 0; foreach ($clients as $client): ?>
            <?php if ($active !== '' && $client['industry'] !== $active) { continue; } ?>
            <a href="<?= e(url('/client.php?slug=' . rawurlencode($client['slug']))) ?>" class="client-card p-6 flex flex-col gap-4 reveal-up" style="animation-delay: <?= $i * 0.05 ?>s">
                <?php if ($client['cover_image_url']): ?>
                    <div class="rounded-2xl overflow-hidden frame-pop aspect-16-9">
                        <img src="<?= e($client['cover_image_url']) ?>" alt="<?= e($client['name']) ?>" class="w-full h-full object-cover" loading="lazy">
                    </div>
                <?php endif; ?>
                <div class="flex items-center justify-between gap-3">
                    <span class="font-display font-extrabold text-lg"><?= e($client['name']) ?></span>
                    <?php if ($client['industry']): ?><span class="tag-pill"><?= e($client['industry']) ?></span><?php endif; ?>
                </div>
                <?php if ($client['short_description']): ?>
                    <p class="text-sm" style="color: var(--color-muted)"><?= e(mb_strimwidth($client['short_description'], 0, 110, '…')) ?></p>
                <?php endif; ?>
            </a>
            <?php $i++; ?>
        <?php endforeach; ?>
    </div>
</section>

<?php
$content = ob_get_clean();
$pageTitle = 'همراهان | ' . $settings['site_name'];
require_once __DIR__ . '/partials/site-layout.php';
