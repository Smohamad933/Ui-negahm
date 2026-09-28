<?php
/**
 * لایه‌ی دسترسی به دیتابیس (PDO/SQLite ساده، بدون ORM).
 */

function db(): PDO
{
    static $pdo = null;

    if ($pdo === null) {
        $pdo = new PDO('sqlite:' . DB_PATH);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        $pdo->exec('PRAGMA foreign_keys = ON');
    }

    return $pdo;
}

/** @return array<int, array<string, mixed>> */
function db_all(string $sql, array $params = []): array
{
    $stmt = db()->prepare($sql);
    $stmt->execute($params);

    return $stmt->fetchAll();
}

/** @return array<string, mixed>|null */
function db_one(string $sql, array $params = []): ?array
{
    $stmt = db()->prepare($sql);
    $stmt->execute($params);
    $row = $stmt->fetch();

    return $row === false ? null : $row;
}

function db_run(string $sql, array $params = []): PDOStatement
{
    $stmt = db()->prepare($sql);
    $stmt->execute($params);

    return $stmt;
}

function db_insert_id(): int
{
    return (int) db()->lastInsertId();
}

/** برمی‌گرداند مقدار اولین ستونِ اولین ردیف، یا null. */
function db_value(string $sql, array $params = [])
{
    $row = db_one($sql, $params);

    if ($row === null) {
        return null;
    }

    return array_values($row)[0];
}
