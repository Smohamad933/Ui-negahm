import Link from "next/link";
import Hero from "@/components/site/Hero";
import Marquee from "@/components/site/Marquee";
import Reveal from "@/components/site/Reveal";
import WorkCard from "@/components/site/WorkCard";
import BrandGrid from "@/components/site/BrandGrid";
import { getSettings, getClients, getFeaturedItems, getFeaturedClients } from "@/lib/queries";

export const dynamic = "force-dynamic";

const SERVICES = [
  { n: "۰۱", title: "هویت بصری و برندینگ", desc: "طراحی لوگو، سیستم گرافیکی و زبان بصری منسجم." },
  { n: "۰۲", title: "تولید محتوای خلاق", desc: "ایده‌پردازی، عکاسی، تصویربرداری، طراحی و شبکه‌های اجتماعی." },
  { n: "۰۳", title: "دیجیتال مارکتینگ", desc: "استراتژی محتوا، مدیریت سوشال مدیا و تبلیغات دیجیتال." },
  { n: "۰۴", title: "کمپین تبلیغاتی", desc: "تدوین کانسپت، سناریونویسی، تولید و انتشار یکپارچه." },
  { n: "۰۵", title: "طراحی وب", desc: "طراحی رابط و تجربه کاربری (UI/UX) منطبق بر هویت بصری برند." },
  { n: "۰۶", title: "استراتژی و مشاوره", desc: "تحلیل مسئله و تبدیل اهداف بیزینس به نقشه راه اجرایی." },
];

export default function HomePage() {
  const settings = getSettings();
  const clients = getClients({ onlyPublished: true });
  const featuredClients = getFeaturedClients();
  const featured = getFeaturedItems(7);

  return (
    <div>
      <Hero
        title={settings.hero_title}
        subtitle={settings.hero_subtitle}
        ctaText={settings.hero_cta_text}
        ctaLink={settings.hero_cta_link}
      />

      {clients.length > 0 && (
        <section className="py-6 my-16">
          <div className="marquee-band py-8">
            <Marquee>
              {clients.map((c) => (
                <Link
                  key={c.id}
                  href={`/clients/${c.slug}`}
                  data-cursor="hover"
                  className="flex items-center gap-3 px-10 shrink-0 opacity-80 hover:opacity-100 transition-opacity"
                >
                  {c.logo_url ? (
                    // eslint-disable-next-line @next/next/no-img-element
                    <img src={c.logo_url} alt={c.name} className="h-8 w-auto brightness-0 invert" />
                  ) : (
                    <span className="font-display text-2xl md:text-3xl font-bold whitespace-nowrap">{c.name} ✦</span>
                  )}
                </Link>
              ))}
            </Marquee>
          </div>
        </section>
      )}

      <BrandGrid clients={featuredClients} />

      {featured.length > 0 && (
        <section className="container-px py-28">
          <Reveal>
            <span className="eyebrow">نمونه‌کارهای برتر</span>
            <h2 className="h-section mt-5 max-w-3xl">
              گزیده‌ای از پروژه‌هایی که براشون افتخار می‌کنیم.
            </h2>
          </Reveal>

          <div className="columns-1 sm:columns-2 lg:columns-3 gap-5 mt-16 [column-fill:_balance]">
            {featured.map((item, i) => (
              <Reveal key={item.id} delay={i * 60} className="mb-5 break-inside-avoid">
                <WorkCard
                  href={`/clients/${item.client_slug}/${item.category_slug}`}
                  title={item.title || item.category_title}
                  clientName={item.client_name}
                  categoryTitle={item.category_title}
                  mediaUrl={item.media_url}
                  mediaType={item.media_type}
                  aspectRatio={item.category_aspect_ratio}
                />
              </Reveal>
            ))}
          </div>

          <Reveal className="mt-14 flex justify-center">
            <Link href="/clients" data-cursor="hover" className="btn-pill">
              مشاهده همه همراهان ↗
            </Link>
          </Reveal>
        </section>
      )}

      <section className="container-px py-28 grid md:grid-cols-2 gap-16 items-center">
        <Reveal>
          {settings.about_image_url ? (
            // eslint-disable-next-line @next/next/no-img-element
            <img
              src={settings.about_image_url}
              alt={settings.about_title}
              className="w-full aspect-[4/5] object-cover rounded-3xl frame-pop"
            />
          ) : (
            <div
              className="w-full aspect-[4/5] rounded-3xl frame-pop"
              style={{ background: "linear-gradient(135deg, var(--color-secondary), var(--color-primary))" }}
            />
          )}
        </Reveal>
        <Reveal delay={100}>
          <span className="eyebrow">درباره استودیو</span>
          <h2 className="h-section mt-5">{settings.about_title}</h2>
          <p className="text-[var(--color-muted)] text-lg leading-relaxed mt-6 max-w-xl">
            {settings.about_body}
          </p>
          <Link href="/about" data-cursor="hover" className="btn-pill mt-8 inline-flex">
            بیشتر بدانید ↗
          </Link>
        </Reveal>
      </section>

      <section className="container-px py-28">
        <Reveal>
          <span className="eyebrow">خدمات ما</span>
        </Reveal>
        <div className="mt-10">
          {SERVICES.map((s, i) => (
            <Reveal key={s.n} delay={i * 60}>
              <div className="group grid md:grid-cols-[100px_1fr_1fr] gap-4 md:items-center py-8 border-b-[3px] border-dashed border-[color-mix(in_srgb,var(--color-fg)_45%,transparent)] transition-colors">
                <span className="font-display text-[var(--color-muted)]">{s.n}</span>
                <h3 className="text-2xl md:text-3xl font-bold group-hover:text-[var(--color-primary)] transition-colors duration-300">
                  {s.title}
                </h3>
                <p className="text-[var(--color-muted)] leading-relaxed">{s.desc}</p>
              </div>
            </Reveal>
          ))}
        </div>
      </section>
    </div>
  );
}
