import Link from "next/link";
import Reveal from "./Reveal";
import type { Client } from "@/lib/queries";

/**
 * Home-page "همراهان" showcase: a fixed 4x2 grid (8 cells).
 * Row 1 = 4 brand tiles, Row 2 = 3 brand tiles + a "دیدن همه همراهان" CTA
 * in the 8th slot, with a dashed divider line between the two rows.
 */
export default function BrandGrid({ clients }: { clients: Client[] }) {
  const brands = clients.slice(0, 7);
  if (brands.length === 0) return null;

  const rowOne = brands.slice(0, 4);
  const rowTwo = brands.slice(4, 7);

  return (
    <section className="container-px py-24">
      <Reveal>
        <span className="eyebrow">همراهان نگاه مدیا</span>
        <h2 className="h-section mt-5 max-w-3xl">برندهایی که داستان‌شون رو با هم ساختیم.</h2>
      </Reveal>

      <div className="mt-14 grid grid-cols-2 md:grid-cols-4 gap-5">
        {rowOne.map((c, i) => (
          <BrandTile key={c.id} client={c} delay={i * 50} />
        ))}
      </div>

      <div className="my-8 border-t-2 border-dashed" style={{ borderColor: "color-mix(in srgb, var(--color-fg) 30%, transparent)" }} />

      <div className="grid grid-cols-2 md:grid-cols-4 gap-5">
        {rowTwo.map((c, i) => (
          <BrandTile key={c.id} client={c} delay={i * 50} />
        ))}
        <Reveal delay={rowTwo.length * 50}>
          <Link
            href="/clients"
            data-cursor="hover"
            className="group relative flex aspect-square w-full flex-col items-center justify-center gap-3 rounded-[28px] text-center transition-transform duration-500 hover:-translate-y-1"
            style={{ background: "var(--color-primary)", color: "#fff" }}
          >
            <span
              className="flex h-12 w-12 items-center justify-center rounded-full border-[2.5px] text-xl font-bold transition-transform duration-500 group-hover:rotate-45"
              style={{ borderColor: "#fff" }}
            >
              ↗
            </span>
            <span className="px-4 text-base font-bold sm:text-lg">دیدن همه همراهان</span>
          </Link>
        </Reveal>
      </div>
    </section>
  );
}

function BrandTile({ client, delay }: { client: Client; delay: number }) {
  return (
    <Reveal delay={delay}>
      <Link
        href={`/clients/${client.slug}`}
        data-cursor="hover"
        className="client-card group relative block aspect-square w-full overflow-hidden"
      >
        {client.cover_image_url ? (
          // eslint-disable-next-line @next/next/no-img-element
          <img
            src={client.cover_image_url}
            alt={client.name}
            className="absolute inset-0 h-full w-full object-cover transition-transform duration-700 group-hover:scale-[1.08]"
          />
        ) : (
          <div
            className="absolute inset-0"
            style={{
              background: `linear-gradient(150deg, ${client.accent_color || "var(--color-secondary)"}, var(--color-accent))`,
            }}
          />
        )}
        <div className="absolute inset-0 bg-gradient-to-t from-black/75 via-black/5 to-transparent" />
        <div className="absolute inset-x-0 bottom-0 p-4 sm:p-5">
          <p className="text-white font-bold leading-tight">{client.name}</p>
          {client.industry && <p className="text-white/60 text-xs mt-1">{client.industry}</p>}
        </div>
      </Link>
    </Reveal>
  );
}
