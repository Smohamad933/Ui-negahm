<?php
/**
 * این اسکریپت فقط توسط توسعه‌دهنده (یا هر کسی که بخواهد دیتابیس را از صفر
 * بسازد) یک‌بار روی خط فرمان اجرا می‌شود:
 *
 *   php install/seed.php
 *
 * نتیجه‌اش فایل data/app.sqlite است که از قبل در پروژه موجود و آماده‌ی
 * استفاده است — کاربر نهایی هیچ‌وقت مجبور به اجرای این فایل نیست.
 */

if (php_sapi_name() !== 'cli') {
    http_response_code(403);
    exit('این اسکریپت فقط از خط فرمان قابل اجراست.');
}

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../lib/db.php';
require_once __DIR__ . '/../lib/slug.php';

$dataDir = dirname(DB_PATH);
if (! is_dir($dataDir)) {
    mkdir($dataDir, 0775, true);
}

if (is_file(DB_PATH)) {
    unlink(DB_PATH);
}

$pdo = db();
$schema = file_get_contents(__DIR__ . '/schema.sql');
$pdo->exec($schema);

$now = date('Y-m-d H:i:s');

// ---------------------------------------------------------------------
// تنظیمات پیش‌فرض
// ---------------------------------------------------------------------
db_run('INSERT INTO settings (id, about_image_url, updated_at) VALUES (1, ?, ?)', ['/seed-demo/about.jpg', $now]);

// ---------------------------------------------------------------------
// کاربر مدیر پیش‌فرض
// ---------------------------------------------------------------------
db_run(
    'INSERT INTO admin_users (username, password_hash, name, created_at) VALUES (?, ?, ?, ?)',
    [DEFAULT_ADMIN_USERNAME, password_hash(DEFAULT_ADMIN_PASSWORD, PASSWORD_DEFAULT), 'مدیر سایت', $now]
);

// ---------------------------------------------------------------------
// ۳۴ کارفرمای واقعی نگاه مدیا (بر پایه‌ی negahm.ir)
// ---------------------------------------------------------------------
const IND_BUSINESS = 'کسب‌وکار و بیزینس';
const IND_GOV = 'دولتی و نهادی';
const IND_CULTURE = 'آموزشی و فرهنگی';
const ACCENTS = ['#ff3d74', '#2f6fff', '#ffc629', '#8c5cff', '#29d6a8', '#ff7a30'];

function clients_data(): array
{
    return [
        // ---------------- کسب‌وکار و بیزینس ----------------
        [
            'name' => 'گالری طلا و جواهر محمود', 'industry' => IND_BUSINESS,
            'desc' => 'خرده‌فروشی و فروش آنلاین طلا و جواهرات دست‌ساز.', 'year' => '۱۴۰۳', 'featured' => true,
            'cover' => '/seed-demo/jewelry-square.jpg',
            'categories' => [
                ['title' => 'کاتالوگ محصول', 'desc' => 'عکاسی استودیویی و کاتالوگ آنلاین محصولات طلا و جواهر.', 'ratio' => '1:1', 'cover' => '/seed-demo/jewelry-square.jpg', 'items' => ['/seed-demo/jewelry-square.jpg', '/seed-demo/jewelry-square.jpg', '/seed-demo/jewelry-square.jpg']],
            ],
        ],
        ['name' => 'رنس تکس', 'industry' => IND_BUSINESS, 'desc' => 'تولید و عرضه پوشاک و منسوجات.', 'year' => '۱۴۰۲', 'featured' => false, 'cover' => '', 'categories' => []],
        [
            'name' => 'رویان شبکه', 'industry' => IND_BUSINESS, 'desc' => 'خدمات اینترنت و زیرساخت شبکه.', 'year' => '۱۴۰۲', 'featured' => true,
            'cover' => '/seed-demo/client-tech.jpg',
            'categories' => [
                ['title' => 'طراحی سایت', 'desc' => 'طراحی و توسعه وب‌سایت شرکتی.', 'ratio' => '16:9', 'cover' => '/seed-demo/website-design-1.jpg', 'items' => ['/seed-demo/website-design-1.jpg']],
                ['title' => 'شبکه‌های اجتماعی', 'desc' => 'تولید محتوای مربع برای اینستاگرام.', 'ratio' => '1:1', 'cover' => '/seed-demo/client-tech.jpg', 'items' => ['/seed-demo/client-tech.jpg', '/seed-demo/client-tech.jpg']],
            ],
        ],
        ['name' => 'الدراگ استور', 'industry' => IND_BUSINESS, 'desc' => 'فروشگاه آنلاین محصولات آرایشی و بهداشتی.', 'year' => '۱۴۰۳', 'featured' => false, 'cover' => '', 'categories' => []],
        ['name' => 'گالری نقره سیده راد', 'industry' => IND_BUSINESS, 'desc' => 'طراحی و فروش زیورآلات نقره.', 'year' => '۱۴۰۱', 'featured' => false, 'cover' => '', 'categories' => []],
        ['name' => 'گالری جواهرات هم‌نفس', 'industry' => IND_BUSINESS, 'desc' => 'گالری تخصصی جواهرات و سنگ‌های قیمتی.', 'year' => '۱۴۰۲', 'featured' => false, 'cover' => '', 'categories' => []],
        ['name' => 'طلا و جواهرات محمد سیاوشی', 'industry' => IND_BUSINESS, 'desc' => 'تولید و فروش طلا و جواهرات.', 'year' => '۱۴۰۱', 'featured' => false, 'cover' => '', 'categories' => []],
        ['name' => 'مجموعه نظریان', 'industry' => IND_BUSINESS, 'desc' => 'مجموعه تجاری چندمنظوره.', 'year' => '۱۴۰۲', 'featured' => false, 'cover' => '', 'categories' => []],
        ['name' => 'ابزار آلات قشقایی', 'industry' => IND_BUSINESS, 'desc' => 'فروش ابزارآلات صنعتی و ساختمانی.', 'year' => '۱۴۰۱', 'featured' => false, 'cover' => '', 'categories' => []],
        [
            'name' => 'آژانس تبلیغاتی لامیلا', 'industry' => IND_BUSINESS, 'desc' => 'آژانس خلاق در حوزه تبلیغات و برندینگ.', 'year' => '۱۴۰۳', 'featured' => true,
            'cover' => '/seed-demo/agency-reels.jpg',
            'categories' => [
                ['title' => 'ریلز و استوری', 'desc' => 'تولید محتوای ویدیویی عمودی برای شبکه‌های اجتماعی.', 'ratio' => '9:16', 'cover' => '/seed-demo/agency-reels.jpg', 'items' => ['/seed-demo/agency-reels.jpg', '/seed-demo/agency-reels.jpg', '/seed-demo/agency-reels.jpg']],
                ['title' => 'کمپین دیجیتال', 'desc' => 'کمپین تبلیغاتی چندکاناله.', 'ratio' => '16:9', 'cover' => '/seed-demo/campaign-1.jpg', 'items' => ['/seed-demo/campaign-1.jpg', '/seed-demo/website-design-1.jpg']],
                ['title' => 'پست‌های اینستاگرام', 'desc' => 'طراحی گرافیک پست‌های مربعی.', 'ratio' => '1:1', 'cover' => '/seed-demo/festival-square.jpg', 'items' => ['/seed-demo/festival-square.jpg', '/seed-demo/festival-square.jpg', '/seed-demo/festival-square.jpg']],
            ],
        ],
        ['name' => 'کلینیک مشاوره کودک و نوجوان بهشت زندگی', 'industry' => IND_BUSINESS, 'desc' => 'مرکز مشاوره و روان‌شناسی کودک و نوجوان.', 'year' => '۱۴۰۳', 'featured' => false, 'cover' => '', 'categories' => []],
        ['name' => 'استودیو هور', 'industry' => IND_BUSINESS, 'desc' => 'استودیوی عکاسی و تولید محتوا.', 'year' => '۱۴۰۲', 'featured' => false, 'cover' => '', 'categories' => []],

        // ---------------- دولتی و نهادی ----------------
        ['name' => 'وزارت علوم، تحقیقات و فناوری', 'industry' => IND_GOV, 'desc' => 'نهاد سیاست‌گذار آموزش عالی و پژوهش کشور.', 'year' => '۱۴۰۲', 'featured' => false, 'cover' => '', 'categories' => []],
        [
            'name' => 'دانشگاه شهید چمران اهواز', 'industry' => IND_GOV, 'desc' => 'دانشگاه دولتی در جنوب‌غرب کشور.', 'year' => '۱۴۰۳', 'featured' => true,
            'cover' => '/seed-demo/university-campaign.jpg',
            'categories' => [
                ['title' => 'کمپین معرفی دانشگاه', 'desc' => 'کمپین تبلیغاتی معرفی رشته‌ها و فضای دانشگاه.', 'ratio' => '16:9', 'cover' => '/seed-demo/university-campaign.jpg', 'items' => ['/seed-demo/university-campaign.jpg', '/seed-demo/about.jpg', '/seed-demo/university-campaign.jpg']],
            ],
        ],
        [
            'name' => 'استانداری خوزستان', 'industry' => IND_GOV, 'desc' => 'نهاد اجرایی استان خوزستان.', 'year' => '۱۴۰۲', 'featured' => true,
            'cover' => '/seed-demo/gov-banner.jpg',
            'categories' => [
                ['title' => 'کمپین اطلاع‌رسانی', 'desc' => 'کمپین اطلاع‌رسانی و تولید محتوای استانی.', 'ratio' => '16:9', 'cover' => '/seed-demo/gov-banner.jpg', 'items' => ['/seed-demo/gov-banner.jpg', '/seed-demo/gov-banner.jpg', '/seed-demo/website-design-1.jpg', '/seed-demo/campaign-1.jpg', '/seed-demo/gov-banner.jpg']],
            ],
        ],
        ['name' => 'استانداری هرمزگان', 'industry' => IND_GOV, 'desc' => 'نهاد اجرایی استان هرمزگان.', 'year' => '۱۴۰۱', 'featured' => false, 'cover' => '', 'categories' => []],
        ['name' => 'شهرداری اهواز', 'industry' => IND_GOV, 'desc' => 'مدیریت شهری کلان‌شهر اهواز.', 'year' => '۱۴۰۲', 'featured' => false, 'cover' => '', 'categories' => []],
        ['name' => 'صداوسیمای مرکز خوزستان', 'industry' => IND_GOV, 'desc' => 'رسانه ملی، مرکز استان خوزستان.', 'year' => '۱۴۰۱', 'featured' => false, 'cover' => '', 'categories' => []],
        ['name' => 'سازمان تبلیغات استان خوزستان', 'industry' => IND_GOV, 'desc' => 'نهاد فرهنگی-تبلیغی استان.', 'year' => '۱۴۰۲', 'featured' => false, 'cover' => '', 'categories' => []],
        ['name' => 'مرکز رسانه استان خوزستان', 'industry' => IND_GOV, 'desc' => 'مرکز هماهنگی رسانه‌ای استان.', 'year' => '۱۴۰۲', 'featured' => false, 'cover' => '', 'categories' => []],
        ['name' => 'دفتر امام جمعه اهواز', 'industry' => IND_GOV, 'desc' => 'دفتر نهاد دینی و فرهنگی اهواز.', 'year' => '۱۴۰۱', 'featured' => false, 'cover' => '', 'categories' => []],
        ['name' => 'پایگاه میراث جهانی سازه‌های آبی شوشتر', 'industry' => IND_GOV, 'desc' => 'پایگاه ثبت‌شده میراث جهانی یونسکو.', 'year' => '۱۴۰۳', 'featured' => false, 'cover' => '', 'categories' => []],
        ['name' => 'انجمن خیریه ۱۴ معصوم', 'industry' => IND_GOV, 'desc' => 'نهاد خیریه و حمایتی مردمی.', 'year' => '۱۴۰۲', 'featured' => false, 'cover' => '', 'categories' => []],
        ['name' => 'مشاوران افق دانش ثریا', 'industry' => IND_GOV, 'desc' => 'موسسه مشاوره تحصیلی و آموزشی.', 'year' => '۱۴۰۳', 'featured' => false, 'cover' => '', 'categories' => []],

        // ---------------- آموزشی و فرهنگی ----------------
        ['name' => 'چمران پلاس', 'industry' => IND_CULTURE, 'desc' => 'رسانه و پلتفرم محتوایی دانشگاهی.', 'year' => '۱۴۰۳', 'featured' => false, 'cover' => '', 'categories' => []],
        ['name' => 'رسانه پرواز', 'industry' => IND_CULTURE, 'desc' => 'رسانه محتوایی و خبری.', 'year' => '۱۴۰۲', 'featured' => false, 'cover' => '', 'categories' => []],
        [
            'name' => 'جشنواره ملی رویش', 'industry' => IND_CULTURE, 'desc' => 'جشنواره ملی فرهنگی و هنری.', 'year' => '۱۴۰۳', 'featured' => true,
            'cover' => '/seed-demo/festival-square.jpg',
            'categories' => [
                ['title' => 'پوستر و هویت بصری', 'desc' => 'طراحی پوستر و هویت بصری جشنواره.', 'ratio' => '1:1', 'cover' => '/seed-demo/festival-square.jpg', 'items' => ['/seed-demo/festival-square.jpg', '/seed-demo/festival-square.jpg']],
                ['title' => 'کمپین تبلیغاتی', 'desc' => 'کمپین معرفی و اطلاع‌رسانی جشنواره.', 'ratio' => '16:9', 'cover' => '/seed-demo/campaign-1.jpg', 'items' => ['/seed-demo/campaign-1.jpg']],
            ],
        ],
        [
            'name' => 'کنسرت علیرضا قربانی', 'industry' => IND_CULTURE, 'desc' => 'اجرای زنده موسیقی سنتی ایرانی.', 'year' => '۱۴۰۴', 'featured' => true,
            'cover' => '/seed-demo/concert-poster.jpg',
            'categories' => [
                ['title' => 'تیزر و ریلز کنسرت', 'desc' => 'تیزرهای عمودی معرفی کنسرت برای اینستاگرام.', 'ratio' => '9:16', 'cover' => '/seed-demo/concert-poster.jpg', 'items' => ['/seed-demo/concert-poster.jpg', '/seed-demo/concert-poster.jpg', '/seed-demo/concert-poster.jpg', '/seed-demo/concert-poster.jpg', '/seed-demo/concert-poster.jpg']],
            ],
        ],
        ['name' => 'ارکستر سازهای ایرانی به یاد خالقی', 'industry' => IND_CULTURE, 'desc' => 'ارکستر تخصصی سازهای ایرانی.', 'year' => '۱۴۰۲', 'featured' => false, 'cover' => '', 'categories' => []],
        ['name' => 'جایزه ملی آهنگ‌سازی استاد روح‌الله خالقی', 'industry' => IND_CULTURE, 'desc' => 'جایزه ملی در حوزه آهنگ‌سازی.', 'year' => '۱۴۰۳', 'featured' => false, 'cover' => '', 'categories' => []],
        ['name' => 'خانه موسیقی تهران', 'industry' => IND_CULTURE, 'desc' => 'نهاد صنفی و فرهنگی موسیقی.', 'year' => '۱۴۰۱', 'featured' => false, 'cover' => '', 'categories' => []],
        ['name' => 'گروه موسیقی نی‌نوا', 'industry' => IND_CULTURE, 'desc' => 'گروه اجرای موسیقی سنتی ایرانی.', 'year' => '۱۴۰۲', 'featured' => false, 'cover' => '', 'categories' => []],
        ['name' => 'مجموعه سرودی‌های استان خوزستان', 'industry' => IND_CULTURE, 'desc' => 'تولید و اجرای سرودهای آیینی و ملی.', 'year' => '۱۴۰۱', 'featured' => false, 'cover' => '', 'categories' => []],
        ['name' => 'موسسه برتینا', 'industry' => IND_CULTURE, 'desc' => 'موسسه فرهنگی و آموزشی.', 'year' => '۱۴۰۲', 'featured' => false, 'cover' => '', 'categories' => []],
    ];
}

$order = 0;
foreach (clients_data() as $c) {
    $slug = make_slug($c['name']);
    $slug = unique_slug($slug, fn ($s) => db_value('SELECT COUNT(*) FROM clients WHERE slug = ?', [$s]) > 0);

    db_run(
        'INSERT INTO clients (slug, name, cover_image_url, accent_color, short_description, industry, website_url, year, featured, published, order_index, created_at, updated_at)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 1, ?, ?, ?)',
        [
            $slug, $c['name'], $c['cover'], ACCENTS[$order % count(ACCENTS)], $c['desc'], $c['industry'],
            '', $c['year'], $c['featured'] ? 1 : 0, $order, $now, $now,
        ]
    );
    $clientId = db_insert_id();
    $order++;

    $catOrder = 0;
    foreach ($c['categories'] as $cat) {
        $catSlug = make_slug($cat['title']) ?: ('cat-' . $catOrder);

        db_run(
            'INSERT INTO categories (client_id, title, slug, description, cover_image_url, aspect_ratio, order_index, created_at)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?)',
            [$clientId, $cat['title'], $catSlug, $cat['desc'], $cat['cover'], $cat['ratio'], $catOrder, $now]
        );
        $categoryId = db_insert_id();
        $catOrder++;

        $itemOrder = 0;
        foreach ($cat['items'] as $img) {
            db_run(
                'INSERT INTO portfolio_items (category_id, title, description, media_url, media_type, order_index, featured_home, created_at)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?)',
                [$categoryId, $cat['title'] . ' - ' . $c['name'], $cat['desc'], $img, 'image', $itemOrder, $itemOrder === 0 ? 1 : 0, $now]
            );
            $itemOrder++;
        }
    }
}

// ---------------------------------------------------------------------
// دو پیام نمونه
// ---------------------------------------------------------------------
db_run(
    'INSERT INTO messages (name, email, phone, subject, message, is_read, created_at) VALUES (?, ?, ?, ?, ?, 0, ?)',
    ['سارا احمدی', 'sara@example.com', '۰۹۱۲۱۲۳۴۵۶۷', 'درخواست همکاری برای کمپین تبلیغاتی', 'سلام، برای برند ما یه کمپین دیجیتال مارکتینگ نیاز داریم. لطفا باهام تماس بگیرید.', $now]
);
db_run(
    'INSERT INTO messages (name, email, phone, subject, message, is_read, created_at) VALUES (?, ?, ?, ?, ?, 1, ?)',
    ['علی محمدی', 'ali@example.com', '', 'طراحی سایت', 'امکان طراحی سایت فروشگاهی برای کسب‌وکارمون هست؟ لطفا نمونه‌کار بفرستید.', $now]
);

$count = count(clients_data());
echo "[seed] {$count} همراه (کارفرما) با موفقیت ساخته شدند.\n";
echo "[seed] دیتابیس در " . DB_PATH . " ساخته شد.\n";
echo '[seed] ورود مدیر -> نام کاربری: ' . DEFAULT_ADMIN_USERNAME . ' / رمز عبور: ' . DEFAULT_ADMIN_PASSWORD . "\n";
