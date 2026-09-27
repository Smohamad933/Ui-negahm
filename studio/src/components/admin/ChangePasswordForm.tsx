"use client";

import { useState } from "react";

export default function ChangePasswordForm() {
  const [currentPassword, setCurrentPassword] = useState("");
  const [newPassword, setNewPassword] = useState("");
  const [confirmPassword, setConfirmPassword] = useState("");
  const [status, setStatus] = useState<"idle" | "saving" | "success">("idle");
  const [error, setError] = useState("");

  async function onSubmit(e: React.FormEvent) {
    e.preventDefault();
    setError("");
    if (newPassword !== confirmPassword) {
      setError("رمز جدید و تکرار آن یکسان نیستند");
      return;
    }
    setStatus("saving");
    try {
      const res = await fetch("/api/admin/change-password", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ currentPassword, newPassword }),
      });
      const data = await res.json();
      if (!res.ok) throw new Error(data.error || "خطا در تغییر رمز");
      setStatus("success");
      setCurrentPassword("");
      setNewPassword("");
      setConfirmPassword("");
    } catch (err) {
      setStatus("idle");
      setError(err instanceof Error ? err.message : "خطا در تغییر رمز");
    }
  }

  return (
    <form onSubmit={onSubmit} className="flex flex-col gap-5 max-w-sm">
      <p className="font-bold">تغییر رمز عبور ورود به پنل مدیریت</p>
      <div>
        <label className="admin-label">رمز فعلی</label>
        <input type="password" required className="admin-input" value={currentPassword} onChange={(e) => setCurrentPassword(e.target.value)} />
      </div>
      <div>
        <label className="admin-label">رمز جدید</label>
        <input type="password" required minLength={6} className="admin-input" value={newPassword} onChange={(e) => setNewPassword(e.target.value)} />
      </div>
      <div>
        <label className="admin-label">تکرار رمز جدید</label>
        <input type="password" required minLength={6} className="admin-input" value={confirmPassword} onChange={(e) => setConfirmPassword(e.target.value)} />
      </div>
      {error && <p style={{ color: "var(--a-danger)" }} className="text-sm">{error}</p>}
      {status === "success" && <p style={{ color: "var(--a-success)" }} className="text-sm">رمز عبور با موفقیت تغییر کرد ✓</p>}
      <button type="submit" disabled={status === "saving"} className="admin-btn admin-btn-primary self-start">
        {status === "saving" ? "در حال ذخیره..." : "تغییر رمز عبور"}
      </button>
    </form>
  );
}
