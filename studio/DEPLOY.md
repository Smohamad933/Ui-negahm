# راهنمای انتشار روی IIS (ویندوز سرور)

این پروژه یک اپلیکیشن **Next.js (Node.js)** است. IIS به‌خودی‌خود Node را اجرا نمی‌کند، پس روش استاندارد و پایدار این است:

> **IIS به‌عنوان دروازه‌ی ورودی (reverse proxy) + Node.js که اپ را روی یک پورت داخلی (مثلاً 3000) اجرا می‌کند.**

این دقیقاً همون روشیه که مایکروسافت رسماً برای هاست اپ‌های Node روی IIS توصیه می‌کنه (با ماژول‌های ARR + URL Rewrite). دامنه، SSL و پورت ۸۰/۴۴۳ کاملاً دست IIS می‌مونه.

---

## پیش‌نیازها روی سرور ویندوز

1. **IIS** نصب و فعال باشد (Server Manager → Add Roles → Web Server IIS).
2. **Node.js نسخه ۲۲ یا بالاتر (LTS)** را از [nodejs.org](https://nodejs.org) نصب کن.
3. دو ماژول IIS زیر را نصب کن (از Microsoft IIS Downloads):
   - **URL Rewrite Module** → https://www.iis.net/downloads/microsoft/url-rewrite
   - **Application Request Routing (ARR)** → https://www.iis.net/downloads/microsoft/application-request-routing
4. یک ابزار برای اجرای دائمی Node به‌عنوان سرویس ویندوزی — پیشنهاد: **NSSM** (Non-Sucking Service Manager) → https://nssm.cc/download
   (جایگزین: PM2 + pm2-windows-startup)

---

## مرحله ۱ — کپی پروژه روی سرور

پوشه‌ی پروژه را (بدون `node_modules` و `.next` و `data`) روی سرور کپی کن، مثلاً در:

```
C:\inetpub\negaham-studio\
```

## مرحله ۲ — نصب و بیلد

داخل پوشه‌ی پروژه، در **Command Prompt یا PowerShell روی خود سرور** (نه از جای دیگه کپی نکن، چون `better-sqlite3` باینری مخصوص ویندوز نیاز داره که با اجرای `npm install` روی خود ویندوز به‌صورت خودکار دانلود می‌شود):

```powershell
cd C:\inetpub\negaham-studio
npm install
npm run build
```

## مرحله ۳ — تنظیم متغیرهای محیطی

یک فایل `.env` در ریشه‌ی پروژه بساز:

```env
SESSION_SECRET=یک-رشته-طولانی-و-تصادفی-منحصربه‌فرد-بساز
ADMIN_USERNAME=Mohusyn
ADMIN_PASSWORD=یک-رمز-قوی-اینجا-بگذار
PORT=3000
NODE_ENV=production
```

> `SESSION_SECRET` را حتماً عوض کن (مثلاً یک رشته‌ی ۴۰ کاراکتری تصادفی). این برای امضای کوکی ورود ادمین استفاده می‌شود.

## مرحله ۴ — اجرای دائمی اپ با NSSM (به‌عنوان Windows Service)

```powershell
nssm install NegahamStudio
```

در پنجره‌ای که باز می‌شود:
- **Path:** مسیر `node.exe` (مثلاً `C:\Program Files\nodejs\node.exe`)
- **Startup directory:** `C:\inetpub\negaham-studio`
- **Arguments:** `node_modules\next\dist\bin\next start -p 3000`

سپس در تب **Environment** متغیرهای بالا (`SESSION_SECRET`, `ADMIN_USERNAME`, ...) را اضافه کن (هرکدام در یک خط، فرمت `KEY=VALUE`).

سرویس را استارت کن:

```powershell
nssm start NegahamStudio
```

حالا اپ روی `http://localhost:3000` روی خود سرور در حال اجراست (فعلاً فقط داخل خود سرور قابل‌دیدنه).

> جایگزین سریع‌تر برای تست (بدون Service دائمی): از همون پوشه `npm start` را دستی اجرا کن — ولی با بسته‌شدن ترمینال، سایت هم می‌خوابد. برای Production حتماً از NSSM یا PM2 استفاده کن.

## مرحله ۵ — تنظیم IIS به‌عنوان Reverse Proxy

1. در **IIS Manager**، روی نام سرور (گره بالایی، نه یک سایت خاص) کلیک کن.
2. **Application Request Routing Cache** را باز کن → از منوی سمت راست **Server Proxy Settings** → تیک **Enable proxy** را بزن → Apply.
3. یک سایت جدید در IIS بساز (یا از سایت پیش‌فرض استفاده کن):
   - Physical Path: هر پوشه‌ی خالی (مثلاً `C:\inetpub\negaham-proxy`) — این پوشه فقط محل `web.config` است.
   - Binding: دامنه‌ات را وصل کن (مثلاً `www.negaham-studio.ir`، پورت ۸۰ و بعداً ۴۴۳ برای SSL).
4. داخل همون پوشه‌ی Physical Path، یک فایل به‌نام **`web.config`** با این محتوا بساز:

```xml
<?xml version="1.0" encoding="UTF-8"?>
<configuration>
  <system.webServer>
    <rewrite>
      <rules>
        <rule name="ReverseProxyToNode" stopProcessing="true">
          <match url="(.*)" />
          <action type="Rewrite" url="http://127.0.0.1:3000/{R:1}" />
        </rule>
      </rules>
    </rewrite>
    <!-- اجازه بده حجم بالاتر برای آپلود عکس/فونت رد بشه -->
    <security>
      <requestFiltering>
        <requestLimits maxAllowedContentLength="52428800" /> <!-- 50MB -->
      </requestFiltering>
    </security>
  </system.webServer>
</configuration>
```

5. سایت را Restart کن. حالا با باز کردن دامنه‌ات، درخواست‌ها از IIS به Node روی پورت 3000 پاس داده می‌شن.

## مرحله ۶ — SSL (HTTPS)

گواهی SSL را (خریداری‌شده یا رایگان با [win-acme](https://www.win-acme.com/) برای Let's Encrypt) از طریق **IIS Manager → Bindings → Add → https** به همون سایت وصل کن. IIS خودش TLS رو مدیریت می‌کنه و به Node به‌صورت HTTP ساده روی لوکال‌هاست وصل می‌شه — نیازی به تغییر در کد نیست.

## به‌روزرسانی سایت در آینده

```powershell
cd C:\inetpub\negaham-studio
# کد جدید رو جایگزین کن (بدون دست‌زدن به پوشه data)
npm install
npm run build
nssm restart NegahamStudio
```

## نکات مهم امنیتی و نگهداری

- **حتماً از پوشه‌ی `data\` (شامل `studio.db` و تمام تصاویر/فونت‌های آپلودی) به‌صورت مرتب بکاپ بگیر.** این پوشه تنها منبع اطلاعات زنده‌ی سایته و در گیت نیست.
- پورت 3000 را در فایروال سرور به بیرون باز نکن — فقط IIS (پورت 80/443) باید از بیرون در دسترس باشد؛ Node فقط باید روی `127.0.0.1` گوش بدهد.
- رمز ادمین پیش‌فرض را در اولین ورود از داخل پنل (تنظیمات → امنیت حساب) عوض کن.
- آدرس ورود پنل مدیریت `/dashbord/app` است (مثلاً `https://yourdomain.com/dashbord/app`).
- `SESSION_SECRET` را برای هر محیط (توسعه/production) متفاوت و محرمانه نگه‌دار.
