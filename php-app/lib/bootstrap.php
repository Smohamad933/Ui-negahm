<?php
/**
 * فایل شروع مشترک؛ در ابتدای هر صفحه include می‌شود.
 */

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/uploads.php';
require_once __DIR__ . '/slug.php';
require_once __DIR__ . '/theme.php';
require_once __DIR__ . '/aspect.php';

if (! is_file(DB_PATH)) {
    http_response_code(500);
    echo '<!doctype html><html lang="fa" dir="rtl"><meta charset="utf-8">'
        . '<body style="font-family:sans-serif;padding:2rem;line-height:2">'
        . '<h1>فایل دیتابیس پیدا نشد</h1>'
        . '<p>فایل <code>data/app.sqlite</code> وجود ندارد. مطمئن شوید این فایل را همراه بقیه‌ی '
        . 'پروژه آپلود کرده‌اید و پوشه‌ی <code>data/</code> برای کاربر IIS قابل‌نوشتن است.</p>'
        . '</body></html>';
    exit;
}

function settings(): array
{
    static $cached = null;

    if ($cached === null) {
        $cached = db_one('SELECT * FROM settings WHERE id = 1');

        if ($cached === null) {
            db_run('INSERT INTO settings (id) VALUES (1)');
            $cached = db_one('SELECT * FROM settings WHERE id = 1');
        }
    }

    return $cached;
}
