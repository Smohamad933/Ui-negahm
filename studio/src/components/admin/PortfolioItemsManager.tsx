"use client";

import { useState } from "react";
import ImageUploader from "./ImageUploader";
import type { PortfolioItem } from "@/lib/queries";

export default function PortfolioItemsManager({
  categoryId,
  items,
  onChange,
}: {
  categoryId: number;
  items: PortfolioItem[];
  onChange: () => void;
}) {
  const [adding, setAdding] = useState(false);
  const [mediaUrl, setMediaUrl] = useState("");
  const [mediaType, setMediaType] = useState<"image" | "video">("image");
  const [title, setTitle] = useState("");
  const [description, setDescription] = useState("");
  const [featured, setFeatured] = useState(false);
  const [saving, setSaving] = useState(false);

  async function addItem() {
    if (!mediaUrl) return;
    setSaving(true);
    try {
      await fetch("/api/admin/portfolio-items", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({
          category_id: categoryId,
          media_url: mediaUrl,
          media_type: mediaType,
          title,
          description,
          featured_home: featured,
        }),
      });
      setMediaUrl("");
      setTitle("");
      setDescription("");
      setFeatured(false);
      setAdding(false);
      onChange();
    } finally {
      setSaving(false);
    }
  }

  async function removeItem(id: number) {
    if (!confirm("این نمونه‌کار حذف بشه؟")) return;
    await fetch(`/api/admin/portfolio-items/${id}`, { method: "DELETE" });
    onChange();
  }

  async function toggleFeatured(item: PortfolioItem) {
    await fetch(`/api/admin/portfolio-items/${item.id}`, {
      method: "PATCH",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ featured_home: !item.featured_home }),
    });
    onChange();
  }

  return (
    <div className="flex flex-col gap-3">
      {items.length > 0 && (
        <div className="grid sm:grid-cols-2 lg:grid-cols-3 gap-3">
          {items.map((item) => (
            <div key={item.id} className="admin-card p-3 flex flex-col gap-2">
              <div className="w-full aspect-video rounded-lg overflow-hidden" style={{ background: "var(--a-panel-2)" }}>
                {item.media_type === "video" ? (
                  <video src={item.media_url} className="w-full h-full object-cover" muted />
                ) : (
                  // eslint-disable-next-line @next/next/no-img-element
                  <img src={item.media_url} alt="" className="w-full h-full object-cover" />
                )}
              </div>
              {item.title && <p className="text-sm font-bold truncate">{item.title}</p>}
              <div className="flex items-center justify-between">
                <button
                  onClick={() => toggleFeatured(item)}
                  className="admin-badge"
                  style={{
                    borderColor: item.featured_home ? "var(--a-primary)" : "var(--a-border)",
                    color: item.featured_home ? "var(--a-primary)" : "var(--a-muted)",
                  }}
                >
                  {item.featured_home ? "در صفحه اصلی" : "نمایش عادی"}
                </button>
                <button onClick={() => removeItem(item.id)} className="admin-btn admin-btn-danger !p-1 !px-2 text-xs">حذف</button>
              </div>
            </div>
          ))}
        </div>
      )}

      {adding ? (
        <div className="admin-card p-4 flex flex-col gap-3">
          <div className="flex gap-2">
            <button className="admin-btn text-xs" style={mediaType === "image" ? { background: "var(--a-primary)", borderColor: "var(--a-primary)", color: "#fff" } : {}} onClick={() => setMediaType("image")} type="button">تصویر</button>
            <button className="admin-btn text-xs" style={mediaType === "video" ? { background: "var(--a-primary)", borderColor: "var(--a-primary)", color: "#fff" } : {}} onClick={() => setMediaType("video")} type="button">ویدیو</button>
          </div>
          <ImageUploader value={mediaUrl} onChange={setMediaUrl} kind={mediaType} />
          <input className="admin-input" placeholder="عنوان (اختیاری)" value={title} onChange={(e) => setTitle(e.target.value)} />
          <textarea className="admin-input" rows={2} placeholder="توضیحات (اختیاری)" value={description} onChange={(e) => setDescription(e.target.value)} />
          <label className="flex items-center gap-2 text-sm" style={{ color: "var(--a-muted)" }}>
            <input type="checkbox" checked={featured} onChange={(e) => setFeatured(e.target.checked)} />
            نمایش در «نمونه‌کارهای برتر» صفحه اصلی
          </label>
          <div className="flex gap-2">
            <button onClick={addItem} disabled={saving || !mediaUrl} className="admin-btn admin-btn-primary text-sm">{saving ? "در حال ذخیره..." : "افزودن"}</button>
            <button onClick={() => setAdding(false)} className="admin-btn text-sm" type="button">انصراف</button>
          </div>
        </div>
      ) : (
        <button onClick={() => setAdding(true)} className="admin-btn self-start text-sm">+ افزودن نمونه‌کار</button>
      )}
    </div>
  );
}
