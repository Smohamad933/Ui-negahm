<?php
/**
 * کمک‌توابع عمومی: escape کردن HTML، ساخت URL، کار با فلش‌مسیج و CSRF.
 */

function e($value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

/** ساخت یک URL نسبت به ریشه‌ی سایت (به BASE_PATH احترام می‌گذارد). */
function url(string $path = ''): string
{
    if ($path === '') {
        return BASE_PATH === '' ? '/' : BASE_PATH;
    }

    return BASE_PATH . $path;
}

function asset(string $path): string
{
    return url($path);
}

function redirect(string $path): void
{
    header('Location: ' . url($path));
    exit;
}

function current_method(): string
{
    return $_SERVER['REQUEST_METHOD'] ?? 'GET';
}

function is_post(): bool
{
    return current_method() === 'POST';
}

/** ورودی POST/GET را برمی‌گرداند و فاصله‌های ابتدا/انتها را حذف می‌کند. */
function input(string $key, $default = '')
{
    $value = $_POST[$key] ?? $_GET[$key] ?? $default;

    return is_string($value) ? trim($value) : $value;
}

function input_bool(string $key): bool
{
    $value = $_POST[$key] ?? null;

    return $value === '1' || $value === 'on' || $value === 'true';
}

/** ------------------------------------------------------------------ */
/** فلش‌مسیج‌ها (پیام موفقیت/خطا که فقط یک بار بعد از ریدایرکت نمایش داده می‌شود) */

function flash_set(string $key, $value): void
{
    $_SESSION['flash'][$key] = $value;
}

function flash_get(string $key)
{
    if (! isset($_SESSION['flash'][$key])) {
        return null;
    }

    $value = $_SESSION['flash'][$key];
    unset($_SESSION['flash'][$key]);

    return $value;
}

/** ------------------------------------------------------------------ */
/** CSRF: یک توکن سشن ساده برای فرم‌های POST */

function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="_csrf" value="' . e(csrf_token()) . '">';
}

function csrf_check(): bool
{
    $token = $_POST['_csrf'] ?? '';

    return is_string($token) && hash_equals($_SESSION['csrf_token'] ?? '', $token);
}

/** اگر درخواست POST بود ولی توکن نامعتبر بود، با خطای ۴۰۰ متوقف می‌شود. */
function csrf_verify_or_die(): void
{
    if (is_post() && ! csrf_check()) {
        http_response_code(400);
        echo 'درخواست نامعتبر است (CSRF). لطفاً صفحه را رفرش کرده و دوباره تلاش کنید.';
        exit;
    }
}
