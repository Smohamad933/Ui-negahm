<?php
/**
 * سه نسبت تصویر ثابت مجاز برای هر دسته‌بندی، و کلاس‌های CSS متناظرشان.
 */

const ASPECT_OPTIONS = [
    '16:9' => '۱۶:۹ — افقی (کمپین، وب‌سایت)',
    '9:16' => '۹:۱۶ — عمودی (ریلز، استوری)',
    '1:1' => '۱:۱ — مربع (پست، محصول)',
];

/** کلاس CSS برای یک تایل با نسبت ثابت مشخص. */
const ASPECT_TILE_CLASS = [
    '16:9' => 'aspect-16-9',
    '9:16' => 'aspect-9-16',
    '1:1' => 'aspect-1-1',
];

/** چیدمان گرید مناسب هر شکل، برای گالری‌ای که همه‌ی آیتم‌هایش هم‌نسبت‌اند. */
const ASPECT_GRID_CLASS = [
    '16:9' => 'grid-2col',
    '9:16' => 'grid-4col',
    '1:1' => 'grid-3col',
];

function normalize_aspect(?string $value): string
{
    return in_array($value, ['9:16', '1:1'], true) ? $value : '16:9';
}
