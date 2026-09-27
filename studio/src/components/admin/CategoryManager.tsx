"use client";

import { useState } from "react";
import ImageUploader from "./ImageUploader";
import PortfolioItemsManager from "./PortfolioItemsManager";
import type { Category, PortfolioItem } from "@/lib/queries";

type CategoryWithItems = Category & { items: PortfolioItem[] };

export default function CategoryManager({
  category,
  onChange,
}: {
  category: CategoryWithItems;
  onChange: () => void;
}) {
  const [expanded, setExpanded] = useState(false);
  const [editing, setEditing] = useState(false);
  const [title, setTitle] = useState(category.title);
  const [description, setDescription] = useState(category.description);
  const [coverImageUrl, setCoverImageUrl] = useState(category.cover_image_url);
  const [saving, setSaving] = useState(false);

  async function saveEdit() {
    setSaving(true);
    try {
      await fetch(`/api/admin/categories/${category.id}`, {
        method: "PATCH",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ title, description, cover_image_url: coverImageUrl }),
      });
      setEditing(false);
      onChange();
    } finally {
      setSaving(false);
    }
  }

  async function removeCategory() {
    if (!confirm(`دسته‌بندی «${category.title}» و تمام نمونه‌کارهاش حذف می‌شه. مطمئنی؟`)) return;
    await fetch(`/api/admin/categories/${category.id}`, { method: "DELETE" });
    onChange();
  }

  return (
    <div className="admin-card p-5">
      <div className="flex items-center justify-between gap-4">
        <button onClick={() => setExpanded((v) => !v)} className="flex items-center gap-3 text-right flex-1">
          <span className="text-lg">{expanded ? "▾" : "◂"}</span>
          <div>
            <p className="font-bold">{category.title}</p>
            <p className="text-xs" style={{ color: "var(--a-muted)" }}>{category.items.length} نمونه‌کار · /{category.slug}</p>
          </div>
        </button>
        <div className="flex gap-2 shrink-0">
          <button onClick={() => setEditing((v) => !v)} className="admin-btn text-xs">ویرایش</button>
          <button onClick={removeCategory} className="admin-btn admin-btn-danger text-xs">حذف</button>
        </div>
      </div>

      {editing && (
        <div className="mt-4 pt-4 flex flex-col gap-3" style={{ borderTop: "1px solid var(--a-border)" }}>
          <input className="admin-input" value={title} onChange={(e) => setTitle(e.target.value)} placeholder="عنوان دسته‌بندی" />
          <textarea className="admin-input" rows={2} value={description} onChange={(e) => setDescription(e.target.value)} placeholder="توضیح کوتاه" />
          <ImageUploader label="تصویر کاور دسته‌بندی" value={coverImageUrl} onChange={setCoverImageUrl} />
          <div className="flex gap-2">
            <button onClick={saveEdit} disabled={saving} className="admin-btn admin-btn-primary text-sm">{saving ? "..." : "ذخیره"}</button>
          </div>
        </div>
      )}

      {expanded && (
        <div className="mt-4 pt-4" style={{ borderTop: "1px solid var(--a-border)" }}>
          <PortfolioItemsManager categoryId={category.id} items={category.items} onChange={onChange} />
        </div>
      )}
    </div>
  );
}
