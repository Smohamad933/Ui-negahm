"use client";

import { useState } from "react";
import { useRouter } from "next/navigation";
import ImageUploader from "@/components/admin/ImageUploader";
import ColorField from "@/components/admin/ColorField";

export default function NewClientPage() {
  const router = useRouter();
  const [name, setName] = useState("");
  const [industry, setIndustry] = useState("");
  const [shortDescription, setShortDescription] = useState("");
  const [websiteUrl, setWebsiteUrl] = useState("");
  const [year, setYear] = useState("");
  const [logoUrl, setLogoUrl] = useState("");
  const [coverImageUrl, setCoverImageUrl] = useState("");
  const [accentColor, setAccentColor] = useState("#7a5cff");
  const [saving, setSaving] = useState(false);
  const [error, setError] = useState("");

  async function onSubmit(e: React.FormEvent) {
    e.preventDefault();
    setSaving(true);
    setError("");
    try {
      const res = await fetch("/api/admin/clients", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({
          name,
          industry,
          short_description: shortDescription,
          website_url: websiteUrl,
          year,
          logo_url: logoUrl,
          cover_image_url: coverImageUrl,
          accent_color: accentColor,
        }),
      });
      const data = await res.json();
      if (!res.ok) throw new Error(data.error || "خطا در ایجاد کارفرما");
      router.push(`/dashbord/app/clients/${data.id}`);
    } catch (err) {
      setError(err instanceof Error ? err.message : "خطا در ایجاد کارفرما");
    } finally {
      setSaving(false);
    }
  }

  return (
    <div className="max-w-2xl">
      <h1 className="text-2xl font-bold mb-8">افزودن کارفرمای جدید</h1>
      <form onSubmit={onSubmit} className="admin-card p-6 flex flex-col gap-5">
        <div>
          <label className="admin-label">نام کارفرما *</label>
          <input required className="admin-input" value={name} onChange={(e) => setName(e.target.value)} />
        </div>
        <div className="grid sm:grid-cols-2 gap-4">
          <div>
            <label className="admin-label">صنعت / حوزه فعالیت</label>
            <input className="admin-input" value={industry} onChange={(e) => setIndustry(e.target.value)} placeholder="مثلا: فینتک" />
          </div>
          <div>
            <label className="admin-label">سال همکاری</label>
            <input className="admin-input" value={year} onChange={(e) => setYear(e.target.value)} placeholder="۱۴۰۴" />
          </div>
        </div>
        <div>
          <label className="admin-label">توضیح کوتاه</label>
          <textarea rows={3} className="admin-input" value={shortDescription} onChange={(e) => setShortDescription(e.target.value)} />
        </div>
        <div>
          <label className="admin-label">وب‌سایت کارفرما</label>
          <input dir="ltr" className="admin-input" value={websiteUrl} onChange={(e) => setWebsiteUrl(e.target.value)} placeholder="https://" />
        </div>
        <ImageUploader label="لوگو" value={logoUrl} onChange={setLogoUrl} />
        <ImageUploader label="تصویر کاور" value={coverImageUrl} onChange={setCoverImageUrl} />
        <ColorField label="رنگ اختصاصی کارت (اختیاری)" value={accentColor} onChange={setAccentColor} />

        {error && <p style={{ color: "var(--a-danger)" }} className="text-sm">{error}</p>}

        <div className="flex gap-3">
          <button type="submit" disabled={saving} className="admin-btn admin-btn-primary">
            {saving ? "در حال ایجاد..." : "ایجاد و ادامه"}
          </button>
        </div>
      </form>
    </div>
  );
}
