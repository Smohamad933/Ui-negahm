<?php
/**
 * ساخت CSS متغیرهای رنگ/فونت بر اساس تنظیمات دیتابیس، برای echo مستقیم در
 * <head> سایت عمومی.
 */

function theme_css(array $settings): string
{
    $formatMap = [
        'woff2' => 'woff2',
        'woff' => 'woff',
        'ttf' => 'truetype',
        'otf' => 'opentype',
    ];

    $fonts = db_all('SELECT * FROM fonts WHERE family_name = ?', [$settings['font_family']]);

    $fontFaceRules = [];
    foreach ($fonts as $f) {
        $format = $formatMap[$f['format']] ?? $f['format'];
        $fontFaceRules[] = "@font-face {\n"
            . "  font-family: '" . addslashes($f['family_name']) . "';\n"
            . "  src: url('" . addslashes($f['file_url']) . "') format('{$format}');\n"
            . '  font-weight: ' . addslashes($f['weight']) . ";\n"
            . '  font-style: ' . addslashes($f['style']) . ";\n"
            . "  font-display: swap;\n"
            . '}';
    }

    $fontFaceCss = implode("\n", $fontFaceRules);
    $fontFamily = addslashes($settings['font_family']);

    return <<<CSS
{$fontFaceCss}
:root {
  --color-bg: {$settings['color_bg']};
  --color-fg: {$settings['color_fg']};
  --color-primary: {$settings['color_primary']};
  --color-secondary: {$settings['color_secondary']};
  --color-accent: {$settings['color_accent']};
  --color-muted: {$settings['color_muted']};
  --font-family: '{$fontFamily}', 'Vazirmatn Variable', sans-serif;
}
html, body {
  background-color: var(--color-bg);
  color: var(--color-fg);
  font-family: var(--font-family);
}
CSS;
}
