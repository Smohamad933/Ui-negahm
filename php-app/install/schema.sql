-- ساختار جداول (معادل مایگریشن‌های نسخه‌ی Laravel قبلی، برای مرجع/بازسازی).
-- این فایل معمولاً لازم نیست دستی اجرا شود؛ data/app.sqlite از قبل با همین
-- ساختار ساخته و seed شده است. برای بازسازی از صفر: install/seed.php را ببینید.

CREATE TABLE IF NOT EXISTS admin_users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    username TEXT NOT NULL UNIQUE,
    password_hash TEXT NOT NULL,
    name TEXT NOT NULL DEFAULT '',
    created_at TEXT
);

CREATE TABLE IF NOT EXISTS settings (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    site_name TEXT NOT NULL DEFAULT 'نگاه مدیا',
    tagline TEXT NOT NULL DEFAULT 'آژانس خلاق و تبلیغاتی',
    logo_url TEXT DEFAULT '',
    favicon_url TEXT DEFAULT '',
    color_bg TEXT NOT NULL DEFAULT '#fff6e9',
    color_fg TEXT NOT NULL DEFAULT '#171310',
    color_primary TEXT NOT NULL DEFAULT '#ff3d74',
    color_secondary TEXT NOT NULL DEFAULT '#2f6fff',
    color_accent TEXT NOT NULL DEFAULT '#ffc629',
    color_muted TEXT NOT NULL DEFAULT '#8b8378',
    font_family TEXT NOT NULL DEFAULT 'Vazirmatn Variable',
    hero_title TEXT NOT NULL DEFAULT 'همه‌چیز در یک نگاه',
    hero_subtitle TEXT DEFAULT '',
    hero_cta_text TEXT DEFAULT 'دیدن نمونه‌کارها',
    hero_cta_link TEXT DEFAULT '/clients.php',
    hero_media_url TEXT DEFAULT '',
    about_title TEXT DEFAULT '',
    about_body TEXT DEFAULT '',
    about_image_url TEXT DEFAULT '',
    contact_address TEXT DEFAULT '',
    contact_phone TEXT DEFAULT '',
    contact_email TEXT DEFAULT '',
    contact_map_embed TEXT DEFAULT '',
    social_instagram TEXT DEFAULT '',
    social_telegram TEXT DEFAULT '',
    social_whatsapp TEXT DEFAULT '',
    social_linkedin TEXT DEFAULT '',
    footer_text TEXT DEFAULT 'نگاه مدیا — تمامی حقوق محفوظ است.',
    stat1_value TEXT DEFAULT '۳+',
    stat1_label TEXT DEFAULT 'سال تجربه',
    stat2_value TEXT DEFAULT '۴۰+',
    stat2_label TEXT DEFAULT 'پروژه اجراشده',
    stat3_value TEXT DEFAULT '۱۲+',
    stat3_label TEXT DEFAULT 'برند همراه',
    updated_at TEXT
);

CREATE TABLE IF NOT EXISTS fonts (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    family_name TEXT NOT NULL,
    weight TEXT NOT NULL DEFAULT '400',
    style TEXT NOT NULL DEFAULT 'normal',
    format TEXT NOT NULL,
    file_url TEXT NOT NULL,
    created_at TEXT
);

CREATE TABLE IF NOT EXISTS clients (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    slug TEXT NOT NULL UNIQUE,
    name TEXT NOT NULL,
    logo_url TEXT DEFAULT '',
    cover_image_url TEXT DEFAULT '',
    accent_color TEXT DEFAULT '',
    short_description TEXT DEFAULT '',
    industry TEXT DEFAULT '',
    website_url TEXT DEFAULT '',
    year TEXT DEFAULT '',
    featured INTEGER NOT NULL DEFAULT 0,
    published INTEGER NOT NULL DEFAULT 1,
    order_index INTEGER NOT NULL DEFAULT 0,
    created_at TEXT,
    updated_at TEXT
);

CREATE TABLE IF NOT EXISTS categories (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    client_id INTEGER NOT NULL REFERENCES clients (id) ON DELETE CASCADE,
    title TEXT NOT NULL,
    slug TEXT NOT NULL,
    description TEXT DEFAULT '',
    cover_image_url TEXT DEFAULT '',
    aspect_ratio TEXT NOT NULL DEFAULT '16:9',
    order_index INTEGER NOT NULL DEFAULT 0,
    created_at TEXT,
    UNIQUE (client_id, slug)
);

CREATE TABLE IF NOT EXISTS portfolio_items (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    category_id INTEGER NOT NULL REFERENCES categories (id) ON DELETE CASCADE,
    title TEXT DEFAULT '',
    description TEXT DEFAULT '',
    media_url TEXT NOT NULL,
    media_type TEXT NOT NULL DEFAULT 'image',
    order_index INTEGER NOT NULL DEFAULT 0,
    featured_home INTEGER NOT NULL DEFAULT 0,
    created_at TEXT
);

CREATE TABLE IF NOT EXISTS messages (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    email TEXT NOT NULL,
    phone TEXT DEFAULT '',
    subject TEXT DEFAULT '',
    message TEXT NOT NULL,
    is_read INTEGER NOT NULL DEFAULT 0,
    created_at TEXT
);

CREATE INDEX IF NOT EXISTS idx_categories_client ON categories (client_id);
CREATE INDEX IF NOT EXISTS idx_items_category ON portfolio_items (category_id);
