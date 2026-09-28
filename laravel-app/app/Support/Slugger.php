<?php

namespace App\Support;

/**
 * Persian/Latin-friendly slug generator. Keeps Persian (Arabic-script) letters
 * intact instead of transliterating/stripping them, since URLs like
 * /clients/گالری-طلا-و-جواهر-محمود are desired for this project.
 */
class Slugger
{
    public static function make(string $input): string
    {
        $slug = mb_strtolower(trim($input), 'UTF-8');

        // Zero-width non-joiner + any whitespace -> single dash
        $slug = preg_replace('/[\x{200C}\s]+/u', '-', $slug) ?? $slug;

        // Strip anything that's not a-z, 0-9, Persian/Arabic letters, or dash
        $slug = preg_replace('/[^a-z0-9\x{0600}-\x{06FF}\-]+/u', '', $slug) ?? $slug;

        // Collapse multiple dashes
        $slug = preg_replace('/-+/', '-', $slug) ?? $slug;

        $slug = trim($slug, '-');

        return $slug !== '' ? $slug : ('item-' . base_convert((string) time(), 10, 36));
    }

    /**
     * Ensure a slug is unique within a callback-provided existence check,
     * appending -2, -3, ... on collision.
     */
    public static function unique(string $base, callable $exists): string
    {
        $slug = $base;
        $i = 2;
        while ($exists($slug)) {
            $slug = $base . '-' . $i;
            $i++;
        }

        return $slug;
    }
}
