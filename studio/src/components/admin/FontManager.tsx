"use client";

import { useEffect, useRef, useState } from "react";
import type { FontAsset } from "@/lib/queries";

const BUILT_IN_FONTS = ["Vazirmatn Variable", "Manrope Variable"];

export default function FontManager({
  activeFamily,
  onSelect,
}: {
  activeFamily: string;
  onSelect: (family: string) => void;
}) {
  const [fonts, setFonts] = useState<FontAsset[]>([]);
  const [loading, setLoading] = useState(false);
  const [familyName, setFamilyName] = useState("");
  const [weight, setWeight] = useState("400");
  const [style, setStyle] = useState("normal");
  const [error, setError] = useState("");
  const inputRef = useRef<HTMLInputElement>(null);

  async function refresh() {
    const res = await fetch("/api/admin/fonts");
    setFonts(await res.json());
  }

  useEffect(() => {
    refresh();
  }, []);

  const customFamilies = Array.from(new Set(fonts.map((f) => f.family_name)));

  async function onFileSelected(e: React.ChangeEvent<HTMLInputElement>) {
    const file = e.target.files?.[0];
    if (!file || !familyName.trim()) {
      setError("ابتدا نام فونت را وارد کنید");
      return;
    }
    setLoading(true);
    setError("");
    try {
      const fd = new FormData();
      fd.append("file", file);
      fd.append("kind", "font");
      fd.append("familyName", familyName.trim());
      fd.append("weight", weight);
      fd.append("style", style);
      const res = await fetch("/api/admin/upload", { method: "POST", body: fd });
      const data = await res.json();
      if (!res.ok) throw new Error(data.error || "خطا در آپلود فونت");
      await refresh();
      onSelect(familyName.trim());
    } catch (err) {
      setError(err instanceof Error ? err.message : "خطا در آپلود فونت");
    } finally {
      setLoading(false);
      if (inputRef.current) inputRef.current.value = "";
    }
  }

  async function removeFont(id: number) {
    await fetch(`/api/admin/fonts/${id}`, { method: "DELETE" });
    await refresh();
  }

  return (
    <div className="flex flex-col gap-6">
      <div>
        <label className="admin-label">فونت فعال سایت</label>
        <div className="flex flex-wrap gap-2">
          {[...BUILT_IN_FONTS, ...customFamilies].map((f) => (
            <button
              key={f}
              type="button"
              onClick={() => onSelect(f)}
              className="admin-btn"
              style={{
                borderColor: activeFamily === f ? "var(--a-primary)" : undefined,
                background: activeFamily === f ? "var(--a-primary)" : undefined,
                color: activeFamily === f ? "#fff" : undefined,
              }}
            >
              <span style={{ fontFamily: `'${f}'` }}>{f}</span>
            </button>
          ))}
        </div>
      </div>

      <div className="admin-card p-5">
        <p className="font-bold mb-4">آپلود فونت اختصاصی</p>
        <div className="grid sm:grid-cols-3 gap-3 mb-3">
          <div>
            <label className="admin-label">نام فونت</label>
            <input
              className="admin-input"
              placeholder="مثلا: یکان بخ"
              value={familyName}
              onChange={(e) => setFamilyName(e.target.value)}
            />
          </div>
          <div>
            <label className="admin-label">وزن</label>
            <select className="admin-input" value={weight} onChange={(e) => setWeight(e.target.value)}>
              {["100", "200", "300", "400", "500", "600", "700", "800", "900", "100 900"].map((w) => (
                <option key={w} value={w}>{w}</option>
              ))}
            </select>
          </div>
          <div>
            <label className="admin-label">سبک</label>
            <select className="admin-input" value={style} onChange={(e) => setStyle(e.target.value)}>
              <option value="normal">Normal</option>
              <option value="italic">Italic</option>
            </select>
          </div>
        </div>
        <input ref={inputRef} type="file" accept=".woff2,.woff,.ttf,.otf" onChange={onFileSelected} className="text-sm" />
        {loading && <p className="text-xs mt-2" style={{ color: "var(--a-muted)" }}>در حال آپلود...</p>}
        {error && <p className="text-xs mt-2" style={{ color: "var(--a-danger)" }}>{error}</p>}
      </div>

      {fonts.length > 0 && (
        <div>
          <p className="admin-label">فایل‌های آپلود شده</p>
          <div className="flex flex-col gap-2">
            {fonts.map((f) => (
              <div key={f.id} className="flex items-center justify-between admin-card px-4 py-2">
                <span className="text-sm">{f.family_name} · وزن {f.weight} · {f.style} · {f.format}</span>
                <button onClick={() => removeFont(f.id)} className="admin-btn admin-btn-danger text-xs">حذف</button>
              </div>
            ))}
          </div>
        </div>
      )}
    </div>
  );
}
