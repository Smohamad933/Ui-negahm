import Link from "next/link";
import type { Settings } from "@/lib/queries";

export default function Footer({ settings }: { settings: Settings }) {
  const year = new Date().getFullYear();
  return (
    <footer className="relative container-px pt-24 pb-10 mt-20 border-t border-[color-mix(in_srgb,var(--color-fg)_12%,transparent)]">
      <div className="flex flex-col gap-14">
        <div className="flex flex-col md:flex-row md:items-end md:justify-between gap-8">
          <div>
            <span className="eyebrow">همکاری با ما</span>
            <h3 className="h-section mt-4 max-w-xl">
              ایده‌ی بعدی برندت رو با هم بسازیم.
            </h3>
          </div>
          <Link href="/contact" data-cursor="hover" className="btn-pill btn-solid whitespace-nowrap">
            شروع پروژه ↗
          </Link>
        </div>

        <div className="hairline" />

        <div className="grid grid-cols-2 md:grid-cols-4 gap-8 text-sm">
          <div className="flex flex-col gap-3">
            <span className="eyebrow">صفحات</span>
            <Link href="/" className="hover:text-[var(--color-primary)] transition-colors">خانه</Link>
            <Link href="/clients" className="hover:text-[var(--color-primary)] transition-colors">کارفرمایان</Link>
            <Link href="/about" className="hover:text-[var(--color-primary)] transition-colors">درباره ما</Link>
            <Link href="/contact" className="hover:text-[var(--color-primary)] transition-colors">تماس با ما</Link>
          </div>
          <div className="flex flex-col gap-3">
            <span className="eyebrow">تماس</span>
            <a href={`mailto:${settings.contact_email}`} className="hover:text-[var(--color-primary)] transition-colors">{settings.contact_email}</a>
            <span dir="ltr" className="text-right md:text-left">{settings.contact_phone}</span>
            <span>{settings.contact_address}</span>
          </div>
          <div className="flex flex-col gap-3">
            <span className="eyebrow">شبکه‌های اجتماعی</span>
            {settings.social_instagram && <a href={settings.social_instagram} target="_blank" className="hover:text-[var(--color-primary)] transition-colors">اینستاگرام</a>}
            {settings.social_telegram && <a href={settings.social_telegram} target="_blank" className="hover:text-[var(--color-primary)] transition-colors">تلگرام</a>}
            {settings.social_whatsapp && <a href={settings.social_whatsapp} target="_blank" className="hover:text-[var(--color-primary)] transition-colors">واتس‌اپ</a>}
            {settings.social_linkedin && <a href={settings.social_linkedin} target="_blank" className="hover:text-[var(--color-primary)] transition-colors">لینکدین</a>}
          </div>
          <div className="flex flex-col gap-3">
            <span className="eyebrow">استودیو</span>
            <span className="text-[var(--color-muted)]">{settings.tagline}</span>
          </div>
        </div>

        <div className="hairline" />

        <div className="flex flex-col md:flex-row justify-between gap-3 text-xs text-[var(--color-muted)] font-display uppercase tracking-widest">
          <span>© {year} {settings.site_name}</span>
          <span>{settings.footer_text}</span>
        </div>
      </div>
    </footer>
  );
}
