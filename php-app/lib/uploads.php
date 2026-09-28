<?php
/**
 * آپلود فایل مستقیم روی دیسک (uploads/images یا uploads/fonts) — بدون
 * هیچ کتابخانه‌ی خارجی. مسیر عمومی (شروع‌شونده با /uploads/...) برگردانده
 * می‌شود که مستقیماً در دیتابیس ذخیره می‌شود.
 */

const ALLOWED_IMAGE_EXTENSIONS = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
const ALLOWED_VIDEO_EXTENSIONS = ['mp4', 'webm'];
const ALLOWED_FONT_EXTENSIONS = ['woff2', 'woff', 'ttf', 'otf'];
const MAX_UPLOAD_BYTES = 20 * 1024 * 1024; // 20MB

/**
 * $file باید یک عنصر از $_FILES باشد (آرایه با کلیدهای name/tmp_name/error/size).
 * در صورت خطا یک رشته‌ی پیام خطا برمی‌گرداند؛ در صورت موفقیت مسیر عمومی را.
 *
 * @return array{0: string|null, 1: string|null} [publicPath, errorMessage]
 */
function handle_upload(array $file, string $folder, array $allowedExtensions): array
{
    if (! isset($file['error']) || $file['error'] === UPLOAD_ERR_NO_FILE) {
        return [null, null];
    }

    if ($file['error'] !== UPLOAD_ERR_OK) {
        return [null, 'خطا در آپلود فایل (کد ' . $file['error'] . ').'];
    }

    if ($file['size'] > MAX_UPLOAD_BYTES) {
        return [null, 'حجم فایل بیش از حد مجاز است (حداکثر ۲۰ مگابایت).'];
    }

    $originalName = $file['name'] ?? 'file';
    $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

    if (! in_array($extension, $allowedExtensions, true)) {
        return [null, 'فرمت فایل مجاز نیست (' . implode(', ', $allowedExtensions) . ').'];
    }

    $targetDir = UPLOADS_DIR . '/' . $folder;
    if (! is_dir($targetDir)) {
        mkdir($targetDir, 0775, true);
    }

    $filename = date('Ymd_His') . '_' . bin2hex(random_bytes(5)) . '.' . $extension;
    $targetPath = $targetDir . '/' . $filename;

    if (! is_uploaded_file($file['tmp_name']) || ! move_uploaded_file($file['tmp_name'], $targetPath)) {
        return [null, 'ذخیره‌ی فایل روی سرور با خطا مواجه شد. دسترسی نوشتن پوشه‌ی uploads را بررسی کنید.'];
    }

    return [UPLOADS_URL . '/' . $folder . '/' . $filename, null];
}

/** فقط فایل‌هایی که واقعاً داخل uploads/ ما هستند حذف می‌شوند (نه فایل‌های seed-demo). */
function delete_public_path(?string $publicPath): void
{
    if (! $publicPath || strpos($publicPath, UPLOADS_URL . '/') !== 0) {
        return;
    }

    $relative = substr($publicPath, strlen(UPLOADS_URL) + 1);
    $fullPath = UPLOADS_DIR . '/' . $relative;

    // جلوگیری از path traversal
    if (strpos($relative, '..') !== false) {
        return;
    }

    if (is_file($fullPath)) {
        @unlink($fullPath);
    }
}
