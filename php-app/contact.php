<?php
require_once __DIR__ . '/lib/bootstrap.php';

$settings = settings();
$errors = [];
$old = ['name' => '', 'email' => '', 'phone' => '', 'subject' => '', 'message' => ''];

if (is_post()) {
    csrf_verify_or_die();

    $old['name'] = input('name');
    $old['email'] = input('email');
    $old['phone'] = input('phone');
    $old['subject'] = input('subject');
    $old['message'] = input('message');

    if ($old['name'] === '') {
        $errors['name'] = 'نام و نام‌خانوادگی را وارد کنید.';
    }
    if ($old['email'] === '') {
        $errors['email'] = 'ایمیل را وارد کنید.';
    } elseif (! filter_var($old['email'], FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'ایمیل معتبر نیست.';
    }
    if ($old['message'] === '') {
        $errors['message'] = 'متن پیام را وارد کنید.';
    }

    if (! $errors) {
        db_run(
            'INSERT INTO messages (name, email, phone, subject, message, is_read, created_at) VALUES (?, ?, ?, ?, ?, 0, ?)',
            [$old['name'], $old['email'], $old['phone'], $old['subject'], $old['message'], date('Y-m-d H:i:s')]
        );
        flash_set('contact_success', true);
        redirect('/contact.php');
    }
}

ob_start();
?>

<section class="container-px pt-6 pb-24">
    <span class="eyebrow">تماس با ما</span>
    <h1 class="h-hero font-display mt-6 max-w-3xl">حرف بزنیم؛ ایده‌هامون رو کنار هم بذاریم.</h1>

    <div class="grid md:grid-cols-[1.1fr_0.9fr] gap-12 mt-16">
        <div class="client-card p-8 md:p-10 reveal-up">
            <?php if (flash_get('contact_success')): ?>
                <div class="rounded-2xl p-5 mb-8 font-display font-bold" style="background: var(--color-accent); border:2.5px solid var(--color-fg)">
                    پیام شما با موفقیت ارسال شد؛ به‌زودی با شما تماس می‌گیریم. ✦
                </div>
            <?php endif; ?>

            <form method="POST" action="<?= e(url('/contact.php')) ?>" class="flex flex-col gap-5">
                <?= csrf_field() ?>
                <div class="grid sm:grid-cols-2 gap-5">
                    <div>
                        <input type="text" name="name" value="<?= e($old['name']) ?>" placeholder="نام و نام‌خانوادگی" class="input-field">
                        <?php if (! empty($errors['name'])): ?><span class="text-xs mt-2 block" style="color:var(--color-primary)"><?= e($errors['name']) ?></span><?php endif; ?>
                    </div>
                    <div>
                        <input type="email" name="email" value="<?= e($old['email']) ?>" placeholder="ایمیل" dir="ltr" class="input-field">
                        <?php if (! empty($errors['email'])): ?><span class="text-xs mt-2 block" style="color:var(--color-primary)"><?= e($errors['email']) ?></span><?php endif; ?>
                    </div>
                </div>
                <div class="grid sm:grid-cols-2 gap-5">
                    <input type="text" name="phone" value="<?= e($old['phone']) ?>" placeholder="شماره تماس (اختیاری)" dir="ltr" class="input-field">
                    <input type="text" name="subject" value="<?= e($old['subject']) ?>" placeholder="موضوع (اختیاری)" class="input-field">
                </div>
                <div>
                    <textarea name="message" rows="6" placeholder="پیام شما" class="input-field"><?= e($old['message']) ?></textarea>
                    <?php if (! empty($errors['message'])): ?><span class="text-xs mt-2 block" style="color:var(--color-primary)"><?= e($errors['message']) ?></span><?php endif; ?>
                </div>
                <button type="submit" class="btn-pill btn-solid self-start">ارسال پیام ↗</button>
            </form>
        </div>

        <div class="flex flex-col gap-6">
            <div class="client-card p-8 reveal-up">
                <span class="eyebrow">آدرس</span>
                <p class="mt-4 text-lg font-display font-bold"><?= e($settings['contact_address']) ?></p>
            </div>
            <div class="client-card p-8 reveal-up">
                <span class="eyebrow">تلفن</span>
                <p class="mt-4 text-lg font-display font-bold" dir="ltr"><?= e($settings['contact_phone']) ?></p>
            </div>
            <div class="client-card p-8 reveal-up">
                <span class="eyebrow">ایمیل</span>
                <p class="mt-4 text-lg font-display font-bold" dir="ltr"><?= e($settings['contact_email']) ?></p>
            </div>
            <?php if ($settings['contact_map_embed']): ?>
                <div class="rounded-[28px] overflow-hidden frame-pop reveal-up">
                    <?= $settings['contact_map_embed'] ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php
$content = ob_get_clean();
$pageTitle = 'تماس با ما | ' . $settings['site_name'];
require_once __DIR__ . '/partials/site-layout.php';
