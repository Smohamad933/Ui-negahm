"use client";

import { useEffect, useState } from "react";
import type { Message } from "@/lib/queries";

export default function AdminMessagesPage() {
  const [messages, setMessages] = useState<Message[] | null>(null);
  const [openId, setOpenId] = useState<number | null>(null);

  async function refresh() {
    const res = await fetch("/api/admin/messages");
    setMessages(await res.json());
  }

  useEffect(() => {
    refresh();
  }, []);

  async function toggleOpen(m: Message) {
    setOpenId(openId === m.id ? null : m.id);
    if (!m.is_read) {
      await fetch(`/api/admin/messages/${m.id}`, {
        method: "PATCH",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ is_read: true }),
      });
      refresh();
    }
  }

  async function remove(id: number) {
    if (!confirm("این پیام حذف بشه؟")) return;
    await fetch(`/api/admin/messages/${id}`, { method: "DELETE" });
    refresh();
  }

  return (
    <div>
      <h1 className="text-2xl font-bold mb-1">پیام‌های تماس با ما</h1>
      <p style={{ color: "var(--a-muted)" }} className="mb-8 text-sm">پیام‌های ارسالی از فرم تماس با ما</p>

      {!messages ? (
        <p style={{ color: "var(--a-muted)" }}>در حال بارگذاری...</p>
      ) : messages.length === 0 ? (
        <div className="admin-card p-10 text-center" style={{ color: "var(--a-muted)" }}>هنوز پیامی دریافت نشده.</div>
      ) : (
        <div className="flex flex-col gap-3 max-w-3xl">
          {messages.map((m) => (
            <div key={m.id} className="admin-card p-4">
              <button onClick={() => toggleOpen(m)} className="w-full flex items-center justify-between gap-4 text-right">
                <div className="flex items-center gap-3">
                  {!m.is_read && <span className="w-2 h-2 rounded-full" style={{ background: "var(--a-danger)" }} />}
                  <div>
                    <p className="font-bold">{m.name} {m.subject && `· ${m.subject}`}</p>
                    <p className="text-xs" style={{ color: "var(--a-muted)" }}>{m.email} · {new Date(m.created_at).toLocaleString("fa-IR")}</p>
                  </div>
                </div>
                <span>{openId === m.id ? "▾" : "◂"}</span>
              </button>
              {openId === m.id && (
                <div className="mt-4 pt-4 flex flex-col gap-3" style={{ borderTop: "1px solid var(--a-border)" }}>
                  <p className="text-sm leading-relaxed whitespace-pre-wrap">{m.message}</p>
                  {m.phone && <p className="text-sm" style={{ color: "var(--a-muted)" }}>تلفن: {m.phone}</p>}
                  <div className="flex gap-2">
                    <a href={`mailto:${m.email}`} className="admin-btn text-xs">پاسخ با ایمیل</a>
                    <button onClick={() => remove(m.id)} className="admin-btn admin-btn-danger text-xs">حذف</button>
                  </div>
                </div>
              )}
            </div>
          ))}
        </div>
      )}
    </div>
  );
}
