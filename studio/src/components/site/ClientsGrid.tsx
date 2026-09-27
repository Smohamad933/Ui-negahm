"use client";

import { useMemo, useState } from "react";
import Link from "next/link";
import { motion, AnimatePresence, LayoutGroup } from "framer-motion";
import type { Client } from "@/lib/queries";

export default function ClientsGrid({ clients }: { clients: Client[] }) {
  const industries = useMemo(() => {
    const set = new Set<string>();
    clients.forEach((c) => c.industry && set.add(c.industry));
    return Array.from(set);
  }, [clients]);

  const [filter, setFilter] = useState<string | null>(null);

  const visible = filter ? clients.filter((c) => c.industry === filter) : clients;

  return (
    <div>
      {industries.length > 1 && (
        <div className="flex flex-wrap gap-3 mb-14">
          <button
            onClick={() => setFilter(null)}
            data-cursor="hover"
            className={`tag-pill transition-colors ${!filter ? "!border-[var(--color-primary)] !text-[var(--color-primary)]" : ""}`}
          >
            همه
          </button>
          {industries.map((ind) => (
            <button
              key={ind}
              onClick={() => setFilter(ind)}
              data-cursor="hover"
              className={`tag-pill transition-colors ${filter === ind ? "!border-[var(--color-primary)] !text-[var(--color-primary)]" : ""}`}
            >
              {ind}
            </button>
          ))}
        </div>
      )}

      <LayoutGroup>
        <motion.div layout className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
          <AnimatePresence mode="popLayout">
            {visible.map((client) => (
              <motion.div
                key={client.id}
                layout
                initial={{ opacity: 0, scale: 0.9, y: 30 }}
                animate={{ opacity: 1, scale: 1, y: 0 }}
                exit={{ opacity: 0, scale: 0.9, y: -20 }}
                transition={{ duration: 0.5, ease: [0.16, 1, 0.3, 1] }}
              >
                <Link href={`/clients/${client.slug}`} data-cursor="hover" className="client-card group block h-full">
                  <div className="relative w-full aspect-[4/5] overflow-hidden">
                    {client.cover_image_url ? (
                      // eslint-disable-next-line @next/next/no-img-element
                      <img
                        src={client.cover_image_url}
                        alt={client.name}
                        className="absolute inset-0 w-full h-full object-cover transition-transform duration-700 ease-[cubic-bezier(.16,1,.3,1)] group-hover:scale-[1.08]"
                      />
                    ) : (
                      <div
                        className="absolute inset-0"
                        style={{
                          background: client.accent_color
                            ? `linear-gradient(150deg, ${client.accent_color}, transparent)`
                            : "linear-gradient(150deg, var(--color-secondary), var(--color-bg))",
                        }}
                      />
                    )}
                    <div className="absolute inset-0 bg-gradient-to-t from-black/85 via-black/10 to-transparent" />
                    <div className="absolute inset-x-0 bottom-0 p-6">
                      {client.industry && (
                        <span className="tag-pill !border-white/30 !text-white/70 mb-3 inline-block">
                          {client.industry}
                        </span>
                      )}
                      <h3 className="text-white text-2xl font-bold">{client.name}</h3>
                      {client.short_description && (
                        <p className="text-white/60 text-sm mt-1 line-clamp-2">{client.short_description}</p>
                      )}
                    </div>
                    <span className="absolute top-6 left-6 h-10 w-10 rounded-full border border-white/30 flex items-center justify-center text-white opacity-0 group-hover:opacity-100 -translate-y-2 group-hover:translate-y-0 transition-all duration-400">
                      ↗
                    </span>
                  </div>
                </Link>
              </motion.div>
            ))}
          </AnimatePresence>
        </motion.div>
      </LayoutGroup>

      {visible.length === 0 && (
        <p className="text-center text-[var(--color-muted)] py-20">کارفرمایی برای نمایش وجود ندارد.</p>
      )}
    </div>
  );
}
