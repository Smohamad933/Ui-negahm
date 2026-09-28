import type { Metadata } from "next";
import Link from "next/link";
import { notFound } from "next/navigation";
import Reveal from "@/components/site/Reveal";
import { getClientBySlug, getCategoriesByClient, getItemsByCategory } from "@/lib/queries";
import { ASPECT_CLASS, normalizeAspectRatio } from "@/lib/aspect";

export const dynamic = "force-dynamic";

export async function generateMetadata({
  params,
}: {
  params: Promise<{ slug: string }>;
}): Promise<Metadata> {
  const { slug } = await params;
  const client = getClientBySlug(slug);
  return { title: client ? client.name : "همراه" };
}

export default async function ClientDetailPage({
  params,
}: {
  params: Promise<{ slug: string }>;
}) {
  const { slug } = await params;
  const client = getClientBySlug(slug);
  if (!client || !client.published) notFound();

  const categories = getCategoriesByClient(client.id);
  const categoriesWithCover = categories.map((cat) => {
    if (cat.cover_image_url) return cat;
    const items = getItemsByCategory(cat.id);
    return { ...cat, cover_image_url: items[0]?.media_url || "" };
  });

  return (
    <div>
      <div className="container-px py-10">
        <Reveal>
          <span className="eyebrow">{client.industry || "همراه"}</span>
          <h1 className="h-hero font-display mt-6 max-w-5xl">{client.name}</h1>
        </Reveal>

        <div className="grid md:grid-cols-[1fr_1fr] gap-10 mt-14 items-start">
          {client.short_description && (
            <Reveal className="text-xl leading-relaxed text-[var(--color-muted)] max-w-xl">
              {client.short_description}
            </Reveal>
          )}
          <Reveal delay={80} className="flex flex-wrap gap-x-12 gap-y-4 md:justify-end">
            {client.year && (
              <div>
                <span className="eyebrow">سال همکاری</span>
                <p className="text-lg mt-2">{client.year}</p>
              </div>
            )}
            {client.website_url && (
              <div>
                <span className="eyebrow">وب‌سایت</span>
                <a href={client.website_url} target="_blank" data-cursor="hover" className="block text-lg mt-2 hover:text-[var(--color-primary)] transition-colors" dir="ltr">
                  {client.website_url.replace(/^https?:\/\//, "")}
                </a>
              </div>
            )}
          </Reveal>
        </div>
      </div>

      {client.cover_image_url && (
        <Reveal className="container-px mt-16">
          {/* eslint-disable-next-line @next/next/no-img-element */}
          <img src={client.cover_image_url} alt={client.name} className="w-full rounded-3xl aspect-[16/8] object-cover frame-pop" />
        </Reveal>
      )}

      <div className="container-px py-24">
        <Reveal>
          <span className="eyebrow">نمونه‌کارها</span>
          <h2 className="h-section mt-5">دسته‌بندی پروژه‌های {client.name}</h2>
        </Reveal>

        {categoriesWithCover.length === 0 ? (
          <p className="text-[var(--color-muted)] mt-10">هنوز نمونه‌کاری برای این همراه ثبت نشده است.</p>
        ) : (
          <div className="flex flex-wrap gap-5 mt-14">
            {categoriesWithCover.map((cat, i) => {
              const ratio = normalizeAspectRatio(cat.aspect_ratio);
              return (
                <Reveal key={cat.id} delay={i * 70} className="shrink-0">
                  <Link
                    href={`/clients/${client.slug}/${cat.slug}`}
                    data-cursor="hover"
                    className={`client-card group relative block overflow-hidden h-56 sm:h-64 md:h-72 ${ASPECT_CLASS[ratio]}`}
                  >
                    {cat.cover_image_url ? (
                      // eslint-disable-next-line @next/next/no-img-element
                      <img src={cat.cover_image_url} alt={cat.title} className="absolute inset-0 w-full h-full object-cover transition-transform duration-700 group-hover:scale-[1.06]" />
                    ) : (
                      <div className="absolute inset-0" style={{ background: "linear-gradient(150deg, var(--color-secondary), var(--color-accent))" }} />
                    )}
                    <div className="absolute inset-0 bg-gradient-to-t from-black/80 via-black/10 to-transparent" />
                    <div className="absolute inset-x-0 bottom-0 p-5 sm:p-7 flex items-end justify-between gap-4">
                      <div>
                        <h3 className="text-white text-lg sm:text-2xl font-bold">{cat.title}</h3>
                        {cat.description && ratio !== "9:16" && (
                          <p className="text-white/60 text-sm mt-2 line-clamp-2 max-w-md">{cat.description}</p>
                        )}
                      </div>
                      <span
                        className="shrink-0 h-9 w-9 sm:h-11 sm:w-11 rounded-full border-[2.5px] flex items-center justify-center font-bold transition-transform duration-500 group-hover:rotate-45"
                        style={{ background: "var(--color-accent)", borderColor: "var(--color-fg)", color: "var(--color-fg)" }}
                      >
                        ↗
                      </span>
                    </div>
                  </Link>
                </Reveal>
              );
            })}
          </div>
        )}

        <Reveal className="mt-16">
          <Link href="/clients" data-cursor="hover" className="btn-pill">← بازگشت به همراهان</Link>
        </Reveal>
      </div>
    </div>
  );
}
