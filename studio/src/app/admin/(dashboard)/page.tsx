import Link from "next/link";
import { db } from "@/lib/db";
import { getUnreadMessageCount } from "@/lib/queries";

export const dynamic = "force-dynamic";

export default function AdminDashboardPage() {
  const clientCount = (db.prepare("SELECT COUNT(*) as c FROM clients").get() as { c: number }).c;
  const categoryCount = (db.prepare("SELECT COUNT(*) as c FROM categories").get() as { c: number }).c;
  const itemCount = (db.prepare("SELECT COUNT(*) as c FROM portfolio_items").get() as { c: number }).c;
  const unread = getUnreadMessageCount();

  const cards = [
    { label: "کارفرمایان", value: clientCount, href: "/admin/clients" },
    { label: "دسته‌بندی نمونه‌کار", value: categoryCount, href: "/admin/clients" },
    { label: "آیتم‌های نمونه‌کار", value: itemCount, href: "/admin/clients" },
    { label: "پیام‌های خوانده‌نشده", value: unread, href: "/admin/messages" },
  ];

  return (
    <div>
      <h1 className="text-2xl font-bold mb-1">داشبورد</h1>
      <p style={{ color: "var(--a-muted)" }} className="mb-8">خلاصه وضعیت سایت استودیو نگاهم</p>

      <div className="grid sm:grid-cols-2 lg:grid-cols-4 gap-4">
        {cards.map((c) => (
          <Link key={c.label} href={c.href} className="admin-card p-6 block hover:opacity-90 transition-opacity">
            <p className="text-3xl font-extrabold">{c.value}</p>
            <p style={{ color: "var(--a-muted)" }} className="text-sm mt-2">{c.label}</p>
          </Link>
        ))}
      </div>

      <div className="admin-card p-6 mt-8 flex flex-wrap gap-4">
        <Link href="/admin/clients/new" className="admin-btn admin-btn-primary">+ افزودن کارفرمای جدید</Link>
        <Link href="/admin/settings" className="admin-btn">تنظیمات ظاهری سایت</Link>
        <Link href="/" target="_blank" className="admin-btn">مشاهده سایت ↗</Link>
      </div>
    </div>
  );
}
