import type { Metadata } from "next";
import Link from "next/link";
import { notFound } from "next/navigation";
import Reveal from "@/components/site/Reveal";
import Gallery from "@/components/site/Gallery";
import { getClientBySlug, getCategoryBySlug, getItemsByCategory, getCategoriesByClient } from "@/lib/queries";

export const dynamic = "force-dynamic";

export async function generateMetadata({
  params,
}: {
  params: Promise<{ slug: string; categorySlug: string }>;
}): Promise<Metadata> {
  const { slug, categorySlug } = await params;
  const client = getClientBySlug(slug);
  if (!client) return {};
  const category = getCategoryBySlug(client.id, categorySlug);
  return { title: category ? `${category.title} · ${client.name}` : client.name };
}

export default async function CategoryPage({
  params,
}: {
  params: Promise<{ slug: string; categorySlug: string }>;
}) {
  const { slug, categorySlug } = await params;
  const client = getClientBySlug(slug);
  if (!client || !client.published) notFound();

  const category = getCategoryBySlug(client.id, categorySlug);
  if (!category) notFound();

  const items = getItemsByCategory(category.id);
  const otherCategories = getCategoriesByClient(client.id).filter((c) => c.id !== category.id);

  return (
    <div className="container-px py-10">
      <Reveal>
        <Link href={`/clients/${client.slug}`} data-cursor="hover" className="eyebrow">
          {client.name}
        </Link>
        <h1 className="h-hero font-display mt-6 max-w-4xl">{category.title}</h1>
        {category.description && (
          <p className="text-xl text-[var(--color-muted)] mt-6 max-w-2xl leading-relaxed">{category.description}</p>
        )}
      </Reveal>

      <div className="mt-16">
        {items.length === 0 ? (
          <p className="text-[var(--color-muted)]">هنوز موردی ثبت نشده است.</p>
        ) : (
          <Gallery items={items} />
        )}
      </div>

      {otherCategories.length > 0 && (
        <div className="mt-24">
          <Reveal>
            <span className="eyebrow">سایر دسته‌بندی‌های {client.name}</span>
          </Reveal>
          <div className="flex flex-wrap gap-3 mt-6">
            {otherCategories.map((c) => (
              <Link key={c.id} href={`/clients/${client.slug}/${c.slug}`} data-cursor="hover" className="tag-pill hover:!text-[var(--color-primary)] hover:!border-[var(--color-primary)] transition-colors">
                {c.title}
              </Link>
            ))}
          </div>
        </div>
      )}

      <Reveal className="mt-16">
        <Link href={`/clients/${client.slug}`} data-cursor="hover" className="btn-pill">← بازگشت به {client.name}</Link>
      </Reveal>
    </div>
  );
}
