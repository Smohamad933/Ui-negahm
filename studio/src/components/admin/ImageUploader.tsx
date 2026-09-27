"use client";

import { useRef, useState } from "react";

export default function ImageUploader({
  value,
  onChange,
  kind = "image",
  label,
}: {
  value: string;
  onChange: (url: string) => void;
  kind?: "image" | "video";
  label?: string;
}) {
  const inputRef = useRef<HTMLInputElement>(null);
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState("");

  async function onFileChange(e: React.ChangeEvent<HTMLInputElement>) {
    const file = e.target.files?.[0];
    if (!file) return;
    setLoading(true);
    setError("");
    try {
      const fd = new FormData();
      fd.append("file", file);
      fd.append("kind", kind);
      const res = await fetch("/api/admin/upload", { method: "POST", body: fd });
      const data = await res.json();
      if (!res.ok) throw new Error(data.error || "خطا در آپلود");
      onChange(data.url);
    } catch (err) {
      setError(err instanceof Error ? err.message : "خطا در آپلود");
    } finally {
      setLoading(false);
      if (inputRef.current) inputRef.current.value = "";
    }
  }

  return (
    <div>
      {label && <label className="admin-label">{label}</label>}
      <div className="flex items-center gap-3">
        <div
          className="w-20 h-20 rounded-lg overflow-hidden shrink-0 flex items-center justify-center"
          style={{ background: "var(--a-panel-2)", border: "1px solid var(--a-border)" }}
        >
          {value ? (
            kind === "video" ? (
              <video src={value} className="w-full h-full object-cover" muted />
            ) : (
              // eslint-disable-next-line @next/next/no-img-element
              <img src={value} alt="" className="w-full h-full object-cover" />
            )
          ) : (
            <span style={{ color: "var(--a-muted)" }} className="text-xs">بدون فایل</span>
          )}
        </div>
        <div className="flex flex-col gap-2">
          <button
            type="button"
            onClick={() => inputRef.current?.click()}
            className="admin-btn"
            disabled={loading}
          >
            {loading ? "در حال آپلود..." : value ? "تغییر فایل" : "آپلود فایل"}
          </button>
          {value && (
            <button type="button" onClick={() => onChange("")} className="admin-btn admin-btn-danger text-xs">
              حذف
            </button>
          )}
        </div>
        <input
          ref={inputRef}
          type="file"
          accept={kind === "video" ? "video/mp4,video/webm" : "image/png,image/jpeg,image/webp,image/gif,image/svg+xml"}
          className="hidden"
          onChange={onFileChange}
        />
      </div>
      {error && <p style={{ color: "var(--a-danger)" }} className="text-xs mt-2">{error}</p>}
    </div>
  );
}
