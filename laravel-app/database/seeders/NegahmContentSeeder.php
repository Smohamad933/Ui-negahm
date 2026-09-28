<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Client;
use App\Models\Message;
use App\Models\PortfolioItem;
use App\Models\Setting;
use App\Support\Slugger;
use Illuminate\Database\Seeder;

/**
 * Seeds the real "همراهان نگاه مدیا" (negahm.ir) content for first run.
 * Idempotent: skips if any clients already exist. Ported 1:1 from the
 * previous Next.js version's scripts/seed.js.
 */
class NegahmContentSeeder extends Seeder
{
    private const IND_BUSINESS = 'کسب‌وکار و بیزینس';
    private const IND_GOV = 'دولتی و نهادی';
    private const IND_CULTURE = 'آموزشی و فرهنگی';

    private const ACCENTS = ['#ff3d74', '#2f6fff', '#ffc629', '#8c5cff', '#29d6a8', '#ff7a30'];

    public function run(): void
    {
        if (Client::query()->count() > 0) {
            $this->command?->info('[seed] Clients already exist, skipping demo seed.');

            return;
        }

        Setting::current()->update(['about_image_url' => '/seed-demo/about.jpg']);

        $order = 0;
        foreach ($this->clientsData() as $c) {
            $slug = Slugger::make($c['name']);
            $slug = Slugger::unique($slug, fn ($s) => Client::query()->where('slug', $s)->exists());

            $client = Client::query()->create([
                'slug' => $slug,
                'name' => $c['name'],
                'cover_image_url' => $c['cover'],
                'accent_color' => self::ACCENTS[$order % count(self::ACCENTS)],
                'short_description' => $c['desc'],
                'industry' => $c['industry'],
                'website_url' => '',
                'year' => $c['year'],
                'featured' => $c['featured'],
                'published' => true,
                'order_index' => $order,
            ]);
            $order++;

            $catOrder = 0;
            foreach ($c['categories'] as $cat) {
                $catSlug = Slugger::make($cat['title']) ?: ('cat-' . $catOrder);

                $category = Category::query()->create([
                    'client_id' => $client->id,
                    'title' => $cat['title'],
                    'slug' => $catSlug,
                    'description' => $cat['desc'],
                    'cover_image_url' => $cat['cover'],
                    'aspect_ratio' => $cat['ratio'],
                    'order_index' => $catOrder,
                ]);
                $catOrder++;

                $itemOrder = 0;
                foreach ($cat['items'] as $img) {
                    PortfolioItem::query()->create([
                        'category_id' => $category->id,
                        'title' => $cat['title'] . ' - ' . $c['name'],
                        'description' => $cat['desc'],
                        'media_url' => $img,
                        'media_type' => 'image',
                        'featured_home' => $itemOrder === 0,
                        'order_index' => $itemOrder,
                    ]);
                    $itemOrder++;
                }
            }
        }

        Message::query()->create([
            'name' => 'سارا احمدی',
            'email' => 'sara@example.com',
            'phone' => '۰۹۱۲۱۲۳۴۵۶۷',
            'subject' => 'درخواست همکاری برای کمپین تبلیغاتی',
            'message' => 'سلام، برای برند ما یه کمپین دیجیتال مارکتینگ نیاز داریم. لطفا باهام تماس بگیرید.',
            'is_read' => false,
        ]);
        Message::query()->create([
            'name' => 'علی محمدی',
            'email' => 'ali@example.com',
            'phone' => '',
            'subject' => 'طراحی سایت',
            'message' => 'امکان طراحی سایت فروشگاهی برای کسب‌وکارمون هست؟ لطفا نمونه‌کار بفرستید.',
            'is_read' => true,
        ]);

        $this->command?->info('[seed] ' . count($this->clientsData()) . ' همراه (کارفرما) با موفقیت seed شدند.');
    }

    private function clientsData(): array
    {
        return [
            // ---------------- کسب‌وکار و بیزینس ----------------
            [
                'name' => 'گالری طلا و جواهر محمود',
                'industry' => self::IND_BUSINESS,
                'desc' => 'خرده‌فروشی و فروش آنلاین طلا و جواهرات دست‌ساز.',
                'year' => '۱۴۰۳',
                'featured' => true,
                'cover' => '/seed-demo/jewelry-square.jpg',
                'categories' => [
                    [
                        'title' => 'کاتالوگ محصول',
                        'desc' => 'عکاسی استودیویی و کاتالوگ آنلاین محصولات طلا و جواهر.',
                        'ratio' => '1:1',
                        'cover' => '/seed-demo/jewelry-square.jpg',
                        'items' => ['/seed-demo/jewelry-square.jpg', '/seed-demo/jewelry-square.jpg', '/seed-demo/jewelry-square.jpg'],
                    ],
                ],
            ],
            ['name' => 'رنس تکس', 'industry' => self::IND_BUSINESS, 'desc' => 'تولید و عرضه پوشاک و منسوجات.', 'year' => '۱۴۰۲', 'featured' => false, 'cover' => '', 'categories' => []],
            [
                'name' => 'رویان شبکه',
                'industry' => self::IND_BUSINESS,
                'desc' => 'خدمات اینترنت و زیرساخت شبکه.',
                'year' => '۱۴۰۲',
                'featured' => true,
                'cover' => '/seed-demo/client-tech.jpg',
                'categories' => [
                    ['title' => 'طراحی سایت', 'desc' => 'طراحی و توسعه وب‌سایت شرکتی.', 'ratio' => '16:9', 'cover' => '/seed-demo/website-design-1.jpg', 'items' => ['/seed-demo/website-design-1.jpg']],
                    ['title' => 'شبکه‌های اجتماعی', 'desc' => 'تولید محتوای مربع برای اینستاگرام.', 'ratio' => '1:1', 'cover' => '/seed-demo/client-tech.jpg', 'items' => ['/seed-demo/client-tech.jpg', '/seed-demo/client-tech.jpg']],
                ],
            ],
            ['name' => 'الدراگ استور', 'industry' => self::IND_BUSINESS, 'desc' => 'فروشگاه آنلاین محصولات آرایشی و بهداشتی.', 'year' => '۱۴۰۳', 'featured' => false, 'cover' => '', 'categories' => []],
            ['name' => 'گالری نقره سیده راد', 'industry' => self::IND_BUSINESS, 'desc' => 'طراحی و فروش زیورآلات نقره.', 'year' => '۱۴۰۱', 'featured' => false, 'cover' => '', 'categories' => []],
            ['name' => 'گالری جواهرات هم‌نفس', 'industry' => self::IND_BUSINESS, 'desc' => 'گالری تخصصی جواهرات و سنگ‌های قیمتی.', 'year' => '۱۴۰۲', 'featured' => false, 'cover' => '', 'categories' => []],
            ['name' => 'طلا و جواهرات محمد سیاوشی', 'industry' => self::IND_BUSINESS, 'desc' => 'تولید و فروش طلا و جواهرات.', 'year' => '۱۴۰۱', 'featured' => false, 'cover' => '', 'categories' => []],
            ['name' => 'مجموعه نظریان', 'industry' => self::IND_BUSINESS, 'desc' => 'مجموعه تجاری چندمنظوره.', 'year' => '۱۴۰۲', 'featured' => false, 'cover' => '', 'categories' => []],
            ['name' => 'ابزار آلات قشقایی', 'industry' => self::IND_BUSINESS, 'desc' => 'فروش ابزارآلات صنعتی و ساختمانی.', 'year' => '۱۴۰۱', 'featured' => false, 'cover' => '', 'categories' => []],
            [
                'name' => 'آژانس تبلیغاتی لامیلا',
                'industry' => self::IND_BUSINESS,
                'desc' => 'آژانس خلاق در حوزه تبلیغات و برندینگ.',
                'year' => '۱۴۰۳',
                'featured' => true,
                'cover' => '/seed-demo/agency-reels.jpg',
                'categories' => [
                    ['title' => 'ریلز و استوری', 'desc' => 'تولید محتوای ویدیویی عمودی برای شبکه‌های اجتماعی.', 'ratio' => '9:16', 'cover' => '/seed-demo/agency-reels.jpg', 'items' => ['/seed-demo/agency-reels.jpg', '/seed-demo/agency-reels.jpg', '/seed-demo/agency-reels.jpg']],
                    ['title' => 'کمپین دیجیتال', 'desc' => 'کمپین تبلیغاتی چندکاناله.', 'ratio' => '16:9', 'cover' => '/seed-demo/campaign-1.jpg', 'items' => ['/seed-demo/campaign-1.jpg', '/seed-demo/website-design-1.jpg']],
                    ['title' => 'پست‌های اینستاگرام', 'desc' => 'طراحی گرافیک پست‌های مربعی.', 'ratio' => '1:1', 'cover' => '/seed-demo/festival-square.jpg', 'items' => ['/seed-demo/festival-square.jpg', '/seed-demo/festival-square.jpg', '/seed-demo/festival-square.jpg']],
                ],
            ],
            ['name' => 'کلینیک مشاوره کودک و نوجوان بهشت زندگی', 'industry' => self::IND_BUSINESS, 'desc' => 'مرکز مشاوره و روان‌شناسی کودک و نوجوان.', 'year' => '۱۴۰۳', 'featured' => false, 'cover' => '', 'categories' => []],
            ['name' => 'استودیو هور', 'industry' => self::IND_BUSINESS, 'desc' => 'استودیوی عکاسی و تولید محتوا.', 'year' => '۱۴۰۲', 'featured' => false, 'cover' => '', 'categories' => []],

            // ---------------- دولتی و نهادی ----------------
            ['name' => 'وزارت علوم، تحقیقات و فناوری', 'industry' => self::IND_GOV, 'desc' => 'نهاد سیاست‌گذار آموزش عالی و پژوهش کشور.', 'year' => '۱۴۰۲', 'featured' => false, 'cover' => '', 'categories' => []],
            [
                'name' => 'دانشگاه شهید چمران اهواز',
                'industry' => self::IND_GOV,
                'desc' => 'دانشگاه دولتی در جنوب‌غرب کشور.',
                'year' => '۱۴۰۳',
                'featured' => true,
                'cover' => '/seed-demo/university-campaign.jpg',
                'categories' => [
                    [
                        'title' => 'کمپین معرفی دانشگاه',
                        'desc' => 'کمپین تبلیغاتی معرفی رشته‌ها و فضای دانشگاه.',
                        'ratio' => '16:9',
                        'cover' => '/seed-demo/university-campaign.jpg',
                        'items' => ['/seed-demo/university-campaign.jpg', '/seed-demo/about.jpg', '/seed-demo/university-campaign.jpg'],
                    ],
                ],
            ],
            [
                'name' => 'استانداری خوزستان',
                'industry' => self::IND_GOV,
                'desc' => 'نهاد اجرایی استان خوزستان.',
                'year' => '۱۴۰۲',
                'featured' => true,
                'cover' => '/seed-demo/gov-banner.jpg',
                'categories' => [
                    [
                        'title' => 'کمپین اطلاع‌رسانی',
                        'desc' => 'کمپین اطلاع‌رسانی و تولید محتوای استانی.',
                        'ratio' => '16:9',
                        'cover' => '/seed-demo/gov-banner.jpg',
                        'items' => [
                            '/seed-demo/gov-banner.jpg',
                            '/seed-demo/gov-banner.jpg',
                            '/seed-demo/website-design-1.jpg',
                            '/seed-demo/campaign-1.jpg',
                            '/seed-demo/gov-banner.jpg',
                        ],
                    ],
                ],
            ],
            ['name' => 'استانداری هرمزگان', 'industry' => self::IND_GOV, 'desc' => 'نهاد اجرایی استان هرمزگان.', 'year' => '۱۴۰۱', 'featured' => false, 'cover' => '', 'categories' => []],
            ['name' => 'شهرداری اهواز', 'industry' => self::IND_GOV, 'desc' => 'مدیریت شهری کلان‌شهر اهواز.', 'year' => '۱۴۰۲', 'featured' => false, 'cover' => '', 'categories' => []],
            ['name' => 'صداوسیمای مرکز خوزستان', 'industry' => self::IND_GOV, 'desc' => 'رسانه ملی، مرکز استان خوزستان.', 'year' => '۱۴۰۱', 'featured' => false, 'cover' => '', 'categories' => []],
            ['name' => 'سازمان تبلیغات استان خوزستان', 'industry' => self::IND_GOV, 'desc' => 'نهاد فرهنگی-تبلیغی استان.', 'year' => '۱۴۰۲', 'featured' => false, 'cover' => '', 'categories' => []],
            ['name' => 'مرکز رسانه استان خوزستان', 'industry' => self::IND_GOV, 'desc' => 'مرکز هماهنگی رسانه‌ای استان.', 'year' => '۱۴۰۲', 'featured' => false, 'cover' => '', 'categories' => []],
            ['name' => 'دفتر امام جمعه اهواز', 'industry' => self::IND_GOV, 'desc' => 'دفتر نهاد دینی و فرهنگی اهواز.', 'year' => '۱۴۰۱', 'featured' => false, 'cover' => '', 'categories' => []],
            ['name' => 'پایگاه میراث جهانی سازه‌های آبی شوشتر', 'industry' => self::IND_GOV, 'desc' => 'پایگاه ثبت‌شده میراث جهانی یونسکو.', 'year' => '۱۴۰۳', 'featured' => false, 'cover' => '', 'categories' => []],
            ['name' => 'انجمن خیریه ۱۴ معصوم', 'industry' => self::IND_GOV, 'desc' => 'نهاد خیریه و حمایتی مردمی.', 'year' => '۱۴۰۲', 'featured' => false, 'cover' => '', 'categories' => []],
            ['name' => 'مشاوران افق دانش ثریا', 'industry' => self::IND_GOV, 'desc' => 'موسسه مشاوره تحصیلی و آموزشی.', 'year' => '۱۴۰۳', 'featured' => false, 'cover' => '', 'categories' => []],

            // ---------------- آموزشی و فرهنگی ----------------
            ['name' => 'چمران پلاس', 'industry' => self::IND_CULTURE, 'desc' => 'رسانه و پلتفرم محتوایی دانشگاهی.', 'year' => '۱۴۰۳', 'featured' => false, 'cover' => '', 'categories' => []],
            ['name' => 'رسانه پرواز', 'industry' => self::IND_CULTURE, 'desc' => 'رسانه محتوایی و خبری.', 'year' => '۱۴۰۲', 'featured' => false, 'cover' => '', 'categories' => []],
            [
                'name' => 'جشنواره ملی رویش',
                'industry' => self::IND_CULTURE,
                'desc' => 'جشنواره ملی فرهنگی و هنری.',
                'year' => '۱۴۰۳',
                'featured' => true,
                'cover' => '/seed-demo/festival-square.jpg',
                'categories' => [
                    ['title' => 'پوستر و هویت بصری', 'desc' => 'طراحی پوستر و هویت بصری جشنواره.', 'ratio' => '1:1', 'cover' => '/seed-demo/festival-square.jpg', 'items' => ['/seed-demo/festival-square.jpg', '/seed-demo/festival-square.jpg']],
                    ['title' => 'کمپین تبلیغاتی', 'desc' => 'کمپین معرفی و اطلاع‌رسانی جشنواره.', 'ratio' => '16:9', 'cover' => '/seed-demo/campaign-1.jpg', 'items' => ['/seed-demo/campaign-1.jpg']],
                ],
            ],
            [
                'name' => 'کنسرت علیرضا قربانی',
                'industry' => self::IND_CULTURE,
                'desc' => 'اجرای زنده موسیقی سنتی ایرانی.',
                'year' => '۱۴۰۴',
                'featured' => true,
                'cover' => '/seed-demo/concert-poster.jpg',
                'categories' => [
                    [
                        'title' => 'تیزر و ریلز کنسرت',
                        'desc' => 'تیزرهای عمودی معرفی کنسرت برای اینستاگرام.',
                        'ratio' => '9:16',
                        'cover' => '/seed-demo/concert-poster.jpg',
                        'items' => [
                            '/seed-demo/concert-poster.jpg',
                            '/seed-demo/concert-poster.jpg',
                            '/seed-demo/concert-poster.jpg',
                            '/seed-demo/concert-poster.jpg',
                            '/seed-demo/concert-poster.jpg',
                        ],
                    ],
                ],
            ],
            ['name' => 'ارکستر سازهای ایرانی به یاد خالقی', 'industry' => self::IND_CULTURE, 'desc' => 'ارکستر تخصصی سازهای ایرانی.', 'year' => '۱۴۰۲', 'featured' => false, 'cover' => '', 'categories' => []],
            ['name' => 'جایزه ملی آهنگ‌سازی استاد روح‌الله خالقی', 'industry' => self::IND_CULTURE, 'desc' => 'جایزه ملی در حوزه آهنگ‌سازی.', 'year' => '۱۴۰۳', 'featured' => false, 'cover' => '', 'categories' => []],
            ['name' => 'خانه موسیقی تهران', 'industry' => self::IND_CULTURE, 'desc' => 'نهاد صنفی و فرهنگی موسیقی.', 'year' => '۱۴۰۱', 'featured' => false, 'cover' => '', 'categories' => []],
            ['name' => 'گروه موسیقی نی‌نوا', 'industry' => self::IND_CULTURE, 'desc' => 'گروه اجرای موسیقی سنتی ایرانی.', 'year' => '۱۴۰۲', 'featured' => false, 'cover' => '', 'categories' => []],
            ['name' => 'مجموعه سرودی‌های استان خوزستان', 'industry' => self::IND_CULTURE, 'desc' => 'تولید و اجرای سرودهای آیینی و ملی.', 'year' => '۱۴۰۱', 'featured' => false, 'cover' => '', 'categories' => []],
            ['name' => 'موسسه برتینا', 'industry' => self::IND_CULTURE, 'desc' => 'موسسه فرهنگی و آموزشی.', 'year' => '۱۴۰۲', 'featured' => false, 'cover' => '', 'categories' => []],
        ];
    }
}
