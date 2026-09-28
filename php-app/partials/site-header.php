<?php
/** @var array $settings */
$navLinks = [
    ['href' => url('/index.php'), 'label' => 'خانه', 'num' => '۰۱'],
    ['href' => url('/clients.php'), 'label' => 'همراهان', 'num' => '۰۲'],
    ['href' => url('/about.php'), 'label' => 'درباره ما', 'num' => '۰۳'],
    ['href' => url('/contact.php'), 'label' => 'تماس با ما', 'num' => '۰۴'],
];
?>
<header class="fixed top-0 inset-x-0 z-50 container-px pt-4">
    <div class="flex items-center justify-between rounded-full px-6 py-3 border-[2.5px] max-w-6xl mx-auto"
         style="background: var(--color-bg); border-color: var(--color-fg); box-shadow: 4px 4px 0 var(--color-fg);">
        <a href="<?= e(url('/index.php')) ?>" class="flex items-center gap-3">
            <?php if (! empty($settings['logo_url'])): ?>
                <img src="<?= e($settings['logo_url']) ?>" alt="<?= e($settings['site_name']) ?>" class="h-8 w-auto">
            <?php else: ?>
                <span class="font-display font-extrabold text-lg tracking-tight" style="color: var(--color-fg)"><?= e($settings['site_name']) ?></span>
            <?php endif; ?>
        </a>

        <button type="button" id="nav-toggle" class="relative z-[70] flex items-center gap-3 font-display text-xs uppercase tracking-[0.2em] font-bold" style="color: var(--color-fg)">
            <span class="nav-toggle-label">منو</span>
            <span class="relative flex h-8 w-8 flex-col items-center justify-center gap-[6px] rounded-full border-2" style="border-color: var(--color-fg)">
                <span class="h-[2px] w-4" style="background: currentColor"></span>
                <span class="h-[2px] w-4" style="background: currentColor"></span>
            </span>
        </button>
    </div>
</header>

<div class="nav-overlay container-px pt-28 pb-10">
    <nav class="flex flex-col gap-2">
        <?php foreach ($navLinks as $link): ?>
            <div class="py-4 flex items-center justify-between gap-4" style="border-bottom: 3px dashed color-mix(in srgb, var(--color-fg) 30%, transparent);">
                <a href="<?= e($link['href']) ?>" class="font-display font-extrabold text-[clamp(2.2rem,7vw,5.5rem)] leading-none tracking-tight">
                    <?= e($link['label']) ?>
                </a>
                <span class="hidden md:flex h-14 w-14 shrink-0 rounded-full border-[2.5px] items-center justify-center text-sm font-display font-bold" style="border-color: var(--color-fg); background: var(--color-accent)">
                    <?= e($link['num']) ?>
                </span>
            </div>
        <?php endforeach; ?>
    </nav>

    <div class="flex flex-wrap items-end justify-between gap-6 text-sm" style="color: var(--color-muted)">
        <div class="flex flex-col gap-1">
            <span class="eyebrow">ارتباط</span>
            <a href="mailto:<?= e($settings['contact_email']) ?>" style="color: var(--color-fg)"><?= e($settings['contact_email']) ?></a>
            <span dir="ltr" class="text-left"><?= e($settings['contact_phone']) ?></span>
        </div>
        <div class="flex gap-5 font-display uppercase tracking-widest text-xs">
            <?php if (! empty($settings['social_instagram'])): ?><a href="<?= e($settings['social_instagram']) ?>" target="_blank" rel="noopener">Instagram</a><?php endif; ?>
            <?php if (! empty($settings['social_telegram'])): ?><a href="<?= e($settings['social_telegram']) ?>" target="_blank" rel="noopener">Telegram</a><?php endif; ?>
            <?php if (! empty($settings['social_linkedin'])): ?><a href="<?= e($settings['social_linkedin']) ?>" target="_blank" rel="noopener">LinkedIn</a><?php endif; ?>
        </div>
    </div>
</div>
