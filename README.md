# نگاه مدیا (negahm.ir) — وب‌سایت و پنل مدیریت

وب‌سایت شرکتی و پنل مدیریت آژانس تبلیغاتی «نگاه مدیا»، ساخته‌شده با
**Laravel + SQLite** برای اجرای بومی روی **IIS** (بدون نیاز به Node.js یا
iisnode در سرور واقعی).

> نسخه‌ی قبلی این پروژه با Next.js/Node.js ساخته شده بود و به‌طور کامل با
> این نسخه‌ی PHP/Laravel جایگزین شده است.

کد اصلی پروژه داخل پوشه‌ی [`laravel-app/`](./laravel-app) قرار دارد.

- راه‌اندازی محلی و مستندات کامل پروژه: [`laravel-app/README.md`](./laravel-app/README.md)
- راهنمای استقرار روی سرور IIS: [`laravel-app/DEPLOY.md`](./laravel-app/DEPLOY.md)

## راه‌اندازی سریع (محلی)

```bash
cd laravel-app
composer install
cp .env.example .env
php artisan key:generate
touch database/database.sqlite
php artisan migrate --seed
php artisan serve
```

- سایت عمومی: `http://127.0.0.1:8000`
- پنل مدیریت: `http://127.0.0.1:8000/dashbord/app/login` (نام کاربری
  `Mohusyn`، رمز عبور `Smosh1387` — پیش‌فرض، قابل تغییر)
