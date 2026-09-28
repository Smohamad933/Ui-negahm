import { db } from "./db";
import { deleteUploadedFile } from "./uploads";

export type Settings = {
  id: number;
  site_name: string;
  tagline: string;
  logo_url: string;
  favicon_url: string;
  color_bg: string;
  color_fg: string;
  color_primary: string;
  color_secondary: string;
  color_accent: string;
  color_muted: string;
  font_family: string;
  hero_title: string;
  hero_subtitle: string;
  hero_cta_text: string;
  hero_cta_link: string;
  hero_media_url: string;
  about_title: string;
  about_body: string;
  about_image_url: string;
  contact_address: string;
  contact_phone: string;
  contact_email: string;
  contact_map_embed: string;
  social_instagram: string;
  social_telegram: string;
  social_whatsapp: string;
  social_linkedin: string;
  footer_text: string;
  updated_at: string;
};

export type Client = {
  id: number;
  slug: string;
  name: string;
  logo_url: string;
  cover_image_url: string;
  accent_color: string;
  short_description: string;
  industry: string;
  website_url: string;
  year: string;
  featured: number;
  published: number;
  order_index: number;
  created_at: string;
  updated_at: string;
};

export type AspectRatio = "16:9" | "9:16" | "1:1";

export type Category = {
  id: number;
  client_id: number;
  title: string;
  slug: string;
  description: string;
  cover_image_url: string;
  aspect_ratio: AspectRatio;
  order_index: number;
  created_at: string;
};

export type PortfolioItem = {
  id: number;
  category_id: number;
  title: string;
  description: string;
  media_url: string;
  media_type: string;
  order_index: number;
  featured_home: number;
  created_at: string;
};

export type FontAsset = {
  id: number;
  family_name: string;
  weight: string;
  style: string;
  format: string;
  file_url: string;
  created_at: string;
};

export type Message = {
  id: number;
  name: string;
  email: string;
  phone: string;
  subject: string;
  message: string;
  is_read: number;
  created_at: string;
};

export function getSettings(): Settings {
  return db.prepare("SELECT * FROM settings WHERE id = 1").get() as Settings;
}

export function updateSettings(patch: Partial<Settings>) {
  const keys = Object.keys(patch).filter((k) => k !== "id");
  if (keys.length === 0) return;
  const setClause = keys.map((k) => `${k} = @${k}`).join(", ");
  db.prepare(
    `UPDATE settings SET ${setClause}, updated_at = datetime('now') WHERE id = 1`
  ).run(patch as Record<string, unknown>);
}

export function getFonts(): FontAsset[] {
  return db.prepare("SELECT * FROM fonts ORDER BY created_at DESC").all() as FontAsset[];
}

export function addFont(f: Omit<FontAsset, "id" | "created_at">) {
  return db
    .prepare(
      "INSERT INTO fonts (family_name, weight, style, format, file_url) VALUES (@family_name, @weight, @style, @format, @file_url)"
    )
    .run(f);
}

export function deleteFont(id: number) {
  const font = db.prepare("SELECT * FROM fonts WHERE id = ?").get(id) as
    | FontAsset
    | undefined;
  db.prepare("DELETE FROM fonts WHERE id = ?").run(id);
  if (font) deleteUploadedFile(font.file_url);
}

export function getFontFamilyFiles(familyName: string): FontAsset[] {
  return db
    .prepare("SELECT * FROM fonts WHERE family_name = ?")
    .all(familyName) as FontAsset[];
}

export function getDistinctFontFamilies(): string[] {
  const rows = db
    .prepare("SELECT DISTINCT family_name FROM fonts ORDER BY family_name")
    .all() as { family_name: string }[];
  return rows.map((r) => r.family_name);
}

// ---------------- Clients ----------------

export function getClients(opts: { onlyPublished?: boolean } = {}): Client[] {
  const where = opts.onlyPublished ? "WHERE published = 1" : "";
  return db
    .prepare(`SELECT * FROM clients ${where} ORDER BY order_index ASC, id DESC`)
    .all() as Client[];
}

export function getFeaturedClients(): Client[] {
  return db
    .prepare(
      "SELECT * FROM clients WHERE published = 1 AND featured = 1 ORDER BY order_index ASC"
    )
    .all() as Client[];
}

export function getClientBySlug(slug: string): Client | undefined {
  let decoded = slug;
  try {
    decoded = decodeURIComponent(slug);
  } catch {
    // slug wasn't encoded, use as-is
  }
  return db.prepare("SELECT * FROM clients WHERE slug = ?").get(decoded) as
    | Client
    | undefined;
}

export function getClientById(id: number): Client | undefined {
  return db.prepare("SELECT * FROM clients WHERE id = ?").get(id) as
    | Client
    | undefined;
}

export function createClient(c: {
  slug: string;
  name: string;
  logo_url?: string;
  cover_image_url?: string;
  accent_color?: string;
  short_description?: string;
  industry?: string;
  website_url?: string;
  year?: string;
  featured?: number;
  published?: number;
}) {
  const maxOrder = (
    db.prepare("SELECT MAX(order_index) as m FROM clients").get() as {
      m: number | null;
    }
  ).m;
  const info = db
    .prepare(
      `INSERT INTO clients (slug, name, logo_url, cover_image_url, accent_color, short_description, industry, website_url, year, featured, published, order_index)
       VALUES (@slug, @name, @logo_url, @cover_image_url, @accent_color, @short_description, @industry, @website_url, @year, @featured, @published, @order_index)`
    )
    .run({
      slug: c.slug,
      name: c.name,
      logo_url: c.logo_url || "",
      cover_image_url: c.cover_image_url || "",
      accent_color: c.accent_color || "",
      short_description: c.short_description || "",
      industry: c.industry || "",
      website_url: c.website_url || "",
      year: c.year || "",
      featured: c.featured ?? 0,
      published: c.published ?? 1,
      order_index: (maxOrder ?? 0) + 1,
    });
  return info.lastInsertRowid as number;
}

export function updateClient(id: number, patch: Record<string, unknown>) {
  const keys = Object.keys(patch);
  if (keys.length === 0) return;
  const setClause = keys.map((k) => `${k} = @${k}`).join(", ");
  db.prepare(
    `UPDATE clients SET ${setClause}, updated_at = datetime('now') WHERE id = @id`
  ).run({ ...patch, id });
}

export function deleteClient(id: number) {
  db.prepare("DELETE FROM clients WHERE id = ?").run(id);
}

export function reorderClients(orderedIds: number[]) {
  const stmt = db.prepare("UPDATE clients SET order_index = ? WHERE id = ?");
  const tx = db.transaction((ids: number[]) => {
    ids.forEach((id, index) => stmt.run(index, id));
  });
  tx(orderedIds);
}

// ---------------- Categories ----------------

export function getCategoriesByClient(clientId: number): Category[] {
  return db
    .prepare(
      "SELECT * FROM categories WHERE client_id = ? ORDER BY order_index ASC, id ASC"
    )
    .all(clientId) as Category[];
}

export function getCategoryBySlug(
  clientId: number,
  slug: string
): Category | undefined {
  let decoded = slug;
  try {
    decoded = decodeURIComponent(slug);
  } catch {
    // slug wasn't encoded, use as-is
  }
  return db
    .prepare("SELECT * FROM categories WHERE client_id = ? AND slug = ?")
    .get(clientId, decoded) as Category | undefined;
}

export function getCategoryById(id: number): Category | undefined {
  return db.prepare("SELECT * FROM categories WHERE id = ?").get(id) as
    | Category
    | undefined;
}

export function createCategory(c: {
  client_id: number;
  title: string;
  slug: string;
  description?: string;
  cover_image_url?: string;
  aspect_ratio?: AspectRatio;
}) {
  const maxOrder = (
    db
      .prepare(
        "SELECT MAX(order_index) as m FROM categories WHERE client_id = ?"
      )
      .get(c.client_id) as { m: number | null }
  ).m;
  const info = db
    .prepare(
      `INSERT INTO categories (client_id, title, slug, description, cover_image_url, aspect_ratio, order_index)
       VALUES (@client_id, @title, @slug, @description, @cover_image_url, @aspect_ratio, @order_index)`
    )
    .run({
      client_id: c.client_id,
      title: c.title,
      slug: c.slug,
      description: c.description || "",
      cover_image_url: c.cover_image_url || "",
      aspect_ratio: c.aspect_ratio || "16:9",
      order_index: (maxOrder ?? -1) + 1,
    });
  return info.lastInsertRowid as number;
}

export function updateCategory(id: number, patch: Record<string, unknown>) {
  const keys = Object.keys(patch);
  if (keys.length === 0) return;
  const setClause = keys.map((k) => `${k} = @${k}`).join(", ");
  db.prepare(`UPDATE categories SET ${setClause} WHERE id = @id`).run({
    ...patch,
    id,
  });
}

export function deleteCategory(id: number) {
  db.prepare("DELETE FROM categories WHERE id = ?").run(id);
}

// ---------------- Portfolio items ----------------

export function getItemsByCategory(categoryId: number): PortfolioItem[] {
  return db
    .prepare(
      "SELECT * FROM portfolio_items WHERE category_id = ? ORDER BY order_index ASC, id ASC"
    )
    .all(categoryId) as PortfolioItem[];
}

export function getFeaturedItems(limit = 8): (PortfolioItem & {
  client_name: string;
  client_slug: string;
  category_slug: string;
  category_title: string;
})[] {
  return db
    .prepare(
      `SELECT pi.*, c.name as client_name, c.slug as client_slug, cat.slug as category_slug, cat.title as category_title
       FROM portfolio_items pi
       JOIN categories cat ON cat.id = pi.category_id
       JOIN clients c ON c.id = cat.client_id
       WHERE pi.featured_home = 1 AND c.published = 1
       ORDER BY pi.order_index ASC
       LIMIT ?`
    )
    .all(limit) as (PortfolioItem & {
    client_name: string;
    client_slug: string;
    category_slug: string;
    category_title: string;
  })[];
}

export function createPortfolioItem(p: {
  category_id: number;
  title?: string;
  description?: string;
  media_url: string;
  media_type?: string;
  featured_home?: number;
}) {
  const maxOrder = (
    db
      .prepare(
        "SELECT MAX(order_index) as m FROM portfolio_items WHERE category_id = ?"
      )
      .get(p.category_id) as { m: number | null }
  ).m;
  const info = db
    .prepare(
      `INSERT INTO portfolio_items (category_id, title, description, media_url, media_type, featured_home, order_index)
       VALUES (@category_id, @title, @description, @media_url, @media_type, @featured_home, @order_index)`
    )
    .run({
      category_id: p.category_id,
      title: p.title || "",
      description: p.description || "",
      media_url: p.media_url,
      media_type: p.media_type || "image",
      featured_home: p.featured_home ?? 0,
      order_index: (maxOrder ?? -1) + 1,
    });
  return info.lastInsertRowid as number;
}

export function updatePortfolioItem(id: number, patch: Record<string, unknown>) {
  const keys = Object.keys(patch);
  if (keys.length === 0) return;
  const setClause = keys.map((k) => `${k} = @${k}`).join(", ");
  db.prepare(`UPDATE portfolio_items SET ${setClause} WHERE id = @id`).run({
    ...patch,
    id,
  });
}

export function deletePortfolioItem(id: number) {
  db.prepare("DELETE FROM portfolio_items WHERE id = ?").run(id);
}

// ---------------- Messages ----------------

export function getMessages(): Message[] {
  return db.prepare("SELECT * FROM messages ORDER BY created_at DESC").all() as Message[];
}

export function createMessage(m: {
  name: string;
  email: string;
  phone?: string;
  subject?: string;
  message: string;
}) {
  db.prepare(
    `INSERT INTO messages (name, email, phone, subject, message) VALUES (@name, @email, @phone, @subject, @message)`
  ).run({
    name: m.name,
    email: m.email,
    phone: m.phone || "",
    subject: m.subject || "",
    message: m.message,
  });
}

export function markMessageRead(id: number, isRead: boolean) {
  db.prepare("UPDATE messages SET is_read = ? WHERE id = ?").run(
    isRead ? 1 : 0,
    id
  );
}

export function deleteMessage(id: number) {
  db.prepare("DELETE FROM messages WHERE id = ?").run(id);
}

export function getUnreadMessageCount(): number {
  return (
    db.prepare("SELECT COUNT(*) as c FROM messages WHERE is_read = 0").get() as {
      c: number;
    }
  ).c;
}
