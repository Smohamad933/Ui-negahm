<?php

namespace App\Support;

use App\Models\Font;
use App\Models\Setting;
use Illuminate\Support\Collection;

class Theme
{
    private const FORMAT_MAP = [
        'woff2' => 'woff2',
        'woff' => 'woff',
        'ttf' => 'truetype',
        'otf' => 'opentype',
    ];

    /**
     * Builds an inline <style> payload with any custom @font-face rules for
     * the currently active font family plus the admin-editable CSS variables.
     */
    public static function css(Setting $settings): string
    {
        /** @var Collection<int, Font> $customFontFiles */
        $customFontFiles = Font::query()->where('family_name', $settings->font_family)->get();

        $fontFaceRules = $customFontFiles->map(function (Font $f) {
            $format = self::FORMAT_MAP[$f->format] ?? $f->format;

            return "@font-face {\n"
                . "  font-family: '" . addslashes($f->family_name) . "';\n"
                . "  src: url('" . addslashes($f->file_url) . "') format('{$format}');\n"
                . "  font-weight: " . addslashes($f->weight) . ";\n"
                . "  font-style: " . addslashes($f->style) . ";\n"
                . "  font-display: swap;\n"
                . "}";
        })->implode("\n");

        $fontFamily = addslashes($settings->font_family);

        return <<<CSS
            {$fontFaceRules}
            :root {
              --color-bg: {$settings->color_bg};
              --color-fg: {$settings->color_fg};
              --color-primary: {$settings->color_primary};
              --color-secondary: {$settings->color_secondary};
              --color-accent: {$settings->color_accent};
              --color-muted: {$settings->color_muted};
              --font-family: '{$fontFamily}', 'Vazirmatn Variable', sans-serif;
            }
            html, body {
              background-color: var(--color-bg);
              color: var(--color-fg);
              font-family: var(--font-family);
            }
            CSS;
    }
}
