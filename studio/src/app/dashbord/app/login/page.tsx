"use client";

import { Suspense, useState } from "react";
import { useRouter, useSearchParams } from "next/navigation";

function LoginForm() {
  const router = useRouter();
  const params = useSearchParams();
  const [username, setUsername] = useState("");
  const [password, setPassword] = useState("");
  const [error, setError] = useState("");
  const [loading, setLoading] = useState(false);

  async function onSubmit(e: React.FormEvent) {
    e.preventDefault();
    setLoading(true);
    setError("");
    try {
      const res = await fetch("/api/admin/login", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ username, password }),
      });
      const data = await res.json();
      if (!res.ok) throw new Error(data.error || "خطا در ورود");
      router.push(params.get("next") || "/dashbord/app");
      router.refresh();
    } catch (err) {
      setError(err instanceof Error ? err.message : "خطا در ورود");
    } finally {
      setLoading(false);
    }
  }

  return (
    <div className="admin-scope min-h-dvh flex items-center justify-center p-6">
      <div className="admin-card w-full max-w-sm p-8">
        <p className="text-xs tracking-[0.2em] uppercase" style={{ color: "var(--a-muted)" }}>پنل مدیریت</p>
        <h1 className="text-2xl font-bold mt-2 mb-8">ورود به پنل نگاه مدیا</h1>
        <form onSubmit={onSubmit} className="flex flex-col gap-5">
          <div>
            <label className="admin-label">نام کاربری</label>
            <input className="admin-input" value={username} onChange={(e) => setUsername(e.target.value)} required autoFocus />
          </div>
          <div>
            <label className="admin-label">رمز عبور</label>
            <input type="password" className="admin-input" value={password} onChange={(e) => setPassword(e.target.value)} required />
          </div>
          {error && <p style={{ color: "var(--a-danger)" }} className="text-sm">{error}</p>}
          <button type="submit" disabled={loading} className="admin-btn admin-btn-primary justify-center mt-2">
            {loading ? "در حال ورود..." : "ورود"}
          </button>
        </form>
      </div>
    </div>
  );
}

export default function AdminLoginPage() {
  return (
    <Suspense>
      <LoginForm />
    </Suspense>
  );
}
