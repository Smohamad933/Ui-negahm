/* Seed real "نگاه مدیا" (negahm.ir) content for local preview / first run. Idempotent: skips if clients already exist. */
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
  hero_subtitle TEXT NOT NULL DEFAULT 'از ایده تا اجرا؛ هویت بصری، تولید محتوا، کمپین و دیجیتال مارکتینگ را یکپارچه می‌سازیم تا برند شما فقط دیده نشود، بلکه در ذهن بماند.',
  hero_cta_text TEXT NOT NULL DEFAULT 'دیدن نمونه‌کارها',
  hero_cta_link TEXT NOT NULL DEFAULT '/clients',
  hero_media_url TEXT DEFAULT '',
  about_title TEXT NOT NULL DEFAULT 'ما فقط تبلیغ نمی‌کنیم؛ روایت می‌سازیم.',
  about_body TEXT NOT NULL DEFAULT 'هر پروژه برای ما یک روایت است؛ از لحظه‌ای که مسئله برند را می‌شناسیم تا لحظه‌ای که مخاطب با آن روبه‌رو می‌شود. تیم نگاه مدیا این مسیر را یکپارچه پیش می‌برد تا خروجی، منسجم و ماندگار باشد.',
  about_image_url TEXT DEFAULT '',
  contact_address TEXT DEFAULT 'تهران | اهواز',
  contact_phone TEXT DEFAULT '۰۹۰۱۲۳۱۹۸۷۹',
  contact_email TEXT DEFAULT 'negahminfo@gmail.com',
  contact_map_embed TEXT DEFAULT '',
  social_instagram TEXT DEFAULT '',
  social_telegram TEXT DEFAULT '',
  social_whatsapp TEXT DEFAULT '',
  social_linkedin TEXT DEFAULT '',
  footer_text TEXT NOT NULL DEFAULT 'نگاه مدیا — تمامی حقوق محفوظ است.',
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
  aspect_ratio TEXT NOT NULL DEFAULT '16:9',
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

function slugify(input) {
  const slug = input
    .trim()
    .toLowerCase()
    .replace(/[\u200c\s]+/g, "-")
    .replace(/[^a-z0-9\u0600-\u06FF-]+/g, "")
    .replace(/-+/g, "-")
    .replace(/^-+|-+$/g, "");
  return slug || `item-${Date.now().toString(36)}`;
}

const insertClient = db.prepare(`
  INSERT INTO clients (slug, name, logo_url, cover_image_url, accent_color, short_description, industry, website_url, year, featured, published, order_index)
  VALUES (@slug, @name, '', @cover_image_url, @accent_color, @short_description, @industry, '', @year, @featured, 1, @order_index)
`);
const insertCategory = db.prepare(`
  INSERT INTO categories (client_id, title, slug, description, cover_image_url, aspect_ratio, order_index)
  VALUES (@client_id, @title, @slug, @description, @cover_image_url, @aspect_ratio, @order_index)
`);
const insertItem = db.prepare(`
  INSERT INTO portfolio_items (category_id, title, description, media_url, media_type, featured_home, order_index)
  VALUES (@category_id, @title, @description, @media_url, 'image', @featured_home, @order_index)
`);

// ---- Real "همراهان نگاه مدیا" — 34 clients across 3 real collaboration domains ----
const IND_BUSINESS = "کسب‌وکار و بیزینس";
const IND_GOV = "دولتی و نهادی";
const IND_CULTURE = "آموزشی و فرهنگی";

const ACCENTS = ["#ff3d74", "#2f6fff", "#ffc629", "#8c5cff", "#29d6a8", "#ff7a30"];
function accentFor(i) {
  return ACCENTS[i % ACCENTS.length];
}

const clientsData = [
  // ---------------- کسب‌وکار و بیزینس ----------------
  {
    name: "گالری طلا و جواهر محمود",
    industry: IND_BUSINESS,
    desc: "خرده‌فروشی و فروش آنلاین طلا و جواهرات دست‌ساز.",
    year: "۱۴۰۳",
    featured: 1,
    cover: "/seed-demo/jewelry-square.jpg",
    categories: [
      {
        title: "کاتالوگ محصول",
        desc: "عکاسی استودیویی و کاتالوگ آنلاین محصولات طلا و جواهر.",
        ratio: "1:1",
        cover: "/seed-demo/jewelry-square.jpg",
        items: ["/seed-demo/jewelry-square.jpg", "/seed-demo/jewelry-square.jpg", "/seed-demo/jewelry-square.jpg"],
      },
    ],
  },
  {
    name: "رنس تکس",
    industry: IND_BUSINESS,
    desc: "تولید و عرضه پوشاک و منسوجات.",
    year: "۱۴۰۲",
    featured: 0,
    cover: "",
    categories: [],
  },
  {
    name: "رویان شبکه",
    industry: IND_BUSINESS,
    desc: "خدمات اینترنت و زیرساخت شبکه.",
    year: "۱۴۰۲",
    featured: 1,
    cover: "/seed-demo/client-tech.jpg",
    categories: [
      { title: "طراحی سایت", desc: "طراحی و توسعه وب‌سایت شرکتی.", ratio: "16:9", cover: "/seed-demo/website-design-1.jpg", items: ["/seed-demo/website-design-1.jpg"] },
      { title: "شبکه‌های اجتماعی", desc: "تولید محتوای مربع برای اینستاگرام.", ratio: "1:1", cover: "/seed-demo/client-tech.jpg", items: ["/seed-demo/client-tech.jpg", "/seed-demo/client-tech.jpg"] },
    ],
  },
  { name: "الدراگ استور", industry: IND_BUSINESS, desc: "فروشگاه آنلاین محصولات آرایشی و بهداشتی.", year: "۱۴۰۳", featured: 0, cover: "", categories: [] },
  { name: "گالری نقره سیده راد", industry: IND_BUSINESS, desc: "طراحی و فروش زیورآلات نقره.", year: "۱۴۰۱", featured: 0, cover: "", categories: [] },
  { name: "گالری جواهرات هم‌نفس", industry: IND_BUSINESS, desc: "گالری تخصصی جواهرات و سنگ‌های قیمتی.", year: "۱۴۰۲", featured: 0, cover: "", categories: [] },
  { name: "طلا و جواهرات محمد سیاوشی", industry: IND_BUSINESS, desc: "تولید و فروش طلا و جواهرات.", year: "۱۴۰۱", featured: 0, cover: "", categories: [] },
  { name: "مجموعه نظریان", industry: IND_BUSINESS, desc: "مجموعه تجاری چندمنظوره.", year: "۱۴۰۲", featured: 0, cover: "", categories: [] },
  { name: "ابزار آلات قشقایی", industry: IND_BUSINESS, desc: "فروش ابزارآلات صنعتی و ساختمانی.", year: "۱۴۰۱", featured: 0, cover: "", categories: [] },
  {
    name: "آژانس تبلیغاتی لامیلا",
    industry: IND_BUSINESS,
    desc: "آژانس خلاق در حوزه تبلیغات و برندینگ.",
    year: "۱۴۰۳",
    featured: 1,
    cover: "/seed-demo/agency-reels.jpg",
    categories: [
      { title: "ریلز و استوری", desc: "تولید محتوای ویدیویی عمودی برای شبکه‌های اجتماعی.", ratio: "9:16", cover: "/seed-demo/agency-reels.jpg", items: ["/seed-demo/agency-reels.jpg", "/seed-demo/agency-reels.jpg", "/seed-demo/agency-reels.jpg"] },
      { title: "کمپین دیجیتال", desc: "کمپین تبلیغاتی چندکاناله.", ratio: "16:9", cover: "/seed-demo/campaign-1.jpg", items: ["/seed-demo/campaign-1.jpg", "/seed-demo/website-design-1.jpg"] },
      { title: "پست‌های اینستاگرام", desc: "طراحی گرافیک پست‌های مربعی.", ratio: "1:1", cover: "/seed-demo/festival-square.jpg", items: ["/seed-demo/festival-square.jpg", "/seed-demo/festival-square.jpg", "/seed-demo/festival-square.jpg"] },
    ],
  },
  { name: "کلینیک مشاوره کودک و نوجوان بهشت زندگی", industry: IND_BUSINESS, desc: "مرکز مشاوره و روان‌شناسی کودک و نوجوان.", year: "۱۴۰۳", featured: 0, cover: "", categories: [] },
  { name: "استودیو هور", industry: IND_BUSINESS, desc: "استودیوی عکاسی و تولید محتوا.", year: "۱۴۰۲", featured: 0, cover: "", categories: [] },

  // ---------------- دولتی و نهادی ----------------
  { name: "وزارت علوم، تحقیقات و فناوری", industry: IND_GOV, desc: "نهاد سیاست‌گذار آموزش عالی و پژوهش کشور.", year: "۱۴۰۲", featured: 0, cover: "", categories: [] },
  {
    name: "دانشگاه شهید چمران اهواز",
    industry: IND_GOV,
    desc: "دانشگاه دولتی در جنوب‌غرب کشور.",
    year: "۱۴۰۳",
    featured: 1,
    cover: "/seed-demo/university-campaign.jpg",
    categories: [
      {
        title: "کمپین معرفی دانشگاه",
        desc: "کمپین تبلیغاتی معرفی رشته‌ها و فضای دانشگاه.",
        ratio: "16:9",
        cover: "/seed-demo/university-campaign.jpg",
        items: ["/seed-demo/university-campaign.jpg", "/seed-demo/about.jpg", "/seed-demo/university-campaign.jpg"],
      },
    ],
  },
  {
    name: "استانداری خوزستان",
    industry: IND_GOV,
    desc: "نهاد اجرایی استان خوزستان.",
    year: "۱۴۰۲",
    featured: 1,
    cover: "/seed-demo/gov-banner.jpg",
    categories: [
      {
        title: "کمپین اطلاع‌رسانی",
        desc: "کمپین اطلاع‌رسانی و تولید محتوای استانی.",
        ratio: "16:9",
        cover: "/seed-demo/gov-banner.jpg",
        items: [
          "/seed-demo/gov-banner.jpg",
          "/seed-demo/gov-banner.jpg",
          "/seed-demo/website-design-1.jpg",
          "/seed-demo/campaign-1.jpg",
          "/seed-demo/gov-banner.jpg",
        ],
      },
    ],
  },
  { name: "استانداری هرمزگان", industry: IND_GOV, desc: "نهاد اجرایی استان هرمزگان.", year: "۱۴۰۱", featured: 0, cover: "", categories: [] },
  { name: "شهرداری اهواز", industry: IND_GOV, desc: "مدیریت شهری کلان‌شهر اهواز.", year: "۱۴۰۲", featured: 0, cover: "", categories: [] },
  { name: "صداوسیمای مرکز خوزستان", industry: IND_GOV, desc: "رسانه ملی، مرکز استان خوزستان.", year: "۱۴۰۱", featured: 0, cover: "", categories: [] },
  { name: "سازمان تبلیغات استان خوزستان", industry: IND_GOV, desc: "نهاد فرهنگی-تبلیغی استان.", year: "۱۴۰۲", featured: 0, cover: "", categories: [] },
  { name: "مرکز رسانه استان خوزستان", industry: IND_GOV, desc: "مرکز هماهنگی رسانه‌ای استان.", year: "۱۴۰۲", featured: 0, cover: "", categories: [] },
  { name: "دفتر امام جمعه اهواز", industry: IND_GOV, desc: "دفتر نهاد دینی و فرهنگی اهواز.", year: "۱۴۰۱", featured: 0, cover: "", categories: [] },
  { name: "پایگاه میراث جهانی سازه‌های آبی شوشتر", industry: IND_GOV, desc: "پایگاه ثبت‌شده میراث جهانی یونسکو.", year: "۱۴۰۳", featured: 0, cover: "", categories: [] },
  { name: "انجمن خیریه ۱۴ معصوم", industry: IND_GOV, desc: "نهاد خیریه و حمایتی مردمی.", year: "۱۴۰۲", featured: 0, cover: "", categories: [] },
  { name: "مشاوران افق دانش ثریا", industry: IND_GOV, desc: "موسسه مشاوره تحصیلی و آموزشی.", year: "۱۴۰۳", featured: 0, cover: "", categories: [] },

  // ---------------- آموزشی و فرهنگی ----------------
  { name: "چمران پلاس", industry: IND_CULTURE, desc: "رسانه و پلتفرم محتوایی دانشگاهی.", year: "۱۴۰۳", featured: 0, cover: "", categories: [] },
  { name: "رسانه پرواز", industry: IND_CULTURE, desc: "رسانه محتوایی و خبری.", year: "۱۴۰۲", featured: 0, cover: "", categories: [] },
  {
    name: "جشنواره ملی رویش",
    industry: IND_CULTURE,
    desc: "جشنواره ملی فرهنگی و هنری.",
    year: "۱۴۰۳",
    featured: 1,
    cover: "/seed-demo/festival-square.jpg",
    categories: [
      { title: "پوستر و هویت بصری", desc: "طراحی پوستر و هویت بصری جشنواره.", ratio: "1:1", cover: "/seed-demo/festival-square.jpg", items: ["/seed-demo/festival-square.jpg", "/seed-demo/festival-square.jpg"] },
      { title: "کمپین تبلیغاتی", desc: "کمپین معرفی و اطلاع‌رسانی جشنواره.", ratio: "16:9", cover: "/seed-demo/campaign-1.jpg", items: ["/seed-demo/campaign-1.jpg"] },
    ],
  },
  {
    name: "کنسرت علیرضا قربانی",
    industry: IND_CULTURE,
    desc: "اجرای زنده موسیقی سنتی ایرانی.",
    year: "۱۴۰۴",
    featured: 1,
    cover: "/seed-demo/concert-poster.jpg",
    categories: [
      {
        title: "تیزر و ریلز کنسرت",
        desc: "تیزرهای عمودی معرفی کنسرت برای اینستاگرام.",
        ratio: "9:16",
        cover: "/seed-demo/concert-poster.jpg",
        items: [
          "/seed-demo/concert-poster.jpg",
          "/seed-demo/concert-poster.jpg",
          "/seed-demo/concert-poster.jpg",
          "/seed-demo/concert-poster.jpg",
          "/seed-demo/concert-poster.jpg",
        ],
      },
    ],
  },
  { name: "ارکستر سازهای ایرانی به یاد خالقی", industry: IND_CULTURE, desc: "ارکستر تخصصی سازهای ایرانی.", year: "۱۴۰۲", featured: 0, cover: "", categories: [] },
  { name: "جایزه ملی آهنگ‌سازی استاد روح‌الله خالقی", industry: IND_CULTURE, desc: "جایزه ملی در حوزه آهنگ‌سازی.", year: "۱۴۰۳", featured: 0, cover: "", categories: [] },
  { name: "خانه موسیقی تهران", industry: IND_CULTURE, desc: "نهاد صنفی و فرهنگی موسیقی.", year: "۱۴۰۱", featured: 0, cover: "", categories: [] },
  { name: "گروه موسیقی نی‌نوا", industry: IND_CULTURE, desc: "گروه اجرای موسیقی سنتی ایرانی.", year: "۱۴۰۲", featured: 0, cover: "", categories: [] },
  { name: "مجموعه سرودی‌های استان خوزستان", industry: IND_CULTURE, desc: "تولید و اجرای سرودهای آیینی و ملی.", year: "۱۴۰۱", featured: 0, cover: "", categories: [] },
  { name: "موسسه برتینا", industry: IND_CULTURE, desc: "موسسه فرهنگی و آموزشی.", year: "۱۴۰۲", featured: 0, cover: "", categories: [] },
];

let order = 0;
for (const c of clientsData) {
  const slug = slugify(c.name);
  const clientId = insertClient.run({
    slug,
    name: c.name,
    cover_image_url: c.cover,
    accent_color: accentFor(order),
    short_description: c.desc,
    industry: c.industry,
    year: c.year,
    featured: c.featured,
    order_index: order++,
  }).lastInsertRowid;

  let catOrder = 0;
  for (const cat of c.categories) {
    const catSlug = slugify(cat.title) || `cat-${catOrder}`;
    const categoryId = insertCategory.run({
      client_id: clientId,
      title: cat.title,
      slug: catSlug,
      description: cat.desc,
      cover_image_url: cat.cover,
      aspect_ratio: cat.ratio,
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

console.log(`[seed] ${clientsData.length} همراه (کارفرما) با موفقیت seed شدند.`);
