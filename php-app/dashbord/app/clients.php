<?php
require_once __DIR__ . '/../../lib/bootstrap.php';
require_admin();

if (is_post()) {
    csrf_verify_or_die();
    $action = input('action');
    $clientId = (int) input('client_id');

    if ($action === 'toggle_published' && $clientId) {
        db_run('UPDATE clients SET published = 1 - published WHERE id = ?', [$clientId]);
    } elseif ($action === 'toggle_featured' && $clientId) {
        db_run('UPDATE clients SET featured = 1 - featured WHERE id = ?', [$clientId]);
    } elseif ($action === 'delete' && $clientId) {
        $items = db_all(
            'SELECT pi.media_url FROM portfolio_items pi JOIN categories c ON c.id = pi.category_id WHERE c.client_id = ?',
            [$clientId]
        );
        foreach ($items as $item) {
            delete_public_path($item['media_url']);
        }
        $client = db_one('SELECT logo_url, cover_image_url FROM clients WHERE id = ?', [$clientId]);
        if ($client) {
            delete_public_path($client['logo_url']);
            delete_public_path($client['cover_image_url']);
        }
        db_run('DELETE FROM clients WHERE id = ?', [$clientId]);
        flash_set('status', 'کارفرما حذف شد.');
    } elseif ($action === 'reorder' && $clientId) {
        $direction = input('direction');
        $ordered = db_all('SELECT id, order_index FROM clients ORDER BY order_index ASC, id DESC');
        $index = null;
        foreach ($ordered as $i => $row) {
            if ((int) $row['id'] === $clientId) {
                $index = $i;
                break;
            }
        }
        if ($index !== null) {
            $target = $direction === 'up' ? $index - 1 : $index + 1;
            if ($target >= 0 && $target < count($ordered)) {
                $a = $ordered[$index];
                $b = $ordered[$target];
                db_run('UPDATE clients SET order_index = ? WHERE id = ?', [$b['order_index'], $a['id']]);
                db_run('UPDATE clients SET order_index = ? WHERE id = ?', [$a['order_index'], $b['id']]);
            }
        }
    }

    redirect('/dashbord/app/clients.php');
}

$clients = db_all('SELECT * FROM clients ORDER BY order_index ASC, id DESC');

ob_start();
?>

<div class="flex justify-between items-center mb-6">
    <a href="<?= e(url('/dashbord/app/client-form.php')) ?>" class="admin-btn admin-btn-primary">+ افزودن کارفرمای جدید</a>
</div>

<?php if (! $clients): ?>
    <p style="color: var(--a-muted)">هنوز کارفرمایی ثبت نشده است.</p>
<?php else: ?>
    <div class="admin-card overflow-x-auto">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ترتیب</th>
                    <th>کارفرما</th>
                    <th>حوزه فعالیت</th>
                    <th>وضعیت</th>
                    <th>ویژه</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($clients as $i => $client): ?>
                    <tr>
                        <td>
                            <div class="flex items-center gap-1">
                                <form method="POST" action="<?= e(url('/dashbord/app/clients.php')) ?>">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="client_id" value="<?= (int) $client['id'] ?>">
                                    <input type="hidden" name="action" value="reorder">
                                    <input type="hidden" name="direction" value="up">
                                    <button type="submit" class="admin-btn" <?= $i === 0 ? 'disabled' : '' ?>>▲</button>
                                </form>
                                <form method="POST" action="<?= e(url('/dashbord/app/clients.php')) ?>">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="client_id" value="<?= (int) $client['id'] ?>">
                                    <input type="hidden" name="action" value="reorder">
                                    <input type="hidden" name="direction" value="down">
                                    <button type="submit" class="admin-btn" <?= $i === count($clients) - 1 ? 'disabled' : '' ?>>▼</button>
                                </form>
                            </div>
                        </td>
                        <td>
                            <a href="<?= e(url('/dashbord/app/client-form.php?id=' . $client['id'])) ?>" class="font-bold hover:underline"><?= e($client['name']) ?></a>
                        </td>
                        <td style="color: var(--a-muted)"><?= e($client['industry'] ?: '—') ?></td>
                        <td>
                            <form method="POST" action="<?= e(url('/dashbord/app/clients.php')) ?>">
                                <?= csrf_field() ?>
                                <input type="hidden" name="client_id" value="<?= (int) $client['id'] ?>">
                                <input type="hidden" name="action" value="toggle_published">
                                <button type="submit" class="admin-badge" style="<?= $client['published'] ? 'border-color: var(--a-success); color: var(--a-success)' : '' ?>">
                                    <?= $client['published'] ? 'منتشرشده' : 'پیش‌نویس' ?>
                                </button>
                            </form>
                        </td>
                        <td>
                            <form method="POST" action="<?= e(url('/dashbord/app/clients.php')) ?>">
                                <?= csrf_field() ?>
                                <input type="hidden" name="client_id" value="<?= (int) $client['id'] ?>">
                                <input type="hidden" name="action" value="toggle_featured">
                                <button type="submit" class="admin-badge" style="<?= $client['featured'] ? 'border-color: var(--a-primary); color: var(--a-primary)' : '' ?>">
                                    <?= $client['featured'] ? 'ویژه' : '—' ?>
                                </button>
                            </form>
                        </td>
                        <td>
                            <div class="flex gap-2">
                                <a href="<?= e(url('/dashbord/app/client-form.php?id=' . $client['id'])) ?>" class="admin-btn">ویرایش</a>
                                <form method="POST" action="<?= e(url('/dashbord/app/clients.php')) ?>" onsubmit="return confirm('این کارفرما و تمام نمونه‌کارهای آن حذف شود؟');">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="client_id" value="<?= (int) $client['id'] ?>">
                                    <input type="hidden" name="action" value="delete">
                                    <button type="submit" class="admin-btn admin-btn-danger">حذف</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>

<?php
$content = ob_get_clean();
$pageTitle = 'کارفرمایان';
$activeNav = 'clients';
require_once __DIR__ . '/../../partials/admin-layout.php';
