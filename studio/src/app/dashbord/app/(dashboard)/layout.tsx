import Link from "next/link";
import { getSession } from "@/lib/auth";
import { getUnreadMessageCount } from "@/lib/queries";
import NavLink from "@/components/admin/NavLink";
import LogoutButton from "@/components/admin/LogoutButton";

export const dynamic = "force-dynamic";

export default async function DashboardLayout({ children }: { children: React.ReactNode }) {
  const session = await getSession();
  const unread = getUnreadMessageCount();

  return (
    <div className="admin-scope flex min-h-dvh">
      <aside className="hidden md:flex w-64 shrink-0 flex-col justify-between border-l p-5" style={{ borderColor: "var(--a-border)" }}>
        <div className="flex flex-col gap-8">
          <div>
            <p className="text-xs tracking-[0.2em] uppercase" style={{ color: "var(--a-muted)" }}>پنل مدیریت</p>
            <p className="font-bold text-lg mt-1">نگاه مدیا</p>
          </div>
          <nav className="flex flex-col gap-1">
            <NavLink href="/dashbord/app" exact>داشبورد</NavLink>
            <NavLink href="/dashbord/app/clients">کارفرمایان</NavLink>
            <NavLink href="/dashbord/app/settings">تنظیمات سایت</NavLink>
            <NavLink href="/dashbord/app/messages">
              پیام‌های تماس {unread > 0 && <span className="admin-badge" style={{ background: "var(--a-danger)", color: "#fff", borderColor: "var(--a-danger)" }}>{unread}</span>}
            </NavLink>
          </nav>
        </div>
        <div className="flex flex-col gap-3">
          <Link href="/" target="_blank" className="admin-nav-link">مشاهده سایت ↗</Link>
          <p className="text-xs px-3" style={{ color: "var(--a-muted)" }}>{session?.username}</p>
          <LogoutButton />
        </div>
      </aside>

      <div className="flex-1 min-w-0">
        <header className="md:hidden flex items-center justify-between p-4 border-b" style={{ borderColor: "var(--a-border)" }}>
          <p className="font-bold">پنل مدیریت</p>
          <Link href="/dashbord/app/clients" className="admin-btn">کارفرمایان</Link>
        </header>
        <main className="p-5 md:p-10">{children}</main>
      </div>
    </div>
  );
}
