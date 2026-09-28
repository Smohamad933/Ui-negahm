import type { Metadata } from "next";
import Reveal from "@/components/site/Reveal";
import CountUp from "@/components/site/CountUp";
import { db } from "@/lib/db";
import { getSettings } from "@/lib/queries";

export const dynamic = "force-dynamic";
export const metadata: Metadata = { title: "درباره ما" };

const VALUES = [
  { title: "خلاقیت بی‌مرز", desc: "هر پروژه رو با نگاه تازه و ایده‌ی متفاوت شروع می‌کنیم." },
  { title: "دقت در اجرا", desc: "از استراتژی تا پیکسل آخر، کیفیت اجرایی برامون اولویت اول‌ه." },
  { title: "شراکت بلندمدت", desc: "با کارفرماها مثل شریک تجاری رفتار می‌کنیم نه فقط پیمانکار." },
];

export default function AboutPage() {
  const settings = getSettings();
  const clientCount = (db.prepare("SELECT COUNT(*) as c FROM clients WHERE published = 1").get() as { c: number }).c;
  const projectCount = (db.prepare("SELECT COUNT(*) as c FROM categories").get() as { c: number }).c;
  const itemCount = (db.prepare("SELECT COUNT(*) as c FROM portfolio_items").get() as { c: number }).c;

  return (
    <div className="container-px py-10">
      <Reveal>
        <span className="eyebrow">درباره ما</span>
        <h1 className="h-hero font-display mt-6 max-w-4xl">{settings.about_title}</h1>
      </Reveal>

      <div className="grid md:grid-cols-2 gap-16 mt-20 items-start">
        <Reveal>
          {settings.about_image_url ? (
            // eslint-disable-next-line @next/next/no-img-element
            <img src={settings.about_image_url} alt={settings.about_title} className="w-full aspect-[4/5] object-cover rounded-3xl sticky top-28 frame-pop" />
          ) : (
            <div className="w-full aspect-[4/5] rounded-3xl sticky top-28 frame-pop" style={{ background: "linear-gradient(135deg, var(--color-secondary), var(--color-accent))" }} />
          )}
        </Reveal>
        <div className="flex flex-col gap-14">
          <Reveal>
            <p className="text-xl md:text-2xl leading-relaxed text-[var(--color-fg)]">{settings.about_body}</p>
          </Reveal>

          <Reveal delay={80}>
            <div className="grid grid-cols-3 gap-6 py-10 border-y-[3px] border-dashed border-[color-mix(in_srgb,var(--color-fg)_55%,transparent)]">
              <div>
                <p className="font-display text-4xl md:text-5xl font-extrabold text-[var(--color-primary)]">
                  <CountUp value={clientCount} suffix="+" />
                </p>
                <p className="text-sm text-[var(--color-muted)] mt-2">کارفرما</p>
              </div>
              <div>
                <p className="font-display text-4xl md:text-5xl font-extrabold text-[var(--color-primary)]">
                  <CountUp value={projectCount} suffix="+" />
                </p>
                <p className="text-sm text-[var(--color-muted)] mt-2">پروژه</p>
              </div>
              <div>
                <p className="font-display text-4xl md:text-5xl font-extrabold text-[var(--color-primary)]">
                  <CountUp value={itemCount} suffix="+" />
                </p>
                <p className="text-sm text-[var(--color-muted)] mt-2">نمونه‌کار</p>
              </div>
            </div>
          </Reveal>

          <div className="flex flex-col gap-8">
            {VALUES.map((v, i) => (
              <Reveal key={v.title} delay={i * 70}>
                <div className="flex gap-5">
                  <span className="font-display text-[var(--color-muted)] text-sm pt-1">{(i + 1).toLocaleString("fa-IR")}</span>
                  <div>
                    <h3 className="text-xl font-bold">{v.title}</h3>
                    <p className="text-[var(--color-muted)] mt-2 leading-relaxed">{v.desc}</p>
                  </div>
                </div>
              </Reveal>
            ))}
          </div>
        </div>
      </div>
    </div>
  );
}
