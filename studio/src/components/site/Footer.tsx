import Link from "next/link";
import type { Settings } from "@/lib/queries";

export default function Footer({ settings }: { settings: Settings }) {
  const year = new Date().getFullYear();
  return (
    <footer
      className="relative container-px pt-24 pb-10 mt-20"
      style={{ background: "var(--color-fg)", color: "var(--color-bg)" }}
    >
      <div className="flex flex-col gap-14">
        <div className="flex flex-col md:flex-row md:items-end md:justify-between gap-8">
          <div>
            <span className="eyebrow eyebrow-invert">همکاری با ما</span>
            <h3 className="h-section mt-4 max-w-xl" style={{ color: "var(--color-bg)" }}>
              ایده‌ی بعدی برندت رو با هم بسازیم.
            </h3>
          </div>
          <Link href="/contact" data-cursor="hover" className="btn-pill btn-solid btn-invert whitespace-nowrap">
            شروع پروژه ↗
          </Link>
        </div>

        <div style={{ borderTop: "3px dashed color-mix(in srgb, var(--color-bg) 35%, transparent)" }} />

        <div className="grid grid-cols-2 md:grid-cols-4 gap-8 text-sm">
          <div className="flex flex-col gap-3">
            <span className="opacity-60 font-display uppercase tracking-widest text-xs font-bold">صفحات</span>
            <Link href="/" className="hover:text-[var(--color-accent)] transition-colors">خانه</Link>
            <Link href="/clients" className="hover:text-[var(--color-accent)] transition-colors">کارفرمایان</Link>
            <Link href="/about" className="hover:text-[var(--color-accent)] transition-colors">درباره ما</Link>
            <Link href="/contact" className="hover:text-[var(--color-accent)] transition-colors">تماس با ما</Link>
          </div>
          <div className="flex flex-col gap-3">
            <span className="opacity-60 font-display uppercase tracking-widest text-xs font-bold">تماس</span>
            <a href={`mailto:${settings.contact_email}`} className="hover:text-[var(--color-accent)] transition-colors">{settings.contact_email}</a>
            <span dir="ltr" className="text-right md:text-left">{settings.contact_phone}</span>
            <span className="opacity-80">{settings.contact_address}</span>
          </div>
          <div className="flex flex-col gap-3">
            <span className="opacity-60 font-display uppercase tracking-widest text-xs font-bold">شبکه‌های اجتماعی</span>
            {settings.social_instagram && <a href={settings.social_instagram} target="_blank" className="hover:text-[var(--color-accent)] transition-colors">اینستاگرام</a>}
            {settings.social_telegram && <a href={settings.social_telegram} target="_blank" className="hover:text-[var(--color-accent)] transition-colors">تلگرام</a>}
            {settings.social_whatsapp && <a href={settings.social_whatsapp} target="_blank" className="hover:text-[var(--color-accent)] transition-colors">واتس‌اپ</a>}
            {settings.social_linkedin && <a href={settings.social_linkedin} target="_blank" className="hover:text-[var(--color-accent)] transition-colors">لینکدین</a>}
          </div>
          <div className="flex flex-col gap-3">
            <span className="opacity-60 font-display uppercase tracking-widest text-xs font-bold">استودیو</span>
            <span className="opacity-80">{settings.tagline}</span>
          </div>
        </div>

        <div style={{ borderTop: "3px dashed color-mix(in srgb, var(--color-bg) 35%, transparent)" }} />

        <div className="flex flex-col md:flex-row justify-between gap-3 text-xs opacity-70 font-display uppercase tracking-widest font-bold">
          <span>© {year} {settings.site_name}</span>
          <span>{settings.footer_text}</span>
        </div>
      </div>
    </footer>
  );
}
