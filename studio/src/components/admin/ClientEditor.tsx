"use client";

import { useEffect, useState } from "react";
import { useRouter } from "next/navigation";
import ImageUploader from "./ImageUploader";
import ColorField from "./ColorField";
import CategoryManager from "./CategoryManager";
import AspectRatioSelect from "./AspectRatioSelect";
import type { Client, Category, PortfolioItem, AspectRatio } from "@/lib/queries";

type ClientDetail = Client & { categories: (Category & { items: PortfolioItem[] })[] };

export default function ClientEditor({ id }: { id: number }) {
  const router = useRouter();
  const [client, setClient] = useState<ClientDetail | null>(null);
  const [tab, setTab] = useState<"info" | "portfolio">("info");
  const [saving, setSaving] = useState(false);
  const [savedAt, setSavedAt] = useState<number | null>(null);

  // new category form
  const [newCatTitle, setNewCatTitle] = useState("");
  const [newCatRatio, setNewCatRatio] = useState<AspectRatio>("16:9");
  const [addingCategory, setAddingCategory] = useState(false);

  async function refresh() {
    const res = await fetch(`/api/admin/clients/${id}`);
    setClient(await res.json());
  }

  useEffect(() => {
    refresh();
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [id]);

  function set<K extends keyof Client>(key: K, value: Client[K]) {
    setClient((c) => (c ? { ...c, [key]: value } : c));
  }

  async function saveInfo() {
    if (!client) return;
    setSaving(true);
    try {
      await fetch(`/api/admin/clients/${id}`, {
        method: "PATCH",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({
          name: client.name,
          slug: client.slug,
          industry: client.industry,
          short_description: client.short_description,
          website_url: client.website_url,
          year: client.year,
          logo_url: client.logo_url,
          cover_image_url: client.cover_image_url,
          accent_color: client.accent_color,
          published: !!client.published,
          featured: !!client.featured,
        }),
      });
      setSavedAt(Date.now());
      refresh();
    } finally {
      setSaving(false);
    }
  }

  async function addCategory() {
    if (!newCatTitle.trim() || !client) return;
    await fetch("/api/admin/categories", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({
        client_id: client.id,
        title: newCatTitle.trim(),
        aspect_ratio: newCatRatio,
      }),
    });
    setNewCatTitle("");
    setNewCatRatio("16:9");
    setAddingCategory(false);
    refresh();
  }

  async function removeClient() {
    if (!confirm("این کارفرما برای همیشه حذف می‌شه. مطمئنی؟")) return;
    await fetch(`/api/admin/clients/${id}`, { method: "DELETE" });
    router.push("/admin/clients");
  }

  if (!client) return <p style={{ color: "var(--a-muted)" }}>در حال بارگذاری...</p>;

  return (
    <div>
      <div className="flex items-center justify-between mb-8 flex-wrap gap-4">
        <div>
          <h1 className="text-2xl font-bold">{client.name}</h1>
          <p style={{ color: "var(--a-muted)" }} className="text-sm mt-1">/clients/{client.slug}</p>
        </div>
        <div className="flex gap-2">
          <a href={`/clients/${client.slug}`} target="_blank" className="admin-btn">مشاهده در سایت ↗</a>
          <button onClick={removeClient} className="admin-btn admin-btn-danger">حذف کارفرما</button>
        </div>
      </div>

      <div className="flex gap-2 mb-8">
        <button onClick={() => setTab("info")} className="admin-btn" style={tab === "info" ? { background: "var(--a-primary)", borderColor: "var(--a-primary)", color: "#fff" } : {}}>اطلاعات کلی</button>
        <button onClick={() => setTab("portfolio")} className="admin-btn" style={tab === "portfolio" ? { background: "var(--a-primary)", borderColor: "var(--a-primary)", color: "#fff" } : {}}>دسته‌بندی نمونه‌کارها ({client.categories.length})</button>
      </div>

      {tab === "info" && (
        <div className="admin-card p-6 max-w-2xl flex flex-col gap-5">
          <div>
            <label className="admin-label">نام کارفرما</label>
            <input className="admin-input" value={client.name} onChange={(e) => set("name", e.target.value)} />
          </div>
          <div>
            <label className="admin-label">آدرس یکتا (slug)</label>
            <input dir="ltr" className="admin-input" value={client.slug} onChange={(e) => set("slug", e.target.value)} />
          </div>
          <div className="grid sm:grid-cols-2 gap-4">
            <div>
              <label className="admin-label">صنعت / حوزه فعالیت</label>
              <input className="admin-input" value={client.industry} onChange={(e) => set("industry", e.target.value)} />
            </div>
            <div>
              <label className="admin-label">سال همکاری</label>
              <input className="admin-input" value={client.year} onChange={(e) => set("year", e.target.value)} />
            </div>
          </div>
          <div>
            <label className="admin-label">توضیح کوتاه</label>
            <textarea rows={3} className="admin-input" value={client.short_description} onChange={(e) => set("short_description", e.target.value)} />
          </div>
          <div>
            <label className="admin-label">وب‌سایت</label>
            <input dir="ltr" className="admin-input" value={client.website_url} onChange={(e) => set("website_url", e.target.value)} />
          </div>
          <ImageUploader label="لوگو" value={client.logo_url} onChange={(v) => set("logo_url", v)} />
          <ImageUploader label="تصویر کاور" value={client.cover_image_url} onChange={(v) => set("cover_image_url", v)} />
          <ColorField label="رنگ اختصاصی" value={client.accent_color} onChange={(v) => set("accent_color", v)} />

          <div className="flex gap-6">
            <label className="flex items-center gap-2 text-sm">
              <input type="checkbox" checked={!!client.published} onChange={(e) => set("published", e.target.checked ? 1 : 0)} />
              منتشر شده (روی سایت نمایش داده شود)
            </label>
            <label className="flex items-center gap-2 text-sm">
              <input type="checkbox" checked={!!client.featured} onChange={(e) => set("featured", e.target.checked ? 1 : 0)} />
              کارفرمای ویژه
            </label>
          </div>

          {savedAt && <p className="text-xs" style={{ color: "var(--a-success)" }}>ذخیره شد ✓</p>}
          <button onClick={saveInfo} disabled={saving} className="admin-btn admin-btn-primary self-start">
            {saving ? "در حال ذخیره..." : "ذخیره تغییرات"}
          </button>
        </div>
      )}

      {tab === "portfolio" && (
        <div className="flex flex-col gap-4 max-w-3xl">
          {client.categories.map((cat) => (
            <CategoryManager key={cat.id} category={cat} onChange={refresh} />
          ))}

          {addingCategory ? (
            <div className="admin-card p-4 flex flex-col gap-3">
              <input
                autoFocus
                className="admin-input"
                placeholder="مثلا: کمپین تبلیغاتی، طراحی سایت، عکاسی محصول..."
                value={newCatTitle}
                onChange={(e) => setNewCatTitle(e.target.value)}
                onKeyDown={(e) => e.key === "Enter" && addCategory()}
              />
              <div className="flex flex-col gap-1">
                <label className="text-xs" style={{ color: "var(--a-muted)" }}>
                  نسبت تصویر این دسته‌بندی (برای کاور و همه نمونه‌کارهای داخلش)
                </label>
                <AspectRatioSelect value={newCatRatio} onChange={setNewCatRatio} />
              </div>
              <div className="flex gap-2">
                <button onClick={addCategory} className="admin-btn admin-btn-primary shrink-0">افزودن</button>
                <button onClick={() => setAddingCategory(false)} className="admin-btn shrink-0">انصراف</button>
              </div>
            </div>
          ) : (
            <button onClick={() => setAddingCategory(true)} className="admin-btn self-start">+ دسته‌بندی جدید (مثلا کمپین، طراحی سایت)</button>
          )}
        </div>
      )}
    </div>
  );
}
