"use client";

import { useEffect, useState } from "react";
import Link from "next/link";
import type { Client } from "@/lib/queries";

export default function AdminClientsPage() {
  const [clients, setClients] = useState<Client[] | null>(null);

  async function refresh() {
    const res = await fetch("/api/admin/clients");
    setClients(await res.json());
  }

  useEffect(() => {
    refresh();
  }, []);

  async function move(index: number, dir: -1 | 1) {
    if (!clients) return;
    const target = index + dir;
    if (target < 0 || target >= clients.length) return;
    const next = [...clients];
    [next[index], next[target]] = [next[target], next[index]];
    setClients(next);
    await fetch("/api/admin/clients/reorder", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ orderedIds: next.map((c) => c.id) }),
    });
  }

  async function remove(id: number) {
    if (!confirm("این کارفرما و تمام نمونه‌کارهاش حذف می‌شه. مطمئنی؟")) return;
    await fetch(`/api/admin/clients/${id}`, { method: "DELETE" });
    refresh();
  }

  async function togglePublished(client: Client) {
    await fetch(`/api/admin/clients/${client.id}`, {
      method: "PATCH",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ published: !client.published }),
    });
    refresh();
  }

  async function toggleFeatured(client: Client) {
    await fetch(`/api/admin/clients/${client.id}`, {
      method: "PATCH",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ featured: !client.featured }),
    });
    refresh();
  }

  return (
    <div>
      <div className="flex items-center justify-between mb-8">
        <div>
          <h1 className="text-2xl font-bold">کارفرمایان</h1>
          <p style={{ color: "var(--a-muted)" }} className="text-sm mt-1">
            مدیریت کارفرماها، دسته‌بندی نمونه‌کارها و ترتیب نمایش در سایت
          </p>
        </div>
        <Link href="/admin/clients/new" className="admin-btn admin-btn-primary">+ افزودن کارفرما</Link>
      </div>

      {!clients ? (
        <p style={{ color: "var(--a-muted)" }}>در حال بارگذاری...</p>
      ) : clients.length === 0 ? (
        <div className="admin-card p-10 text-center" style={{ color: "var(--a-muted)" }}>
          هنوز کارفرمایی اضافه نکردی. با دکمه بالا شروع کن.
        </div>
      ) : (
        <div className="admin-card overflow-x-auto">
          <table className="admin-table">
            <thead>
              <tr>
                <th></th>
                <th>لوگو</th>
                <th>نام</th>
                <th>صنعت</th>
                <th>وضعیت</th>
                <th>ویژه</th>
                <th></th>
              </tr>
            </thead>
            <tbody>
              {clients.map((c, i) => (
                <tr key={c.id}>
                  <td>
                    <div className="flex flex-col gap-1">
                      <button onClick={() => move(i, -1)} className="admin-btn !p-1 !px-2 text-xs" disabled={i === 0}>▲</button>
                      <button onClick={() => move(i, 1)} className="admin-btn !p-1 !px-2 text-xs" disabled={i === clients.length - 1}>▼</button>
                    </div>
                  </td>
                  <td>
                    <div className="w-12 h-12 rounded-lg overflow-hidden" style={{ background: "var(--a-panel-2)" }}>
                      {c.logo_url && (
                        // eslint-disable-next-line @next/next/no-img-element
                        <img src={c.logo_url} alt={c.name} className="w-full h-full object-cover" />
                      )}
                    </div>
                  </td>
                  <td className="font-bold">{c.name}</td>
                  <td style={{ color: "var(--a-muted)" }}>{c.industry || "—"}</td>
                  <td>
                    <button onClick={() => togglePublished(c)} className="admin-badge" style={{ borderColor: c.published ? "var(--a-success)" : "var(--a-border)", color: c.published ? "var(--a-success)" : "var(--a-muted)" }}>
                      {c.published ? "منتشر شده" : "پیش‌نویس"}
                    </button>
                  </td>
                  <td>
                    <button onClick={() => toggleFeatured(c)} className="admin-badge" style={{ borderColor: c.featured ? "var(--a-primary)" : "var(--a-border)", color: c.featured ? "var(--a-primary)" : "var(--a-muted)" }}>
                      {c.featured ? "ویژه" : "عادی"}
                    </button>
                  </td>
                  <td>
                    <div className="flex gap-2 justify-end">
                      <Link href={`/admin/clients/${c.id}`} className="admin-btn text-xs">ویرایش</Link>
                      <button onClick={() => remove(c.id)} className="admin-btn admin-btn-danger text-xs">حذف</button>
                    </div>
                  </td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>
      )}
    </div>
  );
}
