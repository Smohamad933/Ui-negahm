"use client";

import Link from "next/link";
import { useEffect, useState } from "react";
import { usePathname } from "next/navigation";
import { AnimatePresence, motion } from "framer-motion";
import type { Settings } from "@/lib/queries";

const NAV_LINKS = [
  { href: "/", label: "خانه", num: "۰۱" },
  { href: "/clients", label: "کارفرمایان", num: "۰۲" },
  { href: "/about", label: "درباره ما", num: "۰۳" },
  { href: "/contact", label: "تماس با ما", num: "۰۴" },
];

export default function Header({ settings }: { settings: Settings }) {
  const [open, setOpen] = useState(false);
  const pathname = usePathname();

  useEffect(() => {
    setOpen(false);
  }, [pathname]);

  useEffect(() => {
    document.documentElement.style.overflow = open ? "hidden" : "";
  }, [open]);

  return (
    <>
      <header className="fixed top-0 inset-x-0 z-50 container-px py-5 flex items-center justify-between mix-blend-difference">
        <Link href="/" className="flex items-center gap-3" data-cursor="hover">
          {settings.logo_url ? (
            // eslint-disable-next-line @next/next/no-img-element
            <img src={settings.logo_url} alt={settings.site_name} className="h-9 w-auto" />
          ) : (
            <span className="font-display font-extrabold text-lg tracking-tight text-[var(--color-fg)]">
              {settings.site_name}
            </span>
          )}
        </Link>

        <button
          onClick={() => setOpen((v) => !v)}
          data-cursor="hover"
          className="relative z-[70] flex items-center gap-3 font-display text-xs uppercase tracking-[0.25em] text-[var(--color-fg)]"
        >
          <span>{open ? "بستن" : "منو"}</span>
          <span className="relative flex h-9 w-9 flex-col items-center justify-center gap-[6px]">
            <span
              className={`h-[1.5px] w-6 bg-current transition-transform duration-300 ${
                open ? "translate-y-[3.5px] rotate-45" : ""
              }`}
            />
            <span
              className={`h-[1.5px] w-6 bg-current transition-transform duration-300 ${
                open ? "-translate-y-[3.5px] -rotate-45" : ""
              }`}
            />
          </span>
        </button>
      </header>

      <AnimatePresence>
        {open && (
          <motion.div
            initial={{ clipPath: "inset(0% 0% 100% 0%)" }}
            animate={{ clipPath: "inset(0% 0% 0% 0%)" }}
            exit={{ clipPath: "inset(0% 0% 100% 0%)" }}
            transition={{ duration: 0.7, ease: [0.16, 1, 0.3, 1] }}
            className="fixed inset-0 z-[60] flex flex-col justify-between bg-[var(--color-bg)] text-[var(--color-fg)] container-px pt-28 pb-10"
          >
            <nav className="flex flex-col gap-2">
              {NAV_LINKS.map((link, i) => (
                <motion.div
                  key={link.href}
                  initial={{ y: 60, opacity: 0 }}
                  animate={{ y: 0, opacity: 1 }}
                  transition={{ delay: 0.15 + i * 0.07, duration: 0.6, ease: [0.16, 1, 0.3, 1] }}
                  className="border-b border-[color-mix(in_srgb,var(--color-fg)_14%,transparent)] py-4 flex items-center justify-between group"
                >
                  <Link
                    href={link.href}
                    data-cursor="hover"
                    className="font-display font-extrabold text-[clamp(2.2rem,7vw,5.5rem)] leading-none tracking-tight group-hover:text-[var(--color-primary)] transition-colors duration-300"
                  >
                    {link.label}
                  </Link>
                  <span className="hidden md:block text-sm text-[var(--color-muted)] font-display">
                    {link.num}
                  </span>
                </motion.div>
              ))}
            </nav>

            <motion.div
              initial={{ opacity: 0 }}
              animate={{ opacity: 1 }}
              transition={{ delay: 0.5, duration: 0.6 }}
              className="flex flex-wrap items-end justify-between gap-6 text-sm text-[var(--color-muted)]"
            >
              <div className="flex flex-col gap-1">
                <span className="eyebrow">ارتباط</span>
                <a href={`mailto:${settings.contact_email}`} className="text-[var(--color-fg)]">
                  {settings.contact_email}
                </a>
                <span dir="ltr" className="text-left">{settings.contact_phone}</span>
              </div>
              <div className="flex gap-5 font-display uppercase tracking-widest text-xs">
                {settings.social_instagram && (
                  <a href={settings.social_instagram} target="_blank" data-cursor="hover">Instagram</a>
                )}
                {settings.social_telegram && (
                  <a href={settings.social_telegram} target="_blank" data-cursor="hover">Telegram</a>
                )}
                {settings.social_linkedin && (
                  <a href={settings.social_linkedin} target="_blank" data-cursor="hover">LinkedIn</a>
                )}
              </div>
            </motion.div>
          </motion.div>
        )}
      </AnimatePresence>
    </>
  );
}
