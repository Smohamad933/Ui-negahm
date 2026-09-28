<?php
require_once __DIR__ . '/../../lib/bootstrap.php';
$admin = require_admin();

$tabs = [
    'general' => 'عمومی',
    'theme' => 'رنگ و فونت',
    'content' => 'محتوای صفحات',
    'security' => 'رمز عبور',
];

if (is_post()) {
    csrf_verify_or_die();
    $action = input('action');
    $now = date('Y-m-d H:i:s');

    if ($action === 'update_general') {
        $settingsRow = settings();
        [$logoPath, $logoErr] = handle_upload($_FILES['logo'] ?? [], 'images', ALLOWED_IMAGE_EXTENSIONS);
        [$faviconPath, $faviconErr] = handle_upload($_FILES['favicon'] ?? [], 'images', ALLOWED_IMAGE_EXTENSIONS);

        if ($logoErr || $faviconErr) {
            flash_set('error', $logoErr ?: $faviconErr);
            redirect('/dashbord/app/settings.php?section=general');
        }

        if ($logoPath) {
            delete_public_path($settingsRow['logo_url']);
        } elseif (input_bool('remove_logo')) {
            delete_public_path($settingsRow['logo_url']);
            $logoPath = '';
        }
        if ($faviconPath) {
            delete_public_path($settingsRow['favicon_url']);
        } elseif (input_bool('remove_favicon')) {
            delete_public_path($settingsRow['favicon_url']);
            $faviconPath = '';
        }

        db_run(
            'UPDATE settings SET site_name = ?, tagline = ?' . ($logoPath !== null ? ', logo_url = ?' : '') . ($faviconPath !== null ? ', favicon_url = ?' : '') . ', updated_at = ? WHERE id = 1',
            array_merge(
                [input('site_name'), input('tagline')],
                $logoPath !== null ? [$logoPath] : [],
                $faviconPath !== null ? [$faviconPath] : [],
                [$now]
            )
        );

        flash_set('status', 'تنظیمات ذخیره شد.');
        redirect('/dashbord/app/settings.php?section=general');
    }

    if ($action === 'update_theme') {
        db_run(
            'UPDATE settings SET color_bg = ?, color_fg = ?, color_primary = ?, color_secondary = ?, color_accent = ?, color_muted = ?, font_family = ?, updated_at = ? WHERE id = 1',
            [input('color_bg'), input('color_fg'), input('color_primary'), input('color_secondary'), input('color_accent'), input('color_muted'), input('font_family'), $now]
        );
        flash_set('status', 'تنظیمات ذخیره شد.');
        redirect('/dashbord/app/settings.php?section=theme');
    }

    if ($action === 'upload_font') {
        $familyName = input('family_name');
        $weight = input('weight', '400');
        $style = input('style') === 'italic' ? 'italic' : 'normal';

        if ($familyName === '') {
            flash_set('error', 'ابتدا نام فونت را وارد کنید.');
            redirect('/dashbord/app/settings.php?section=theme');
        }

        $file = $_FILES['file'] ?? [];
        $originalName = $file['name'] ?? '';
        $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

        [$fontPath, $fontErr] = handle_upload($file, 'fonts', ALLOWED_FONT_EXTENSIONS);
        if ($fontErr) {
            flash_set('error', $fontErr);
            redirect('/dashbord/app/settings.php?section=theme');
        }
        if (! $fontPath) {
            flash_set('error', 'لطفاً یک فایل فونت انتخاب کنید.');
            redirect('/dashbord/app/settings.php?section=theme');
        }

        db_run(
            'INSERT INTO fonts (family_name, weight, style, format, file_url, created_at) VALUES (?, ?, ?, ?, ?, ?)',
            [$familyName, $weight, $style, $extension, $fontPath, $now]
        );

        flash_set('status', 'فونت با موفقیت آپلود شد.');
        redirect('/dashbord/app/settings.php?section=theme');
    }

    if ($action === 'delete_font') {
        $fontId = (int) input('font_id');
        $font = db_one('SELECT * FROM fonts WHERE id = ?', [$fontId]);
        if ($font) {
            delete_public_path($font['file_url']);
            db_run('DELETE FROM fonts WHERE id = ?', [$fontId]);
            flash_set('status', 'فونت حذف شد.');
        }
        redirect('/dashbord/app/settings.php?section=theme');
    }

    if ($action === 'update_content') {
        $settingsRow = settings();
        [$aboutImagePath, $aboutImageErr] = handle_upload($_FILES['about_image'] ?? [], 'images', ALLOWED_IMAGE_EXTENSIONS);

        if ($aboutImageErr) {
            flash_set('error', $aboutImageErr);
            redirect('/dashbord/app/settings.php?section=content');
        }

        if ($aboutImagePath) {
            delete_public_path($settingsRow['about_image_url']);
        } elseif (input_bool('remove_about_image')) {
            delete_public_path($settingsRow['about_image_url']);
            $aboutImagePath = '';
        }

        db_run(
            'UPDATE settings SET hero_title = ?, hero_subtitle = ?, hero_cta_text = ?, hero_cta_link = ?, about_title = ?, about_body = ?'
                . ($aboutImagePath !== null ? ', about_image_url = ?' : '') . ',
             contact_address = ?, contact_phone = ?, contact_email = ?, contact_map_embed = ?,
             social_instagram = ?, social_telegram = ?, social_whatsapp = ?, social_linkedin = ?, footer_text = ?,
             stat1_value = ?, stat1_label = ?, stat2_value = ?, stat2_label = ?, stat3_value = ?, stat3_label = ?,
             updated_at = ?
             WHERE id = 1',
            array_merge(
                [input('hero_title'), input('hero_subtitle'), input('hero_cta_text'), input('hero_cta_link'), input('about_title'), input('about_body')],
                $aboutImagePath !== null ? [$aboutImagePath] : [],
                [
                    input('contact_address'), input('contact_phone'), input('contact_email'), input('contact_map_embed'),
                    input('social_instagram'), input('social_telegram'), input('social_whatsapp'), input('social_linkedin'), input('footer_text'),
                    input('stat1_value'), input('stat1_label'), input('stat2_value'), input('stat2_label'), input('stat3_value'), input('stat3_label'),
                    $now,
                ]
            )
        );

        flash_set('status', 'تنظیمات ذخیره شد.');
        redirect('/dashbord/app/settings.php?section=content');
    }

    if ($action === 'change_password') {
        $current = input('current_password');
        $new = input('new_password');
        $confirm = input('new_password_confirmation');

        if (! password_verify($current, $admin['password_hash'])) {
            flash_set('error', 'رمز فعلی اشتباه است.');
        } elseif (mb_strlen($new) < 6) {
            flash_set('error', 'رمز جدید باید حداقل ۶ کاراکتر باشد.');
        } elseif ($new !== $confirm) {
            flash_set('error', 'رمز جدید و تکرار آن یکسان نیستند.');
        } else {
            db_run('UPDATE admin_users SET password_hash = ? WHERE id = ?', [password_hash($new, PASSWORD_DEFAULT), $admin['id']]);
            flash_set('status', 'رمز عبور با موفقیت تغییر کرد.');
        }

        redirect('/dashbord/app/settings.php?section=security');
    }

    redirect('/dashbord/app/settings.php');
}

$section = input('section', 'general');
if (! isset($tabs[$section])) {
    $section = 'general';
}

$settingsRow = settings();
$fonts = db_all('SELECT * FROM fonts ORDER BY created_at DESC');
$customFamilies = [];
foreach ($fonts as $f) {
    if (! in_array($f['family_name'], $customFamilies, true)) {
        $customFamilies[] = $f['family_name'];
    }
}

ob_start();
?>

<div class="flex flex-wrap gap-2 mb-8">
    <?php foreach ($tabs as $key => $label): ?>
        <a href="<?= e(url('/dashbord/app/settings.php?section=' . $key)) ?>" class="admin-btn <?= $section === $key ? 'admin-btn-active' : '' ?>"><?= e($label) ?></a>
    <?php endforeach; ?>
</div>

<?php if ($section === 'general'): ?>
    <form method="POST" action="<?= e(url('/dashbord/app/settings.php')) ?>" enctype="multipart/form-data" class="admin-card p-6 flex flex-col gap-5 max-w-2xl">
        <?= csrf_field() ?>
        <input type="hidden" name="action" value="update_general">

        <div>
            <label class="admin-label">نام سایت</label>
            <input type="text" name="site_name" value="<?= e($settingsRow['site_name']) ?>" class="admin-input">
        </div>
        <div>
            <label class="admin-label">شعار / تگ‌لاین</label>
            <input type="text" name="tagline" value="<?= e($settingsRow['tagline']) ?>" class="admin-input">
        </div>

        <div class="grid sm:grid-cols-2 gap-5">
            <div>
                <label class="admin-label">لوگو</label>
                <?php if ($settingsRow['logo_url']): ?>
                    <div class="mb-2 flex items-center gap-3">
                        <img src="<?= e($settingsRow['logo_url']) ?>" class="h-10" alt="لوگو">
                        <label class="text-xs flex items-center gap-1" style="color: var(--a-danger)"><input type="checkbox" name="remove_logo" value="1"> حذف</label>
                    </div>
                <?php endif; ?>
                <input type="file" name="logo" accept="image/*" class="admin-input">
            </div>
            <div>
                <label class="admin-label">فاوآیکون</label>
                <?php if ($settingsRow['favicon_url']): ?>
                    <div class="mb-2 flex items-center gap-3">
                        <img src="<?= e($settingsRow['favicon_url']) ?>" class="h-8" alt="فاوآیکون">
                        <label class="text-xs flex items-center gap-1" style="color: var(--a-danger)"><input type="checkbox" name="remove_favicon" value="1"> حذف</label>
                    </div>
                <?php endif; ?>
                <input type="file" name="favicon" accept="image/*" class="admin-input">
            </div>
        </div>

        <button type="submit" class="admin-btn admin-btn-primary self-start mt-2">ذخیره تغییرات</button>
    </form>
<?php endif; ?>

<?php if ($section === 'theme'): ?>
    <form method="POST" action="<?= e(url('/dashbord/app/settings.php')) ?>" class="admin-card p-6 flex flex-col gap-5 max-w-2xl mb-10">
        <?= csrf_field() ?>
        <input type="hidden" name="action" value="update_theme">

        <div class="grid grid-cols-2 sm:grid-cols-3 gap-5">
            <?php foreach (['color_bg' => 'پس‌زمینه', 'color_fg' => 'متن اصلی', 'color_primary' => 'اصلی', 'color_secondary' => 'ثانویه', 'color_accent' => 'تاکیدی', 'color_muted' => 'خنثی'] as $key => $label): ?>
                <div>
                    <label class="admin-label"><?= e($label) ?></label>
                    <div class="flex items-center gap-2">
                        <input type="color" name="<?= e($key) ?>" value="<?= e($settingsRow[$key]) ?>" class="h-10 w-12 rounded border-0 bg-transparent" oninput="this.nextElementSibling.value=this.value">
                        <input type="text" value="<?= e($settingsRow[$key]) ?>" class="admin-input" dir="ltr" oninput="this.previousElementSibling.value=this.value" onchange="this.previousElementSibling.value=this.value">
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div>
            <label class="admin-label">فونت اصلی سایت</label>
            <select name="font_family" class="admin-input">
                <option value="Vazirmatn Variable" <?= $settingsRow['font_family'] === 'Vazirmatn Variable' ? 'selected' : '' ?>>Vazirmatn (پیش‌فرض)</option>
                <?php foreach ($customFamilies as $family): ?>
                    <option value="<?= e($family) ?>" <?= $settingsRow['font_family'] === $family ? 'selected' : '' ?>><?= e($family) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <button type="submit" class="admin-btn admin-btn-primary self-start mt-2">ذخیره تغییرات</button>
    </form>

    <div class="admin-card p-6 max-w-2xl">
        <h2 class="font-display font-bold mb-4">فونت‌های سفارشی</h2>

        <?php if ($fonts): ?>
            <div class="flex flex-col gap-2 mb-6">
                <?php foreach ($fonts as $font): ?>
                    <div class="flex items-center justify-between gap-3 p-3 rounded-lg" style="background: var(--a-panel-2)">
                        <span class="text-sm"><?= e($font['family_name']) ?> <span style="color: var(--a-muted)">(<?= e($font['weight']) ?>, <?= e($font['style']) ?>, <?= e($font['format']) ?>)</span></span>
                        <form method="POST" action="<?= e(url('/dashbord/app/settings.php')) ?>" onsubmit="return confirm('این فونت حذف شود؟');">
                            <?= csrf_field() ?>
                            <input type="hidden" name="action" value="delete_font">
                            <input type="hidden" name="font_id" value="<?= (int) $font['id'] ?>">
                            <button type="submit" class="admin-btn admin-btn-danger">حذف</button>
                        </form>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="<?= e(url('/dashbord/app/settings.php')) ?>" enctype="multipart/form-data" class="grid sm:grid-cols-2 gap-4">
            <?= csrf_field() ?>
            <input type="hidden" name="action" value="upload_font">
            <div>
                <label class="admin-label">نام خانواده فونت</label>
                <input type="text" name="family_name" class="admin-input" placeholder="مثلاً: Peyda" required>
            </div>
            <div>
                <label class="admin-label">وزن (weight)</label>
                <input type="text" name="weight" value="400" class="admin-input" required>
            </div>
            <div>
                <label class="admin-label">سبک</label>
                <select name="style" class="admin-input">
                    <option value="normal">Normal</option>
                    <option value="italic">Italic</option>
                </select>
            </div>
            <div>
                <label class="admin-label">فایل فونت (woff2, woff, ttf, otf)</label>
                <input type="file" name="file" accept=".woff2,.woff,.ttf,.otf" class="admin-input" required>
            </div>
            <button type="submit" class="admin-btn admin-btn-primary sm:col-span-2">آپلود فونت</button>
        </form>
    </div>
<?php endif; ?>

<?php if ($section === 'content'): ?>
    <form method="POST" action="<?= e(url('/dashbord/app/settings.php')) ?>" enctype="multipart/form-data" class="admin-card p-6 flex flex-col gap-8 max-w-2xl">
        <?= csrf_field() ?>
        <input type="hidden" name="action" value="update_content">

        <div class="flex flex-col gap-4">
            <h2 class="font-display font-bold">صفحه اصلی (هیرو)</h2>
            <div>
                <label class="admin-label">عنوان اصلی</label>
                <textarea name="hero_title" rows="2" class="admin-input"><?= e($settingsRow['hero_title']) ?></textarea>
            </div>
            <div>
                <label class="admin-label">زیرعنوان</label>
                <textarea name="hero_subtitle" rows="3" class="admin-input"><?= e($settingsRow['hero_subtitle']) ?></textarea>
            </div>
            <div class="grid sm:grid-cols-2 gap-4">
                <div>
                    <label class="admin-label">متن دکمه</label>
                    <input type="text" name="hero_cta_text" value="<?= e($settingsRow['hero_cta_text']) ?>" class="admin-input">
                </div>
                <div>
                    <label class="admin-label">لینک دکمه</label>
                    <input type="text" name="hero_cta_link" value="<?= e($settingsRow['hero_cta_link']) ?>" class="admin-input" dir="ltr">
                </div>
            </div>
        </div>

        <div class="flex flex-col gap-4">
            <h2 class="font-display font-bold">درباره ما</h2>
            <div>
                <label class="admin-label">عنوان</label>
                <textarea name="about_title" rows="2" class="admin-input"><?= e($settingsRow['about_title']) ?></textarea>
            </div>
            <div>
                <label class="admin-label">متن</label>
                <textarea name="about_body" rows="5" class="admin-input"><?= e($settingsRow['about_body']) ?></textarea>
            </div>
            <div>
                <label class="admin-label">تصویر درباره ما</label>
                <?php if ($settingsRow['about_image_url']): ?>
                    <div class="mb-2 flex items-center gap-3">
                        <img src="<?= e($settingsRow['about_image_url']) ?>" class="h-16 rounded" alt="">
                        <label class="text-xs flex items-center gap-1" style="color: var(--a-danger)"><input type="checkbox" name="remove_about_image" value="1"> حذف</label>
                    </div>
                <?php endif; ?>
                <input type="file" name="about_image" accept="image/*" class="admin-input">
            </div>
        </div>

        <div class="flex flex-col gap-4">
            <h2 class="font-display font-bold">آمار (روی صفحه اصلی نمایش داده می‌شود)</h2>
            <div class="grid sm:grid-cols-3 gap-4">
                <div class="grid grid-cols-2 gap-2">
                    <input type="text" name="stat1_value" value="<?= e($settingsRow['stat1_value']) ?>" class="admin-input" placeholder="۳+">
                    <input type="text" name="stat1_label" value="<?= e($settingsRow['stat1_label']) ?>" class="admin-input" placeholder="سال تجربه">
                </div>
                <div class="grid grid-cols-2 gap-2">
                    <input type="text" name="stat2_value" value="<?= e($settingsRow['stat2_value']) ?>" class="admin-input" placeholder="۴۰+">
                    <input type="text" name="stat2_label" value="<?= e($settingsRow['stat2_label']) ?>" class="admin-input" placeholder="پروژه اجراشده">
                </div>
                <div class="grid grid-cols-2 gap-2">
                    <input type="text" name="stat3_value" value="<?= e($settingsRow['stat3_value']) ?>" class="admin-input" placeholder="۱۲+">
                    <input type="text" name="stat3_label" value="<?= e($settingsRow['stat3_label']) ?>" class="admin-input" placeholder="برند همراه">
                </div>
            </div>
        </div>

        <div class="flex flex-col gap-4">
            <h2 class="font-display font-bold">اطلاعات تماس</h2>
            <div class="grid sm:grid-cols-2 gap-4">
                <div>
                    <label class="admin-label">آدرس</label>
                    <input type="text" name="contact_address" value="<?= e($settingsRow['contact_address']) ?>" class="admin-input">
                </div>
                <div>
                    <label class="admin-label">تلفن</label>
                    <input type="text" name="contact_phone" value="<?= e($settingsRow['contact_phone']) ?>" class="admin-input" dir="ltr">
                </div>
                <div>
                    <label class="admin-label">ایمیل</label>
                    <input type="text" name="contact_email" value="<?= e($settingsRow['contact_email']) ?>" class="admin-input" dir="ltr">
                </div>
            </div>
            <div>
                <label class="admin-label">کد Embed نقشه (اختیاری)</label>
                <textarea name="contact_map_embed" rows="3" class="admin-input" dir="ltr"><?= e($settingsRow['contact_map_embed']) ?></textarea>
            </div>
        </div>

        <div class="flex flex-col gap-4">
            <h2 class="font-display font-bold">شبکه‌های اجتماعی و فوتر</h2>
            <div class="grid sm:grid-cols-2 gap-4">
                <div><label class="admin-label">اینستاگرام</label><input type="text" name="social_instagram" value="<?= e($settingsRow['social_instagram']) ?>" class="admin-input" dir="ltr"></div>
                <div><label class="admin-label">تلگرام</label><input type="text" name="social_telegram" value="<?= e($settingsRow['social_telegram']) ?>" class="admin-input" dir="ltr"></div>
                <div><label class="admin-label">واتس‌اپ</label><input type="text" name="social_whatsapp" value="<?= e($settingsRow['social_whatsapp']) ?>" class="admin-input" dir="ltr"></div>
                <div><label class="admin-label">لینکدین</label><input type="text" name="social_linkedin" value="<?= e($settingsRow['social_linkedin']) ?>" class="admin-input" dir="ltr"></div>
            </div>
            <div>
                <label class="admin-label">متن فوتر</label>
                <input type="text" name="footer_text" value="<?= e($settingsRow['footer_text']) ?>" class="admin-input">
            </div>
        </div>

        <button type="submit" class="admin-btn admin-btn-primary self-start">ذخیره تغییرات</button>
    </form>
<?php endif; ?>

<?php if ($section === 'security'): ?>
    <form method="POST" action="<?= e(url('/dashbord/app/settings.php')) ?>" class="admin-card p-6 flex flex-col gap-5 max-w-md">
        <?= csrf_field() ?>
        <input type="hidden" name="action" value="change_password">
        <div>
            <label class="admin-label">رمز عبور فعلی</label>
            <input type="password" name="current_password" class="admin-input">
        </div>
        <div>
            <label class="admin-label">رمز عبور جدید</label>
            <input type="password" name="new_password" class="admin-input">
        </div>
        <div>
            <label class="admin-label">تکرار رمز عبور جدید</label>
            <input type="password" name="new_password_confirmation" class="admin-input">
        </div>
        <button type="submit" class="admin-btn admin-btn-primary self-start">تغییر رمز عبور</button>
    </form>
<?php endif; ?>

<?php
$content = ob_get_clean();
$pageTitle = 'تنظیمات سایت';
$activeNav = 'settings';
require_once __DIR__ . '/../../partials/admin-layout.php';
