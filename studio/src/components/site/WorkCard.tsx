import Link from "next/link";
import type { AspectRatio } from "@/lib/queries";
import { ASPECT_CLASS, normalizeAspectRatio } from "@/lib/aspect";

export default function WorkCard({
  href,
  title,
  clientName,
  categoryTitle,
  mediaUrl,
  mediaType,
  aspectRatio,
}: {
  href: string;
  title: string;
  clientName: string;
  categoryTitle: string;
  mediaUrl: string;
  mediaType: string;
  aspectRatio?: AspectRatio | string;
}) {
  const ratio = normalizeAspectRatio(aspectRatio);
  return (
    <Link href={href} data-cursor="hover" className="client-card group block w-full">
      <div className={`relative w-full overflow-hidden ${ASPECT_CLASS[ratio]}`}>
        {mediaType === "video" ? (
          <video
            src={mediaUrl}
            className="absolute inset-0 w-full h-full object-cover transition-transform duration-700 ease-[cubic-bezier(.16,1,.3,1)] group-hover:scale-[1.06]"
            muted
            loop
            playsInline
            autoPlay
          />
        ) : (
          // eslint-disable-next-line @next/next/no-img-element
          <img
            src={mediaUrl}
            alt={title}
            className="absolute inset-0 w-full h-full object-cover transition-transform duration-700 ease-[cubic-bezier(.16,1,.3,1)] group-hover:scale-[1.06]"
          />
        )}
        <div className="absolute inset-0 bg-gradient-to-t from-black/80 via-black/10 to-transparent opacity-70 group-hover:opacity-90 transition-opacity duration-500" />
        <div className="absolute inset-x-0 bottom-0 p-6 flex items-end justify-between gap-3">
          <div>
            <p className="font-display text-xs uppercase tracking-[0.2em] text-white/60 mb-2">
              {clientName} · {categoryTitle}
            </p>
            <h3 className="text-white text-xl md:text-2xl font-bold">{title}</h3>
          </div>
          <span
            className="shrink-0 h-11 w-11 rounded-full border-[2.5px] flex items-center justify-center font-bold transition-transform duration-500 group-hover:rotate-45"
            style={{ background: "var(--color-accent)", borderColor: "var(--color-fg)", color: "var(--color-fg)" }}
          >
            ↗
          </span>
        </div>
      </div>
    </Link>
  );
}
