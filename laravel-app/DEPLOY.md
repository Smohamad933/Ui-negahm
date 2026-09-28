# راهنمای استقرار روی IIS (PHP بومی، بدون Node.js)

این پروژه Laravel + SQLite است و برای اجرا روی **IIS با PHP بومی** (از طریق
FastCGI) طراحی شده — هیچ نیازی به Node.js یا iisnode در سرور واقعی نیست؛
مرحله‌ی ساخت CSS (پوشه‌ی `.assets-build/`) فقط یک ابزار محلی/یک‌بارمصرف در
زمان توسعه است و خروجی‌اش (`public/css/app.css` و `public/fonts/*`) از قبل
ساخته و در پروژه کامیت شده است.

## پیش‌نیازهای سرور

1. **Windows Server + IIS** با فعال بودن نقش «Web Server (IIS)».
2. **URL Rewrite Module** برای IIS (از سایت مایکروسافت/IIS.net نصب شود).
3. **PHP 8.2 یا بالاتر** نصب‌شده روی سرور (نسخه‌ی غیر Thread-Safe برای
   FastCGI توصیه می‌شود) + پسوندهای زیر فعال در `php.ini`:
   `pdo_sqlite`, `sqlite3`, `mbstring`, `openssl`, `fileinfo`, `curl`,
   `tokenizer`, `xml`, `ctype`, `session`.
4. **PHP Manager for IIS** (افزونه‌ی IIS Manager) برای رجیستر کردن PHP
   روی FastCGI به‌سادگی — گزینه‌ی «Register new PHP version» را بزنید و
   مسیر `php-cgi.exe` را انتخاب کنید. (اگر ترجیح می‌دهید دستی تنظیم کنید،
   `public/web.config` از قبل یک handler به نام `PHP_via_FastCGI` تعریف
   کرده که همان قراردادی است که PHP Manager هم استفاده می‌کند.)
5. **Composer** (فقط برای نصب یک‌باره‌ی وابستگی‌ها؛ لازم نیست روی خود
   سرور IIS باشد — می‌توانید پوشه‌ی `vendor/` را از یک ماشین دیگر با
   `composer install --no-dev --optimize-autoloader` بسازید و کل پروژه
   را همراه با `vendor/` آپلود کنید).

## مراحل استقرار

1. کل پوشه‌ی `laravel-app/` را روی سرور کپی کنید (مثلاً در
   `C:\inetpub\negahm-media\`).
2. اگر پوشه‌ی `vendor/` را همراه نیاورده‌اید:
   ```
   composer install --no-dev --optimize-autoloader
   ```
3. فایل `.env.example` را کپی و به نام `.env` ذخیره کنید؛ سپس مقادیر را
   با اطلاعات واقعی سرور خودتان پر کنید (حداقل `APP_URL`، و در صورت نیاز
   `ADMIN_USERNAME` / `ADMIN_PASSWORD` برای کاربر مدیر پیش‌فرض).
4. یک کلید امنیتی برنامه بسازید:
   ```
   php artisan key:generate
   ```
5. فایل دیتابیس SQLite را بسازید (اگر وجود ندارد):
   ```
   type nul > database\database.sqlite
   ```
   (یا در PowerShell: `New-Item database\database.sqlite`)
6. مایگریشن‌ها و seed اولیه (تنظیمات پیش‌فرض + کاربر مدیر + ۳۴ کارفرمای
   نمونه) را اجرا کنید:
   ```
   php artisan migrate --seed
   ```
7. دسترسی نوشتن (Modify) برای کاربر IIS (پیش‌فرض: `IIS_IUSRS` یا
   Application Pool Identity مثل `IIS AppPool\NegahmMedia`) را روی این
   پوشه‌ها بدهید:
   - `storage/`
   - `bootstrap/cache/`
   - `database/` (برای نوشتن روی فایل SQLite)
   - `public/uploads/` (برای آپلود لوگو، تصاویر و فونت از پنل مدیریت)
8. در **IIS Manager** یک سایت/اپلیکیشن جدید بسازید و **Physical Path** آن
   را مستقیماً روی پوشه‌ی `public/` همین پروژه بگذارید (نه روی ریشه‌ی
   پروژه). این روش استاندارد و امن‌ترین حالت است چون بقیه‌ی پوشه‌ها
   (`app/`, `.env`, `database/` و…) اصلاً از بیرون در دسترس نیستند.
   - اگر کنترل‌پنل هاست شما اجازه نمی‌دهد Physical Path را روی `public/`
     بگذارید و مجبورید ریشه‌ی سایت همان پوشه‌ی پروژه باشد، از
     `web.config` موجود در ریشه‌ی پروژه استفاده کنید (تمام درخواست‌ها را
     به‌صورت داخلی به `public/` هدایت می‌کند) — اما ترجیح همیشه گزینه‌ی
     بالاست.
9. `APP_DEBUG=false` و `APP_ENV=production` را در `.env` تنظیم کنید.
10. سایت را باز کنید و از مسیر `/dashbord/app/login` با نام کاربری و
    رمز عبور مدیر (پیش‌فرض `Mohusyn` / `Smosh1387`، مگر در `.env` تغییر
    داده باشید) وارد پنل شوید.

## نکات مهم امنیتی

- حتماً `.env` را از دسترس عمومی خارج نگه دارید (در `public/web.config`
  این مسیر مسدود شده، اما اگر Physical Path را روی ریشه‌ی پروژه گذاشتید
  حتماً از `web.config` ریشه هم استفاده کنید).
- پس از اولین ورود، از بخش «تنظیمات سایت → رمز عبور» در پنل، رمز پیش‌فرض
  را حتماً تغییر دهید.
- برای HTTPS از IIS binding با گواهی SSL استفاده کنید و `APP_URL` را با
  `https://` در `.env` تنظیم کنید.

## به‌روزرسانی بعدی سایت (آپلود نسخه‌ی جدید)

فقط کافیست فایل‌های پروژه را جایگزین کنید (پوشه‌ی `storage/`,
`database/database.sqlite` و `public/uploads/` را دست نخورده نگه دارید)
و در صورت وجود مایگریشن جدید:
```
php artisan migrate
```
