"use client";

import { useEffect, useState } from "react";
import ImageUploader from "@/components/admin/ImageUploader";
import ColorField from "@/components/admin/ColorField";
import FontManager from "@/components/admin/FontManager";
import ChangePasswordForm from "@/components/admin/ChangePasswordForm";
import type { Settings } from "@/lib/queries";

type SectionKey = "identity" | "theme" | "home" | "about" | "contact" | "security";

const SECTIONS: { key: SectionKey; label: string }[] = [
  { key: "identity", label: "هویت سایت" },
  { key: "theme", label: "رنگ و فونت" },
  { key: "home", label: "صفحه خانه" },
  { key: "about", label: "درباره ما" },
  { key: "contact", label: "تماس با ما" },
  { key: "security", label: "امنیت حساب" },
];

export default function SettingsPage() {
  const [settings, setSettings] = useState<Settings | null>(null);
  const [section, setSection] = useState<SectionKey>("identity");
  const [saving, setSaving] = useState(false);
  const [savedAt, setSavedAt] = useState<number | null>(null);

  useEffect(() => {
    fetch("/api/admin/settings").then((r) => r.json()).then(setSettings);
  }, []);

  function set<K extends keyof Settings>(key: K, value: Settings[K]) {
    setSettings((s) => (s ? { ...s, [key]: value } : s));
  }

  async function save() {
    if (!settings) return;
    setSaving(true);
    try {
      const res = await fetch("/api/admin/settings", {
        method: "PATCH",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(settings),
      });
      const data = await res.json();
      setSettings(data);
      setSavedAt(Date.now());
    } finally {
      setSaving(false);
    }
  }

  if (!settings) return <p style={{ color: "var(--a-muted)" }}>در حال بارگذاری...</p>;

  return (
    <div>
      <div className="flex items-center justify-between mb-8">
        <div>
          <h1 className="text-2xl font-bold">تنظیمات سایت</h1>
          <p style={{ color: "var(--a-muted)" }} className="text-sm mt-1">
            هر تغییری که اینجا ذخیره کنی، بلافاصله روی سایت اصلی اعمال می‌شه.
          </p>
        </div>
        <button onClick={save} disabled={saving} className="admin-btn admin-btn-primary">
          {saving ? "در حال ذخیره..." : "ذخیره تغییرات"}
        </button>
      </div>

      {savedAt && <p className="text-xs mb-4" style={{ color: "var(--a-success)" }}>تغییرات ذخیره شد ✓</p>}

      <div className="flex gap-2 mb-8 flex-wrap">
        {SECTIONS.map((s) => (
          <button
            key={s.key}
            onClick={() => setSection(s.key)}
            className="admin-btn"
            style={
              section === s.key
                ? { background: "var(--a-primary)", borderColor: "var(--a-primary)", color: "#fff" }
                : {}
            }
          >
            {s.label}
          </button>
        ))}
      </div>

      <div className="admin-card p-6 max-w-3xl flex flex-col gap-6">
        {section === "identity" && (
          <>
            <div>
              <label className="admin-label">نام سایت</label>
              <input className="admin-input" value={settings.site_name} onChange={(e) => set("site_name", e.target.value)} />
            </div>
            <div>
              <label className="admin-label">شعار / تگ‌لاین</label>
              <input className="admin-input" value={settings.tagline} onChange={(e) => set("tagline", e.target.value)} />
            </div>
            <ImageUploader label="لوگو" value={settings.logo_url} onChange={(v) => set("logo_url", v)} />
            <ImageUploader label="فاوآیکون" value={settings.favicon_url} onChange={(v) => set("favicon_url", v)} />
          </>
        )}

        {section === "theme" && (
          <>
            <div className="grid sm:grid-cols-2 gap-4">
              <ColorField label="رنگ پس‌زمینه" value={settings.color_bg} onChange={(v) => set("color_bg", v)} />
              <ColorField label="رنگ متن" value={settings.color_fg} onChange={(v) => set("color_fg", v)} />
              <ColorField label="رنگ اصلی (Primary)" value={settings.color_primary} onChange={(v) => set("color_primary", v)} />
              <ColorField label="رنگ ثانویه" value={settings.color_secondary} onChange={(v) => set("color_secondary", v)} />
              <ColorField label="رنگ تاکیدی (Accent)" value={settings.color_accent} onChange={(v) => set("color_accent", v)} />
              <ColorField label="رنگ خنثی (Muted)" value={settings.color_muted} onChange={(v) => set("color_muted", v)} />
            </div>
            <div className="hairline" style={{ background: "var(--a-border)", height: 1 }} />
            <FontManager activeFamily={settings.font_family} onSelect={(f) => set("font_family", f)} />
          </>
        )}

        {section === "home" && (
          <>
            <div>
              <label className="admin-label">تیتر اصلی (هر خط جدید = یک سطر)</label>
              <textarea rows={3} className="admin-input" value={settings.hero_title} onChange={(e) => set("hero_title", e.target.value)} />
            </div>
            <div>
              <label className="admin-label">زیرعنوان</label>
              <textarea rows={2} className="admin-input" value={settings.hero_subtitle} onChange={(e) => set("hero_subtitle", e.target.value)} />
            </div>
            <div className="grid sm:grid-cols-2 gap-4">
              <div>
                <label className="admin-label">متن دکمه اصلی</label>
                <input className="admin-input" value={settings.hero_cta_text} onChange={(e) => set("hero_cta_text", e.target.value)} />
              </div>
              <div>
                <label className="admin-label">لینک دکمه اصلی</label>
                <input className="admin-input" dir="ltr" value={settings.hero_cta_link} onChange={(e) => set("hero_cta_link", e.target.value)} />
              </div>
            </div>
          </>
        )}

        {section === "about" && (
          <>
            <div>
              <label className="admin-label">عنوان درباره ما</label>
              <input className="admin-input" value={settings.about_title} onChange={(e) => set("about_title", e.target.value)} />
            </div>
            <div>
              <label className="admin-label">متن درباره ما</label>
              <textarea rows={6} className="admin-input" value={settings.about_body} onChange={(e) => set("about_body", e.target.value)} />
            </div>
            <ImageUploader label="تصویر درباره ما" value={settings.about_image_url} onChange={(v) => set("about_image_url", v)} />
          </>
        )}

        {section === "contact" && (
          <>
            <div className="grid sm:grid-cols-2 gap-4">
              <div>
                <label className="admin-label">ایمیل</label>
                <input dir="ltr" className="admin-input" value={settings.contact_email} onChange={(e) => set("contact_email", e.target.value)} />
              </div>
              <div>
                <label className="admin-label">تلفن</label>
                <input dir="ltr" className="admin-input" value={settings.contact_phone} onChange={(e) => set("contact_phone", e.target.value)} />
              </div>
            </div>
            <div>
              <label className="admin-label">آدرس</label>
              <input className="admin-input" value={settings.contact_address} onChange={(e) => set("contact_address", e.target.value)} />
            </div>
            <div>
              <label className="admin-label">کد امبد نقشه (اختیاری - iframe)</label>
              <textarea rows={3} dir="ltr" className="admin-input" value={settings.contact_map_embed} onChange={(e) => set("contact_map_embed", e.target.value)} />
            </div>
            <div className="grid sm:grid-cols-2 gap-4">
              <div>
                <label className="admin-label">اینستاگرام</label>
                <input dir="ltr" className="admin-input" value={settings.social_instagram} onChange={(e) => set("social_instagram", e.target.value)} />
              </div>
              <div>
                <label className="admin-label">تلگرام</label>
                <input dir="ltr" className="admin-input" value={settings.social_telegram} onChange={(e) => set("social_telegram", e.target.value)} />
              </div>
              <div>
                <label className="admin-label">واتس‌اپ</label>
                <input dir="ltr" className="admin-input" value={settings.social_whatsapp} onChange={(e) => set("social_whatsapp", e.target.value)} />
              </div>
              <div>
                <label className="admin-label">لینکدین</label>
                <input dir="ltr" className="admin-input" value={settings.social_linkedin} onChange={(e) => set("social_linkedin", e.target.value)} />
              </div>
            </div>
            <div>
              <label className="admin-label">متن فوتر</label>
              <input className="admin-input" value={settings.footer_text} onChange={(e) => set("footer_text", e.target.value)} />
            </div>
          </>
        )}

        {section === "security" && <ChangePasswordForm />}
      </div>
    </div>
  );
}
