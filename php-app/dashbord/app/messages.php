<?php
require_once __DIR__ . '/../../lib/bootstrap.php';
require_admin();

if (is_post()) {
    csrf_verify_or_die();
    $action = input('action');
    $messageId = (int) input('message_id');

    if ($action === 'toggle_read' && $messageId) {
        db_run('UPDATE messages SET is_read = 1 - is_read WHERE id = ?', [$messageId]);
    } elseif ($action === 'delete' && $messageId) {
        db_run('DELETE FROM messages WHERE id = ?', [$messageId]);
        flash_set('status', 'پیام حذف شد.');
    }

    redirect('/dashbord/app/messages.php');
}

$messages = db_all('SELECT * FROM messages ORDER BY created_at DESC, id DESC');

ob_start();
?>

<?php if (! $messages): ?>
    <p style="color: var(--a-muted)">هنوز پیامی دریافت نشده است.</p>
<?php else: ?>
    <div class="admin-card overflow-x-auto">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>وضعیت</th>
                    <th>نام</th>
                    <th>ایمیل / تلفن</th>
                    <th>موضوع</th>
                    <th>پیام</th>
                    <th>تاریخ</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($messages as $message): ?>
                    <tr style="<?= ! $message['is_read'] ? 'font-weight:700' : 'opacity:.75' ?>">
                        <td>
                            <form method="POST" action="<?= e(url('/dashbord/app/messages.php')) ?>">
                                <?= csrf_field() ?>
                                <input type="hidden" name="message_id" value="<?= (int) $message['id'] ?>">
                                <input type="hidden" name="action" value="toggle_read">
                                <button type="submit" class="admin-badge" style="<?= ! $message['is_read'] ? 'border-color: var(--a-primary); color: var(--a-primary)' : '' ?>">
                                    <?= $message['is_read'] ? 'خوانده‌شده' : 'خوانده‌نشده' ?>
                                </button>
                            </form>
                        </td>
                        <td><?= e($message['name']) ?></td>
                        <td>
                            <div dir="ltr" class="text-left"><?= e($message['email']) ?></div>
                            <?php if ($message['phone']): ?><div dir="ltr" class="text-left" style="color: var(--a-muted)"><?= e($message['phone']) ?></div><?php endif; ?>
                        </td>
                        <td><?= e($message['subject'] ?: '—') ?></td>
                        <td class="max-w-xs"><?= e(mb_strimwidth($message['message'], 0, 90, '…')) ?></td>
                        <td style="color: var(--a-muted); white-space:nowrap"><?= e($message['created_at'] ? date('Y/m/d H:i', strtotime($message['created_at'])) : '') ?></td>
                        <td>
                            <form method="POST" action="<?= e(url('/dashbord/app/messages.php')) ?>" onsubmit="return confirm('پیام حذف شود؟');">
                                <?= csrf_field() ?>
                                <input type="hidden" name="message_id" value="<?= (int) $message['id'] ?>">
                                <input type="hidden" name="action" value="delete">
                                <button type="submit" class="admin-btn admin-btn-danger">حذف</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>

<?php
$content = ob_get_clean();
$pageTitle = 'پیام‌های تماس';
$activeNav = 'messages';
require_once __DIR__ . '/../../partials/admin-layout.php';
