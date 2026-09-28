<?php
require_once __DIR__ . '/../../lib/bootstrap.php';
require_admin();

$clientId = (int) input('id');
$client = $clientId ? db_one('SELECT * FROM clients WHERE id = ?', [$clientId]) : null;
$isEdit = $client !== null;

if ($clientId && ! $isEdit) {
    flash_set('error', 'کارفرما پیدا نشد.');
    redirect('/dashbord/app/clients.php');
}

$tab = input('tab', 'info');
if (! in_array($tab, ['info', 'portfolio'], true)) {
    $tab = 'info';
}

function redirect_to_client(int $id, string $tab = 'info'): void
{
    redirect('/dashbord/app/client-form.php?id=' . $id . '&tab=' . $tab);
}

if (is_post()) {
    csrf_verify_or_die();
    $action = input('action');

    // ---------------------------------------------------------------
    // ذخیره‌ی اطلاعات کلی کارفرما (ایجاد یا ویرایش)
    // ---------------------------------------------------------------
    if ($action === 'save_client') {
        $name = input('name');

        if ($name === '') {
            flash_set('error', 'نام کارفرما را وارد کنید.');
            redirect($isEdit ? ('/dashbord/app/client-form.php?id=' . $clientId . '&tab=info') : '/dashbord/app/client-form.php');
        }

        $industry = input('industry');
        $shortDescription = input('short_description');
        $websiteUrl = input('website_url');
        $year = input('year');
        $accentColor = input('accent_color');
        $now = date('Y-m-d H:i:s');

        [$logoPath, $logoErr] = handle_upload($_FILES['logo'] ?? [], 'images', ALLOWED_IMAGE_EXTENSIONS);
        [$coverPath, $coverErr] = handle_upload($_FILES['cover'] ?? [], 'images', ALLOWED_IMAGE_EXTENSIONS);

        if ($logoErr || $coverErr) {
            flash_set('error', $logoErr ?: $coverErr);
            redirect($isEdit ? ('/dashbord/app/client-form.php?id=' . $clientId . '&tab=info') : '/dashbord/app/client-form.php');
        }

        if (! $isEdit) {
            $slug = unique_slug(make_slug($name), fn ($s) => db_value('SELECT COUNT(*) FROM clients WHERE slug = ?', [$s]) > 0);
            $maxOrder = (int) (db_value('SELECT MAX(order_index) FROM clients') ?? 0);

            db_run(
                'INSERT INTO clients (slug, name, industry, short_description, website_url, year, accent_color, logo_url, cover_image_url, featured, published, order_index, created_at, updated_at)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 0, 1, ?, ?, ?)',
                [$slug, $name, $industry, $shortDescription, $websiteUrl, $year, $accentColor, $logoPath ?? '', $coverPath ?? '', $maxOrder + 1, $now, $now]
            );
            $newId = db_insert_id();
            flash_set('status', 'کارفرما با موفقیت ایجاد شد.');
            redirect_to_client($newId, 'info');
        }

        $slugInput = input('slug');
        $slug = make_slug($slugInput !== '' ? $slugInput : $name);
        if ($slug !== $client['slug']) {
            $slug = unique_slug($slug, fn ($s) => db_value('SELECT COUNT(*) FROM clients WHERE slug = ? AND id != ?', [$s, $clientId]) > 0);
        }

        if ($logoPath) {
            delete_public_path($client['logo_url']);
        } elseif (input_bool('remove_logo')) {
            delete_public_path($client['logo_url']);
            $logoPath = '';
        }

        if ($coverPath) {
            delete_public_path($client['cover_image_url']);
        } elseif (input_bool('remove_cover')) {
            delete_public_path($client['cover_image_url']);
            $coverPath = '';
        }

        db_run(
            'UPDATE clients SET slug = ?, name = ?, industry = ?, short_description = ?, website_url = ?, year = ?, accent_color = ?,
                published = ?, featured = ?
                ' . ($logoPath !== null ? ', logo_url = ?' : '') . ($coverPath !== null ? ', cover_image_url = ?' : '') . ',
                updated_at = ?
             WHERE id = ?',
            array_merge(
                [$slug, $name, $industry, $shortDescription, $websiteUrl, $year, $accentColor, input_bool('published') ? 1 : 0, input_bool('featured') ? 1 : 0],
                $logoPath !== null ? [$logoPath] : [],
                $coverPath !== null ? [$coverPath] : [],
                [$now, $clientId]
            )
        );

        flash_set('status', 'تغییرات ذخیره شد.');
        redirect_to_client($clientId, 'info');
    }

    // ---------------------------------------------------------------
    // حذف کارفرما
    // ---------------------------------------------------------------
    if ($action === 'delete_client' && $isEdit) {
        $items = db_all(
            'SELECT pi.media_url FROM portfolio_items pi JOIN categories c ON c.id = pi.category_id WHERE c.client_id = ?',
            [$clientId]
        );
        foreach ($items as $item) {
            delete_public_path($item['media_url']);
        }
        delete_public_path($client['logo_url']);
        delete_public_path($client['cover_image_url']);
        db_run('DELETE FROM clients WHERE id = ?', [$clientId]);

        flash_set('status', 'کارفرما حذف شد.');
        redirect('/dashbord/app/clients.php');
    }

    // ---------------------------------------------------------------
    // دسته‌بندی‌ها
    // ---------------------------------------------------------------
    if ($action === 'save_category' && $isEdit) {
        $title = input('title');
        $aspect = input('aspect_ratio');
        if (! in_array($aspect, ['16:9', '9:16', '1:1'], true)) {
            $aspect = '16:9';
        }
        $categoryId = (int) input('category_id');
        $now = date('Y-m-d H:i:s');

        [$coverPath, $coverErr] = handle_upload($_FILES['cover'] ?? [], 'images', ALLOWED_IMAGE_EXTENSIONS);
        if ($coverErr) {
            flash_set('error', $coverErr);
            redirect_to_client($clientId, 'portfolio');
        }

        if ($categoryId) {
            $category = db_one('SELECT * FROM categories WHERE id = ? AND client_id = ?', [$categoryId, $clientId]);
            if ($category) {
                if ($coverPath) {
                    delete_public_path($category['cover_image_url']);
                } elseif (input_bool('remove_cover')) {
                    delete_public_path($category['cover_image_url']);
                    $coverPath = '';
                }
                db_run(
                    'UPDATE categories SET title = ?, description = ?, aspect_ratio = ?' . ($coverPath !== null ? ', cover_image_url = ?' : '') . ' WHERE id = ?',
                    array_merge([$title, input('description'), $aspect], $coverPath !== null ? [$coverPath] : [], [$categoryId])
                );
            }
        } elseif ($title !== '') {
            $baseSlug = make_slug($title);
            $slug = unique_slug($baseSlug, fn ($s) => db_value('SELECT COUNT(*) FROM categories WHERE client_id = ? AND slug = ?', [$clientId, $s]) > 0);
            $maxOrder = (int) (db_value('SELECT MAX(order_index) FROM categories WHERE client_id = ?', [$clientId]) ?? -1);

            db_run(
                'INSERT INTO categories (client_id, title, slug, description, cover_image_url, aspect_ratio, order_index, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?)',
                [$clientId, $title, $slug, '', '', $aspect, $maxOrder + 1, $now]
            );
        }

        flash_set('status', 'دسته‌بندی ذخیره شد.');
        redirect_to_client($clientId, 'portfolio');
    }

    if ($action === 'delete_category' && $isEdit) {
        $categoryId = (int) input('category_id');
        $category = db_one('SELECT * FROM categories WHERE id = ? AND client_id = ?', [$categoryId, $clientId]);
        if ($category) {
            $items = db_all('SELECT media_url FROM portfolio_items WHERE category_id = ?', [$categoryId]);
            foreach ($items as $item) {
                delete_public_path($item['media_url']);
            }
            delete_public_path($category['cover_image_url']);
            db_run('DELETE FROM categories WHERE id = ?', [$categoryId]);
            flash_set('status', 'دسته‌بندی حذف شد.');
        }
        redirect_to_client($clientId, 'portfolio');
    }

    // ---------------------------------------------------------------
    // نمونه‌کارها
    // ---------------------------------------------------------------
    if ($action === 'save_item' && $isEdit) {
        $categoryId = (int) input('category_id');
        $category = db_one('SELECT * FROM categories WHERE id = ? AND client_id = ?', [$categoryId, $clientId]);

        if (! $category) {
            flash_set('error', 'دسته‌بندی نامعتبر است.');
            redirect_to_client($clientId, 'portfolio');
        }

        $mediaType = input('media_type') === 'video' ? 'video' : 'image';
        $allowed = $mediaType === 'video' ? ALLOWED_VIDEO_EXTENSIONS : ALLOWED_IMAGE_EXTENSIONS;

        [$mediaPath, $mediaErr] = handle_upload($_FILES['media'] ?? [], 'images', $allowed);
        if ($mediaErr) {
            flash_set('error', $mediaErr);
            redirect_to_client($clientId, 'portfolio');
        }
        if (! $mediaPath) {
            flash_set('error', 'لطفاً یک فایل انتخاب کنید.');
            redirect_to_client($clientId, 'portfolio');
        }

        $maxOrder = (int) (db_value('SELECT MAX(order_index) FROM portfolio_items WHERE category_id = ?', [$categoryId]) ?? -1);
        $now = date('Y-m-d H:i:s');

        db_run(
            'INSERT INTO portfolio_items (category_id, title, description, media_url, media_type, order_index, featured_home, created_at) VALUES (?, ?, ?, ?, ?, ?, 0, ?)',
            [$categoryId, input('title'), input('description'), $mediaPath, $mediaType, $maxOrder + 1, $now]
        );

        flash_set('status', 'نمونه‌کار اضافه شد.');
        redirect_to_client($clientId, 'portfolio');
    }

    if ($action === 'toggle_item_featured' && $isEdit) {
        $itemId = (int) input('item_id');
        db_run(
            'UPDATE portfolio_items SET featured_home = 1 - featured_home
             WHERE id = ? AND category_id IN (SELECT id FROM categories WHERE client_id = ?)',
            [$itemId, $clientId]
        );
        redirect_to_client($clientId, 'portfolio');
    }

    if ($action === 'delete_item' && $isEdit) {
        $itemId = (int) input('item_id');
        $item = db_one(
            'SELECT pi.* FROM portfolio_items pi JOIN categories c ON c.id = pi.category_id WHERE pi.id = ? AND c.client_id = ?',
            [$itemId, $clientId]
        );
        if ($item) {
            delete_public_path($item['media_url']);
            db_run('DELETE FROM portfolio_items WHERE id = ?', [$itemId]);
        }
        redirect_to_client($clientId, 'portfolio');
    }

    redirect_to_client($clientId ?: 0, $tab);
}

$categories = [];
if ($isEdit) {
    $categories = db_all('SELECT * FROM categories WHERE client_id = ? ORDER BY order_index ASC', [$clientId]);
    foreach ($categories as &$cat) {
        $cat['items'] = db_all('SELECT * FROM portfolio_items WHERE category_id = ? ORDER BY order_index ASC', [$cat['id']]);
    }
    unset($cat);
}

ob_start();
?>

<?php if ($isEdit): ?>
    <div class="flex flex-wrap items-center justify-between gap-3 mb-8">
        <div class="flex gap-2">
            <a href="<?= e(url('/dashbord/app/client-form.php?id=' . $clientId . '&tab=info')) ?>" class="admin-btn <?= $tab === 'info' ? 'admin-btn-active' : '' ?>">اطلاعات کلی</a>
            <a href="<?= e(url('/dashbord/app/client-form.php?id=' . $clientId . '&tab=portfolio')) ?>" class="admin-btn <?= $tab === 'portfolio' ? 'admin-btn-active' : '' ?>">نمونه‌کارها</a>
        </div>
        <a href="<?= e(url('/client.php?slug=' . rawurlencode($client['slug']))) ?>" target="_blank" class="admin-btn">مشاهده در سایت ↗</a>
    </div>
<?php endif; ?>

<?php if (! $isEdit || $tab === 'info'): ?>
    <form method="POST" action="<?= e(url('/dashbord/app/client-form.php' . ($isEdit ? ('?id=' . $clientId) : ''))) ?>" enctype="multipart/form-data" class="admin-card p-6 flex flex-col gap-5 max-w-2xl">
        <?= csrf_field() ?>
        <input type="hidden" name="action" value="save_client">
        <?php if ($isEdit): ?><input type="hidden" name="id" value="<?= $clientId ?>"><?php endif; ?>

        <div class="grid sm:grid-cols-2 gap-5">
            <div>
                <label class="admin-label">نام کارفرما</label>
                <input type="text" name="name" value="<?= e($client['name'] ?? '') ?>" class="admin-input" autofocus>
            </div>
            <?php if ($isEdit): ?>
                <div>
                    <label class="admin-label">نامک (slug)</label>
                    <input type="text" name="slug" value="<?= e($client['slug']) ?>" class="admin-input" dir="ltr">
                </div>
            <?php endif; ?>
        </div>

        <div class="grid sm:grid-cols-2 gap-5">
            <div>
                <label class="admin-label">حوزه فعالیت</label>
                <input type="text" name="industry" value="<?= e($client['industry'] ?? '') ?>" class="admin-input">
            </div>
            <div>
                <label class="admin-label">سال همکاری</label>
                <input type="text" name="year" value="<?= e($client['year'] ?? '') ?>" class="admin-input">
            </div>
        </div>

        <div>
            <label class="admin-label">توضیح کوتاه</label>
            <textarea name="short_description" rows="3" class="admin-input"><?= e($client['short_description'] ?? '') ?></textarea>
        </div>

        <div class="grid sm:grid-cols-2 gap-5">
            <div>
                <label class="admin-label">آدرس وب‌سایت</label>
                <input type="text" name="website_url" value="<?= e($client['website_url'] ?? '') ?>" class="admin-input" dir="ltr">
            </div>
            <div>
                <label class="admin-label">رنگ اختصاصی (اختیاری)</label>
                <input type="color" name="accent_color" value="<?= e($client['accent_color'] ?? '#ff3d74') ?: '#ff3d74' ?>" class="h-10 w-16 rounded border-0 bg-transparent">
            </div>
        </div>

        <div class="grid sm:grid-cols-2 gap-5">
            <div>
                <label class="admin-label">لوگو</label>
                <?php if (! empty($client['logo_url'])): ?>
                    <div class="mb-2 flex items-center gap-3">
                        <img src="<?= e($client['logo_url']) ?>" class="h-10" alt="">
                        <label class="text-xs flex items-center gap-1" style="color: var(--a-danger)"><input type="checkbox" name="remove_logo" value="1"> حذف</label>
                    </div>
                <?php endif; ?>
                <input type="file" name="logo" accept="image/*" class="admin-input">
            </div>
            <div>
                <label class="admin-label">تصویر کاور</label>
                <?php if (! empty($client['cover_image_url'])): ?>
                    <div class="mb-2 flex items-center gap-3">
                        <img src="<?= e($client['cover_image_url']) ?>" class="h-10" alt="">
                        <label class="text-xs flex items-center gap-1" style="color: var(--a-danger)"><input type="checkbox" name="remove_cover" value="1"> حذف</label>
                    </div>
                <?php endif; ?>
                <input type="file" name="cover" accept="image/*" class="admin-input">
            </div>
        </div>

        <?php if ($isEdit): ?>
            <div class="flex gap-6">
                <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="published" value="1" <?= $client['published'] ? 'checked' : '' ?>> منتشر شده</label>
                <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="featured" value="1" <?= $client['featured'] ? 'checked' : '' ?>> کارفرمای ویژه</label>
            </div>
        <?php endif; ?>

        <div class="flex items-center justify-between mt-2">
            <button type="submit" class="admin-btn admin-btn-primary"><?= $isEdit ? 'ذخیره تغییرات' : 'ایجاد و ادامه ویرایش' ?></button>
            <?php if ($isEdit): ?>
                <form method="POST" action="<?= e(url('/dashbord/app/client-form.php?id=' . $clientId)) ?>" onsubmit="return confirm('این کارفرما و تمام نمونه‌کارهای آن حذف شود؟');">
                    <?= csrf_field() ?>
                    <input type="hidden" name="action" value="delete_client">
                    <button type="submit" class="admin-btn admin-btn-danger">حذف این کارفرما</button>
                </form>
            <?php endif; ?>
        </div>
    </form>
<?php endif; ?>

<?php if ($isEdit && $tab === 'portfolio'): ?>
    <div class="flex flex-col gap-8">
        <?php foreach ($categories as $category): $ratio = normalize_aspect($category['aspect_ratio']); ?>
            <div class="admin-card p-6">
                <div class="flex flex-wrap items-start justify-between gap-4 mb-5">
                    <form method="POST" action="<?= e(url('/dashbord/app/client-form.php?id=' . $clientId)) ?>" enctype="multipart/form-data" class="grid sm:grid-cols-2 gap-4 flex-1 min-w-[260px]">
                        <?= csrf_field() ?>
                        <input type="hidden" name="action" value="save_category">
                        <input type="hidden" name="category_id" value="<?= (int) $category['id'] ?>">
                        <div>
                            <label class="admin-label">عنوان دسته‌بندی</label>
                            <input type="text" name="title" value="<?= e($category['title']) ?>" class="admin-input">
                        </div>
                        <div>
                            <label class="admin-label">نسبت تصویر</label>
                            <select name="aspect_ratio" class="admin-input">
                                <?php foreach (ASPECT_OPTIONS as $value => $label): ?>
                                    <option value="<?= e($value) ?>" <?= $ratio === $value ? 'selected' : '' ?>><?= e($label) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="sm:col-span-2">
                            <label class="admin-label">توضیح</label>
                            <input type="text" name="description" value="<?= e($category['description']) ?>" class="admin-input">
                        </div>
                        <div>
                            <label class="admin-label">تصویر کاور دسته‌بندی</label>
                            <?php if ($category['cover_image_url']): ?>
                                <div class="mb-2 flex items-center gap-2">
                                    <img src="<?= e($category['cover_image_url']) ?>" class="h-10 <?= e(ASPECT_TILE_CLASS[$ratio]) ?> object-cover rounded">
                                    <label class="text-xs flex items-center gap-1" style="color: var(--a-danger)"><input type="checkbox" name="remove_cover" value="1"> حذف</label>
                                </div>
                            <?php endif; ?>
                            <input type="file" name="cover" accept="image/*" class="admin-input">
                        </div>
                        <div class="flex items-end">
                            <button type="submit" class="admin-btn admin-btn-primary w-full">ذخیره دسته‌بندی</button>
                        </div>
                    </form>

                    <form method="POST" action="<?= e(url('/dashbord/app/client-form.php?id=' . $clientId)) ?>" onsubmit="return confirm('این دسته‌بندی و تمام نمونه‌کارهای آن حذف شود؟');">
                        <?= csrf_field() ?>
                        <input type="hidden" name="action" value="delete_category">
                        <input type="hidden" name="category_id" value="<?= (int) $category['id'] ?>">
                        <button type="submit" class="admin-btn admin-btn-danger">حذف دسته‌بندی</button>
                    </form>
                </div>

                <div class="hairline mb-5" style="border-top: 1px dashed var(--a-border)"></div>

                <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-6 gap-3 mb-5">
                    <?php foreach ($category['items'] as $item): ?>
                        <div class="relative rounded-lg overflow-hidden border" style="border-color: var(--a-border)">
                            <?php if ($item['media_type'] === 'video'): ?>
                                <video src="<?= e($item['media_url']) ?>" class="w-full <?= e(ASPECT_TILE_CLASS[$ratio]) ?> object-cover" muted></video>
                            <?php else: ?>
                                <img src="<?= e($item['media_url']) ?>" class="w-full <?= e(ASPECT_TILE_CLASS[$ratio]) ?> object-cover" alt="">
                            <?php endif; ?>
                            <div class="absolute inset-x-0 bottom-0 flex items-center justify-between gap-1 p-1.5" style="background: rgba(0,0,0,.6)">
                                <form method="POST" action="<?= e(url('/dashbord/app/client-form.php?id=' . $clientId)) ?>">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="action" value="toggle_item_featured">
                                    <input type="hidden" name="item_id" value="<?= (int) $item['id'] ?>">
                                    <button type="submit" class="text-[10px] px-1.5 py-0.5 rounded" style="background: <?= $item['featured_home'] ? 'var(--a-primary)' : 'transparent' ?>; color:#fff; border:1px solid #fff">ویژه</button>
                                </form>
                                <form method="POST" action="<?= e(url('/dashbord/app/client-form.php?id=' . $clientId)) ?>" onsubmit="return confirm('این نمونه‌کار حذف شود؟');">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="action" value="delete_item">
                                    <input type="hidden" name="item_id" value="<?= (int) $item['id'] ?>">
                                    <button type="submit" class="text-[10px] px-1.5 py-0.5 rounded" style="color:#fff; border:1px solid var(--a-danger); background: var(--a-danger)">حذف</button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <form method="POST" action="<?= e(url('/dashbord/app/client-form.php?id=' . $clientId)) ?>" enctype="multipart/form-data" class="grid sm:grid-cols-5 gap-3 items-end">
                    <?= csrf_field() ?>
                    <input type="hidden" name="action" value="save_item">
                    <input type="hidden" name="category_id" value="<?= (int) $category['id'] ?>">
                    <div class="sm:col-span-1">
                        <label class="admin-label">نوع</label>
                        <select name="media_type" class="admin-input">
                            <option value="image">تصویر</option>
                            <option value="video">ویدئو</option>
                        </select>
                    </div>
                    <div class="sm:col-span-2">
                        <label class="admin-label">فایل (<?= e(ASPECT_OPTIONS[$ratio]) ?>)</label>
                        <input type="file" name="media" accept="image/*,video/mp4,video/webm" class="admin-input">
                    </div>
                    <div class="sm:col-span-1">
                        <label class="admin-label">عنوان (اختیاری)</label>
                        <input type="text" name="title" class="admin-input">
                    </div>
                    <div class="sm:col-span-1">
                        <button type="submit" class="admin-btn admin-btn-primary w-full">افزودن</button>
                    </div>
                </form>
            </div>
        <?php endforeach; ?>

        <div class="admin-card p-6">
            <h2 class="font-display font-bold mb-4">افزودن دسته‌بندی جدید</h2>
            <form method="POST" action="<?= e(url('/dashbord/app/client-form.php?id=' . $clientId)) ?>" class="grid sm:grid-cols-4 gap-4 items-end">
                <?= csrf_field() ?>
                <input type="hidden" name="action" value="save_category">
                <input type="hidden" name="category_id" value="0">
                <div class="sm:col-span-2">
                    <label class="admin-label">عنوان دسته‌بندی</label>
                    <input type="text" name="title" class="admin-input" placeholder="مثلاً: کمپین تبلیغاتی">
                </div>
                <div>
                    <label class="admin-label">نسبت تصویر</label>
                    <select name="aspect_ratio" class="admin-input">
                        <?php foreach (ASPECT_OPTIONS as $value => $label): ?>
                            <option value="<?= e($value) ?>"><?= e($label) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button type="submit" class="admin-btn admin-btn-primary">افزودن دسته‌بندی</button>
            </form>
        </div>
    </div>
<?php endif; ?>

<?php
$content = ob_get_clean();
$pageTitle = $isEdit ? ('ویرایش: ' . $client['name']) : 'افزودن کارفرمای جدید';
$activeNav = 'clients';
require_once __DIR__ . '/../../partials/admin-layout.php';
