import Database from "better-sqlite3";
import path from "path";
import fs from "fs";

const dataDir = path.join(process.cwd(), "data");
if (!fs.existsSync(dataDir)) fs.mkdirSync(dataDir, { recursive: true });

const dbPath = path.join(dataDir, "studio.db");

declare global {
  var __studioDb: Database.Database | undefined;
}

function createConnection() {
  const db = new Database(dbPath);
  db.pragma("journal_mode = WAL");
  db.pragma("foreign_keys = ON");
  return db;
}

export const db = global.__studioDb ?? createConnection();
if (process.env.NODE_ENV !== "production") global.__studioDb = db;

export function migrate() {
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
      id INTEGER PRIMARY KEY AUTOINCREMENT,
      family_name TEXT NOT NULL,
      weight TEXT NOT NULL DEFAULT '400',
      style TEXT NOT NULL DEFAULT 'normal',
      format TEXT NOT NULL,
      file_url TEXT NOT NULL,
      created_at TEXT NOT NULL DEFAULT (datetime('now'))
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
      created_at TEXT NOT NULL DEFAULT (datetime('now')),
      updated_at TEXT NOT NULL DEFAULT (datetime('now'))
    );

    CREATE TABLE IF NOT EXISTS categories (
      id INTEGER PRIMARY KEY AUTOINCREMENT,
      client_id INTEGER NOT NULL REFERENCES clients(id) ON DELETE CASCADE,
      title TEXT NOT NULL,
      slug TEXT NOT NULL,
      description TEXT DEFAULT '',
      cover_image_url TEXT DEFAULT '',
      order_index INTEGER NOT NULL DEFAULT 0,
      created_at TEXT NOT NULL DEFAULT (datetime('now')),
      UNIQUE(client_id, slug)
    );

    CREATE TABLE IF NOT EXISTS portfolio_items (
      id INTEGER PRIMARY KEY AUTOINCREMENT,
      category_id INTEGER NOT NULL REFERENCES categories(id) ON DELETE CASCADE,
      title TEXT DEFAULT '',
      description TEXT DEFAULT '',
      media_url TEXT NOT NULL,
      media_type TEXT NOT NULL DEFAULT 'image',
      order_index INTEGER NOT NULL DEFAULT 0,
      featured_home INTEGER NOT NULL DEFAULT 0,
      created_at TEXT NOT NULL DEFAULT (datetime('now'))
    );

    CREATE TABLE IF NOT EXISTS messages (
      id INTEGER PRIMARY KEY AUTOINCREMENT,
      name TEXT NOT NULL,
      email TEXT NOT NULL,
      phone TEXT DEFAULT '',
      subject TEXT DEFAULT '',
      message TEXT NOT NULL,
      is_read INTEGER NOT NULL DEFAULT 0,
      created_at TEXT NOT NULL DEFAULT (datetime('now'))
    );

    CREATE TABLE IF NOT EXISTS admin_users (
      id INTEGER PRIMARY KEY AUTOINCREMENT,
      username TEXT NOT NULL UNIQUE,
      password_hash TEXT NOT NULL,
      name TEXT DEFAULT '',
      created_at TEXT NOT NULL DEFAULT (datetime('now'))
    );
  `);

  const settingsRow = db.prepare("SELECT id FROM settings WHERE id = 1").get();
  if (!settingsRow) {
    db.prepare("INSERT INTO settings (id) VALUES (1)").run();
  }
}

migrate();
