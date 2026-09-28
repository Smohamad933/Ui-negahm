<?php
/**
 * ساخت اسلاگ فارسی/لاتین. حروف فارسی حذف نمی‌شوند (برخلاف اسلاگ‌سازهای
 * معمول که فقط لاتین نگه می‌دارند) چون در این پروژه اسلاگ فارسی هم مجاز است.
 */

function make_slug(string $input): string
{
    $slug = mb_strtolower(trim($input), 'UTF-8');

    // نویسه‌ی نیم‌فاصله + هر نوع فاصله -> یک خط تیره
    $slug = preg_replace('/[\x{200C}\s]+/u', '-', $slug) ?? $slug;

    // حذف هر چیزی جز a-z, 0-9, حروف فارسی/عربی، یا خط تیره
    $slug = preg_replace('/[^a-z0-9\x{0600}-\x{06FF}\-]+/u', '', $slug) ?? $slug;

    // یکی‌کردن چند خط‌تیره‌ی پشت‌سرهم
    $slug = preg_replace('/-+/', '-', $slug) ?? $slug;

    $slug = trim($slug, '-');

    return $slug !== '' ? $slug : ('item-' . base_convert((string) time(), 10, 36));
}

/**
 * تضمین یکتا بودن اسلاگ با افزودن -2، -3 و... در صورت تداخل.
 * $exists باید یک callable باشد که یک اسلاگ می‌گیرد و true/false برمی‌گرداند.
 */
function unique_slug(string $base, callable $exists): string
{
    $slug = $base;
    $i = 2;

    while ($exists($slug)) {
        $slug = $base . '-' . $i;
        $i++;
    }

    return $slug;
}
