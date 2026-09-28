<?php
/**
 * احراز هویت پنل مدیریت: مبتنی بر سشن ساده‌ی PHP (بدون فریم‌ورک).
 */

function current_admin(): ?array
{
    static $cached = null;
    static $loaded = false;

    if ($loaded) {
        return $cached;
    }

    $loaded = true;
    $id = $_SESSION[ADMIN_SESSION_KEY] ?? null;

    if ($id === null) {
        return null;
    }

    $cached = db_one('SELECT * FROM admin_users WHERE id = ?', [$id]);

    return $cached;
}

function require_admin(): array
{
    $admin = current_admin();

    if ($admin === null) {
        redirect('/dashbord/app/login.php');
    }

    return $admin;
}

function attempt_login(string $username, string $password): bool
{
    $admin = db_one('SELECT * FROM admin_users WHERE username = ?', [$username]);

    if ($admin === null || ! password_verify($password, $admin['password_hash'])) {
        return false;
    }

    session_regenerate_id(true);
    $_SESSION[ADMIN_SESSION_KEY] = $admin['id'];

    return true;
}

function admin_logout(): void
{
    unset($_SESSION[ADMIN_SESSION_KEY]);
    session_regenerate_id(true);
}
