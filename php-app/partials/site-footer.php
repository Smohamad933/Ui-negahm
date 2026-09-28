<?php /** @var array $settings */ ?>
<footer class="relative container-px pt-24 pb-10 mt-20" style="background: var(--color-fg); color: var(--color-bg)">
    <div class="flex flex-col gap-14">
        <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-8">
            <div>
                <span class="eyebrow eyebrow-invert">همکاری با ما</span>
                <h3 class="h-section mt-4 max-w-xl" style="color: var(--color-bg)">ایده‌ی بعدی برندت رو با هم بسازیم.</h3>
            </div>
            <a href="<?= e(url('/contact.php')) ?>" class="btn-pill btn-solid btn-invert whitespace-nowrap">شروع پروژه ↗</a>
        </div>

        <div style="border-top: 3px dashed color-mix(in srgb, var(--color-bg) 35%, transparent)"></div>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-sm">
            <div class="flex flex-col gap-3">
                <span class="opacity-60 font-display uppercase tracking-widest text-xs font-bold">صفحات</span>
                <a href="<?= e(url('/index.php')) ?>" class="hover:opacity-80">خانه</a>
                <a href="<?= e(url('/clients.php')) ?>" class="hover:opacity-80">همراهان</a>
                <a href="<?= e(url('/about.php')) ?>" class="hover:opacity-80">درباره ما</a>
                <a href="<?= e(url('/contact.php')) ?>" class="hover:opacity-80">تماس با ما</a>
            </div>
            <div class="flex flex-col gap-3">
                <span class="opacity-60 font-display uppercase tracking-widest text-xs font-bold">تماس</span>
                <a href="mailto:<?= e($settings['contact_email']) ?>" class="hover:opacity-80"><?= e($settings['contact_email']) ?></a>
                <span dir="ltr" class="text-right md:text-left"><?= e($settings['contact_phone']) ?></span>
                <span class="opacity-80"><?= e($settings['contact_address']) ?></span>
            </div>
            <div class="flex flex-col gap-3">
                <span class="opacity-60 font-display uppercase tracking-widest text-xs font-bold">شبکه‌های اجتماعی</span>
                <?php if (! empty($settings['social_instagram'])): ?><a href="<?= e($settings['social_instagram']) ?>" target="_blank" rel="noopener" class="hover:opacity-80">اینستاگرام</a><?php endif; ?>
                <?php if (! empty($settings['social_telegram'])): ?><a href="<?= e($settings['social_telegram']) ?>" target="_blank" rel="noopener" class="hover:opacity-80">تلگرام</a><?php endif; ?>
                <?php if (! empty($settings['social_whatsapp'])): ?><a href="<?= e($settings['social_whatsapp']) ?>" target="_blank" rel="noopener" class="hover:opacity-80">واتس‌اپ</a><?php endif; ?>
                <?php if (! empty($settings['social_linkedin'])): ?><a href="<?= e($settings['social_linkedin']) ?>" target="_blank" rel="noopener" class="hover:opacity-80">لینکدین</a><?php endif; ?>
            </div>
            <div class="flex flex-col gap-3">
                <span class="opacity-60 font-display uppercase tracking-widest text-xs font-bold">استودیو</span>
                <span class="opacity-80"><?= e($settings['tagline']) ?></span>
            </div>
        </div>

        <div style="border-top: 3px dashed color-mix(in srgb, var(--color-bg) 35%, transparent)"></div>

        <div class="flex flex-col md:flex-row justify-between gap-3 text-xs opacity-70 font-display uppercase tracking-widest font-bold">
            <span>© <?= date('Y') ?> <?= e($settings['site_name']) ?></span>
            <span><?= e($settings['footer_text']) ?></span>
        </div>
    </div>
</footer>
