<?php

namespace App\Support;

class Aspect
{
    public const OPTIONS = [
        '16:9' => '۱۶:۹ — افقی (کمپین، وب‌سایت)',
        '9:16' => '۹:۱۶ — عمودی (ریلز، استوری)',
        '1:1' => '۱:۱ — مربع (پست، محصول)',
    ];

    /** CSS class applied to a single tile for a given fixed aspect ratio. */
    public const TILE_CLASS = [
        '16:9' => 'aspect-16-9',
        '9:16' => 'aspect-9-16',
        '1:1' => 'aspect-1-1',
    ];

    /** Grid layout tuned per shape for a gallery of same-ratio tiles (no crop, no gaps). */
    public const GRID_CLASS = [
        '16:9' => 'grid-2col',
        '9:16' => 'grid-4col',
        '1:1' => 'grid-3col',
    ];

    public static function normalize(?string $value): string
    {
        return in_array($value, ['9:16', '1:1'], true) ? $value : '16:9';
    }
}
