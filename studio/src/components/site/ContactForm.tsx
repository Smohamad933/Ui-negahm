"use client";

import { useState } from "react";

export default function ContactForm() {
  const [status, setStatus] = useState<"idle" | "loading" | "success" | "error">("idle");
  const [error, setError] = useState("");

  async function onSubmit(e: React.FormEvent<HTMLFormElement>) {
    e.preventDefault();
    setStatus("loading");
    setError("");
    const form = e.currentTarget;
    const data = new FormData(form);
    const payload = Object.fromEntries(data.entries());

    try {
      const res = await fetch("/api/contact", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(payload),
      });
      if (!res.ok) {
        const j = await res.json().catch(() => ({}));
        throw new Error(j.error || "خطایی رخ داد");
      }
      setStatus("success");
      form.reset();
    } catch (err) {
      setStatus("error");
      setError(err instanceof Error ? err.message : "خطایی رخ داد");
    }
  }

  if (status === "success") {
    return (
      <div className="rounded-3xl frame-pop p-10 text-center">
        <p className="font-display text-2xl font-bold text-[var(--color-primary)]">پیام شما ارسال شد ✓</p>
        <p className="text-[var(--color-muted)] mt-3">به‌زودی با شما تماس می‌گیریم.</p>
      </div>
    );
  }

  return (
    <form onSubmit={onSubmit} className="flex flex-col gap-6">
      <div className="grid md:grid-cols-2 gap-6">
        <label className="flex flex-col gap-2">
          <span className="eyebrow">نام و نام‌خانوادگی</span>
          <input name="name" required className="input-field" placeholder="نام شما" />
        </label>
        <label className="flex flex-col gap-2">
          <span className="eyebrow">ایمیل</span>
          <input name="email" type="email" required className="input-field" placeholder="you@example.com" dir="ltr" />
        </label>
      </div>
      <div className="grid md:grid-cols-2 gap-6">
        <label className="flex flex-col gap-2">
          <span className="eyebrow">شماره تماس</span>
          <input name="phone" className="input-field" placeholder="۰۹۱۲۰۰۰۰۰۰۰" dir="ltr" />
        </label>
        <label className="flex flex-col gap-2">
          <span className="eyebrow">موضوع</span>
          <input name="subject" className="input-field" placeholder="مثلا: درخواست همکاری" />
        </label>
      </div>
      <label className="flex flex-col gap-2">
        <span className="eyebrow">پیام شما</span>
        <textarea name="message" required rows={6} className="input-field resize-none" placeholder="توضیحات پروژه یا درخواستتون..." />
      </label>

      {status === "error" && <p className="text-[var(--color-accent)] text-sm">{error}</p>}

      <button
        type="submit"
        disabled={status === "loading"}
        data-cursor="hover"
        className="btn-pill btn-solid self-start disabled:opacity-60"
      >
        {status === "loading" ? "در حال ارسال..." : "ارسال پیام ↗"}
      </button>
    </form>
  );
}
