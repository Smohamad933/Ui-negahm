<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('site_name')->default('نگاه مدیا');
            $table->string('tagline')->default('آژانس خلاق و تبلیغاتی');
            $table->string('logo_url')->nullable()->default('');
            $table->string('favicon_url')->nullable()->default('');
            $table->string('color_bg')->default('#fff6e9');
            $table->string('color_fg')->default('#171310');
            $table->string('color_primary')->default('#ff3d74');
            $table->string('color_secondary')->default('#2f6fff');
            $table->string('color_accent')->default('#ffc629');
            $table->string('color_muted')->default('#8b8378');
            $table->string('font_family')->default('Vazirmatn Variable');
            $table->text('hero_title')->default('همه‌چیز در یک نگاه');
            $table->text('hero_subtitle')->default('از ایده تا اجرا؛ هویت بصری، تولید محتوا، کمپین و دیجیتال مارکتینگ را یکپارچه می‌سازیم تا برند شما فقط دیده نشود، بلکه در ذهن بماند.');
            $table->string('hero_cta_text')->default('دیدن نمونه‌کارها');
            $table->string('hero_cta_link')->default('/clients');
            $table->string('hero_media_url')->nullable()->default('');
            $table->text('about_title')->default('ما فقط تبلیغ نمی‌کنیم؛ روایت می‌سازیم.');
            $table->text('about_body')->default('هر پروژه برای ما یک روایت است؛ از لحظه‌ای که مسئله برند را می‌شناسیم تا لحظه‌ای که مخاطب با آن روبه‌رو می‌شود. تیم نگاه مدیا این مسیر را یکپارچه پیش می‌برد تا خروجی، منسجم و ماندگار باشد.');
            $table->string('about_image_url')->nullable()->default('');
            $table->string('contact_address')->nullable()->default('تهران | اهواز');
            $table->string('contact_phone')->nullable()->default('۰۹۰۱۲۳۱۹۸۷۹');
            $table->string('contact_email')->nullable()->default('negahminfo@gmail.com');
            $table->text('contact_map_embed')->nullable()->default('');
            $table->string('social_instagram')->nullable()->default('');
            $table->string('social_telegram')->nullable()->default('');
            $table->string('social_whatsapp')->nullable()->default('');
            $table->string('social_linkedin')->nullable()->default('');
            $table->string('footer_text')->default('نگاه مدیا — تمامی حقوق محفوظ است.');
            $table->timestamp('updated_at')->nullable();
        });

        DB::table('settings')->insert(['id' => 1]);
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
