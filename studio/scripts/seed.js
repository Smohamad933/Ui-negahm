/* Seed demo content for local preview / first run. Idempotent: skips if clients already exist. */
const path = require("path");
const Database = require("better-sqlite3");
const fs = require("fs");

const dataDir = path.join(process.cwd(), "data");
if (!fs.existsSync(dataDir)) fs.mkdirSync(dataDir, { recursive: true });
const db = new Database(path.join(dataDir, "studio.db"));
db.pragma("foreign_keys = ON");

// Ensure schema exists (mirrors src/lib/db.ts)
db.exec(`
CREATE TABLE IF NOT EXISTS settings (
  id INTEGER PRIMARY KEY CHECK (id = 1),
  site_name TEXT NOT NULL DEFAULT 'استودیو نگاهم',
  tagline TEXT NOT NULL DEFAULT 'استودیوی خلاقیت و تبلیغات',
  logo_url TEXT DEFAULT '',
  favicon_url TEXT DEFAULT '',
  color_bg TEXT NOT NULL DEFAULT '#0a0a0a',
  color_fg TEXT NOT NULL DEFAULT '#f5f3ee',
  color_primary TEXT NOT NULL DEFAULT '#d9ff3f',
  color_secondary TEXT NOT NULL DEFAULT '#7a5cff',
  color_accent TEXT NOT NULL DEFAULT '#ff5b3d',
  color_muted TEXT NOT NULL DEFAULT '#8a8a86',
  font_family TEXT NOT NULL DEFAULT 'Vazirmatn Variable',
  hero_title TEXT NOT NULL DEFAULT 'ما ایده‌ها را به تجربه تبدیل می‌کنیم',
  hero_subtitle TEXT NOT NULL DEFAULT 'استودیو تبلیغاتی نگاهم؛ برندسازی، کمپین و طراحی دیجیتال',
  hero_cta_text TEXT NOT NULL DEFAULT 'دیدن نمونه‌کارها',
  hero_cta_link TEXT NOT NULL DEFAULT '/clients',
  hero_media_url TEXT DEFAULT '',
  about_title TEXT NOT NULL DEFAULT 'درباره ما',
  about_body TEXT NOT NULL DEFAULT 'استودیو نگاهم یک تیم خلاق در حوزه تبلیغات، برندینگ و طراحی دیجیتال است.',
  about_image_url TEXT DEFAULT '',
  contact_address TEXT DEFAULT 'تهران، ایران',
  contact_phone TEXT DEFAULT '۰۲۱-۰۰۰۰۰۰۰',
  contact_email TEXT DEFAULT 'info@negaham.studio',
  contact_map_embed TEXT DEFAULT '',
  social_instagram TEXT DEFAULT '',
  social_telegram TEXT DEFAULT '',
  social_whatsapp TEXT DEFAULT '',
  social_linkedin TEXT DEFAULT '',
  footer_text TEXT NOT NULL DEFAULT 'استودیو تبلیغاتی نگاهم — تمامی حقوق محفوظ است.',
  updated_at TEXT NOT NULL DEFAULT (datetime('now'))
);
CREATE TABLE IF NOT EXISTS fonts (
  id INTEGER PRIMARY KEY AUTOINCREMENT, family_name TEXT NOT NULL, weight TEXT NOT NULL DEFAULT '400',
  style TEXT NOT NULL DEFAULT 'normal', format TEXT NOT NULL, file_url TEXT NOT NULL,
  created_at TEXT NOT NULL DEFAULT (datetime('now'))
);
CREATE TABLE IF NOT EXISTS clients (
  id INTEGER PRIMARY KEY AUTOINCREMENT, slug TEXT NOT NULL UNIQUE, name TEXT NOT NULL,
  logo_url TEXT DEFAULT '', cover_image_url TEXT DEFAULT '', accent_color TEXT DEFAULT '',
  short_description TEXT DEFAULT '', industry TEXT DEFAULT '', website_url TEXT DEFAULT '', year TEXT DEFAULT '',
  featured INTEGER NOT NULL DEFAULT 0, published INTEGER NOT NULL DEFAULT 1, order_index INTEGER NOT NULL DEFAULT 0,
  created_at TEXT NOT NULL DEFAULT (datetime('now')), updated_at TEXT NOT NULL DEFAULT (datetime('now'))
);
CREATE TABLE IF NOT EXISTS categories (
  id INTEGER PRIMARY KEY AUTOINCREMENT, client_id INTEGER NOT NULL REFERENCES clients(id) ON DELETE CASCADE,
  title TEXT NOT NULL, slug TEXT NOT NULL, description TEXT DEFAULT '', cover_image_url TEXT DEFAULT '',
  order_index INTEGER NOT NULL DEFAULT 0, created_at TEXT NOT NULL DEFAULT (datetime('now')), UNIQUE(client_id, slug)
);
CREATE TABLE IF NOT EXISTS portfolio_items (
  id INTEGER PRIMARY KEY AUTOINCREMENT, category_id INTEGER NOT NULL REFERENCES categories(id) ON DELETE CASCADE,
  title TEXT DEFAULT '', description TEXT DEFAULT '', media_url TEXT NOT NULL, media_type TEXT NOT NULL DEFAULT 'image',
  order_index INTEGER NOT NULL DEFAULT 0, featured_home INTEGER NOT NULL DEFAULT 0, created_at TEXT NOT NULL DEFAULT (datetime('now'))
);
CREATE TABLE IF NOT EXISTS messages (
  id INTEGER PRIMARY KEY AUTOINCREMENT, name TEXT NOT NULL, email TEXT NOT NULL, phone TEXT DEFAULT '',
  subject TEXT DEFAULT '', message TEXT NOT NULL, is_read INTEGER NOT NULL DEFAULT 0, created_at TEXT NOT NULL DEFAULT (datetime('now'))
);
CREATE TABLE IF NOT EXISTS admin_users (
  id INTEGER PRIMARY KEY AUTOINCREMENT, username TEXT NOT NULL UNIQUE, password_hash TEXT NOT NULL, name TEXT DEFAULT '',
  created_at TEXT NOT NULL DEFAULT (datetime('now'))
);
`);

if (!db.prepare("SELECT id FROM settings WHERE id = 1").get()) {
  db.prepare("INSERT INTO settings (id) VALUES (1)").run();
}

const clientCount = db.prepare("SELECT COUNT(*) as c FROM clients").get().c;
if (clientCount > 0) {
  console.log("[seed] Clients already exist, skipping demo seed.");
  process.exit(0);
}

db.prepare(
  "UPDATE settings SET about_image_url = ? WHERE id = 1"
).run("/seed-demo/about.jpg");

const insertClient = db.prepare(`
  INSERT INTO clients (slug, name, logo_url, cover_image_url, accent_color, short_description, industry, website_url, year, featured, published, order_index)
  VALUES (@slug, @name, '', @cover_image_url, @accent_color, @short_description, @industry, @website_url, @year, @featured, 1, @order_index)
`);
const insertCategory = db.prepare(`
  INSERT INTO categories (client_id, title, slug, description, cover_image_url, order_index)
  VALUES (@client_id, @title, @slug, @description, @cover_image_url, @order_index)
`);
const insertItem = db.prepare(`
  INSERT INTO portfolio_items (category_id, title, description, media_url, media_type, featured_home, order_index)
  VALUES (@category_id, @title, @description, @media_url, 'image', @featured_home, @order_index)
`);

const clientsData = [
  {
    slug: "bank-ayande",
    name: "بانک آینده",
    cover: "/seed-demo/client-fintech.jpg",
    accent: "#7a5cff",
    industry: "فینتک و بانکداری",
    desc: "طراحی هویت دیجیتال و اجرای کمپین بازآفرینی برند برای یکی از بانک‌های پیشرو کشور.",
    website: "https://example.com",
    year: "۱۴۰۳",
    featured: 1,
    categories: [
      { title: "کمپین تبلیغاتی", desc: "کمپین ۳۶۰ درجه لانچ اپلیکیشن بانکداری دیجیتال.", cover: "/seed-demo/campaign-1.jpg", items: ["/seed-demo/campaign-1.jpg", "/seed-demo/client-fintech.jpg"] },
      { title: "طراحی سایت", desc: "طراحی و توسعه وب‌سایت جدید با تجربه کاربری بازطراحی‌شده.", cover: "/seed-demo/website-design-1.jpg", items: ["/seed-demo/website-design-1.jpg"] },
    ],
  },
  {
    slug: "modo-fashion",
    name: "مدو فشن",
    cover: "/seed-demo/client-fashion.jpg",
    accent: "#ff5b3d",
    industry: "پوشاک و مد",
    desc: "استراتژی برند و تولید محتوای بصری برای کالکشن پاییزه برند مدو.",
    website: "https://example.com",
    year: "۱۴۰۴",
    featured: 1,
    categories: [
      { title: "کمپین تبلیغاتی", desc: "کمپین معرفی کالکشن جدید در فضای دیجیتال.", cover: "/seed-demo/client-fashion.jpg", items: ["/seed-demo/client-fashion.jpg"] },
      { title: "عکاسی محصول", desc: "عکاسی استودیویی و تولید محتوا برای شبکه‌های اجتماعی.", cover: "", items: [] },
    ],
  },
  {
    slug: "cafe-lamiz",
    name: "کافه لمیز",
    cover: "/seed-demo/client-food.jpg",
    accent: "#d9ff3f",
    industry: "غذا و نوشیدنی",
    desc: "بازطراحی هویت بصری و مدیریت شبکه‌های اجتماعی کافه لمیز.",
    website: "",
    year: "۱۴۰۳",
    featured: 1,
    categories: [
      { title: "هویت بصری", desc: "طراحی لوگو، بسته‌بندی و منو.", cover: "/seed-demo/client-food.jpg", items: ["/seed-demo/client-food.jpg"] },
      { title: "شبکه‌های اجتماعی", desc: "تولید محتوای ماهانه اینستاگرام.", cover: "", items: [] },
    ],
  },
  {
    slug: "nabz-tech",
    name: "نبض‌تک",
    cover: "/seed-demo/client-tech.jpg",
    accent: "#7a5cff",
    industry: "استارتاپ فناوری",
    desc: "طراحی اپلیکیشن، هویت بصری و کمپین لانچ برای استارتاپ نبض‌تک.",
    website: "https://example.com",
    year: "۱۴۰۴",
    featured: 1,
    categories: [
      { title: "طراحی اپلیکیشن", desc: "طراحی رابط کاربری اپلیکیشن موبایل.", cover: "/seed-demo/client-tech.jpg", items: ["/seed-demo/client-tech.jpg"] },
      { title: "کمپین لانچ", desc: "کمپین معرفی محصول به بازار.", cover: "/seed-demo/campaign-1.jpg", items: ["/seed-demo/campaign-1.jpg"] },
    ],
  },
  {
    slug: "sabzine-market",
    name: "سبزینه مارکت",
    cover: "",
    accent: "#37d399",
    industry: "خرده‌فروشی آنلاین",
    desc: "طراحی سایت فروشگاهی و اجرای کمپین تخفیف فصلی.",
    website: "",
    year: "۱۴۰۲",
    featured: 0,
    categories: [{ title: "طراحی سایت", desc: "فروشگاه اینترنتی محصولات ارگانیک.", cover: "/seed-demo/website-design-1.jpg", items: ["/seed-demo/website-design-1.jpg"] }],
  },
  {
    slug: "poya-insurance",
    name: "بیمه پویا",
    cover: "",
    accent: "#7a5cff",
    industry: "بیمه",
    desc: "بازطراحی هویت بصری و کمپین آگاهی‌بخشی بیمه پویا.",
    website: "",
    year: "۱۴۰۲",
    featured: 0,
    categories: [{ title: "کمپین تبلیغاتی", desc: "کمپین محیطی و دیجیتال.", cover: "", items: [] }],
  },
  {
    slug: "arad-motors",
    name: "آراد موتورز",
    cover: "",
    accent: "#ff5b3d",
    industry: "خودرو",
    desc: "تولید محتوای ویدیویی و کمپین معرفی خودرو جدید.",
    website: "",
    year: "۱۴۰۳",
    featured: 0,
    categories: [{ title: "تولید محتوا", desc: "فیلم‌برداری تبلیغاتی و عکاسی محصول.", cover: "", items: [] }],
  },
];

let order = 0;
for (const c of clientsData) {
  const clientId = insertClient.run({
    slug: c.slug,
    name: c.name,
    cover_image_url: c.cover,
    accent_color: c.accent,
    short_description: c.desc,
    industry: c.industry,
    website_url: c.website,
    year: c.year,
    featured: c.featured,
    order_index: order++,
  }).lastInsertRowid;

  let catOrder = 0;
  for (const cat of c.categories) {
    const slug = cat.title
      .toLowerCase()
      .replace(/[^a-z0-9\u0600-\u06FF]+/g, "-")
      .replace(/^-+|-+$/g, "") || `cat-${catOrder}`;
    const categoryId = insertCategory.run({
      client_id: clientId,
      title: cat.title,
      slug,
      description: cat.desc,
      cover_image_url: cat.cover,
      order_index: catOrder++,
    }).lastInsertRowid;

    let itemOrder = 0;
    for (const img of cat.items) {
      insertItem.run({
        category_id: categoryId,
        title: `${cat.title} - ${c.name}`,
        description: cat.desc,
        media_url: img,
        featured_home: itemOrder === 0 ? 1 : 0,
        order_index: itemOrder++,
      });
    }
  }
}

const insertMessage = db.prepare(`
  INSERT INTO messages (name, email, phone, subject, message, is_read) VALUES (@name, @email, @phone, @subject, @message, @is_read)
`);
insertMessage.run({
  name: "سارا احمدی",
  email: "sara@example.com",
  phone: "۰۹۱۲۱۲۳۴۵۶۷",
  subject: "درخواست همکاری برای کمپین تبلیغاتی",
  message: "سلام، برای برند ما یه کمپین دیجیتال مارکتینگ نیاز داریم. لطفا باهام تماس بگیرید.",
  is_read: 0,
});
insertMessage.run({
  name: "علی محمدی",
  email: "ali@example.com",
  phone: "",
  subject: "طراحی سایت",
  message: "امکان طراحی سایت فروشگاهی برای کسب‌وکارمون هست؟ لطفا نمونه‌کار بفرستید.",
  is_read: 1,
});

console.log("[seed] Demo content seeded successfully.");
