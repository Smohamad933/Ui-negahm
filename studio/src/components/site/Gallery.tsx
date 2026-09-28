"use client";

import { useState } from "react";
import { AnimatePresence, motion } from "framer-motion";
import Reveal from "./Reveal";
import type { AspectRatio, PortfolioItem } from "@/lib/queries";
import { ASPECT_CLASS, ASPECT_GRID_CLASS, normalizeAspectRatio } from "@/lib/aspect";

export default function Gallery({
  items,
  aspectRatio,
}: {
  items: PortfolioItem[];
  aspectRatio?: AspectRatio | string;
}) {
  const [activeIndex, setActiveIndex] = useState<number | null>(null);
  const ratio = normalizeAspectRatio(aspectRatio);

  return (
    <>
      <div className={`grid gap-5 ${ASPECT_GRID_CLASS[ratio]}`}>
        {items.map((item, i) => (
          <Reveal key={item.id} delay={(i % 4) * 60}>
            <button
              onClick={() => setActiveIndex(i)}
              data-cursor="hover"
              className="client-card group block w-full text-right"
            >
              <div className={`relative w-full overflow-hidden ${ASPECT_CLASS[ratio]}`}>
                {item.media_type === "video" ? (
                  <video src={item.media_url} className="absolute inset-0 w-full h-full object-cover" muted loop playsInline autoPlay />
                ) : (
                  // eslint-disable-next-line @next/next/no-img-element
                  <img
                    src={item.media_url}
                    alt={item.title || "نمونه‌کار"}
                    className="absolute inset-0 w-full h-full object-cover transition-transform duration-700 group-hover:scale-[1.05]"
                  />
                )}
                {(item.title || item.description) && (
                  <div className="absolute inset-x-0 bottom-0 p-5 bg-gradient-to-t from-black/80 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-400">
                    {item.title && <p className="text-white font-bold">{item.title}</p>}
                  </div>
                )}
              </div>
            </button>
          </Reveal>
        ))}
      </div>

      <AnimatePresence>
        {activeIndex !== null && (
          <motion.div
            initial={{ opacity: 0 }}
            animate={{ opacity: 1 }}
            exit={{ opacity: 0 }}
            className="fixed inset-0 z-[80] bg-black/92 flex items-center justify-center p-6"
            onClick={() => setActiveIndex(null)}
          >
            <motion.div
              initial={{ scale: 0.92, opacity: 0 }}
              animate={{ scale: 1, opacity: 1 }}
              exit={{ scale: 0.95, opacity: 0 }}
              transition={{ duration: 0.35, ease: [0.16, 1, 0.3, 1] }}
              className="max-w-5xl w-full max-h-[85vh] flex flex-col gap-4"
              onClick={(e) => e.stopPropagation()}
            >
              {items[activeIndex].media_type === "video" ? (
                <video src={items[activeIndex].media_url} className="w-full max-h-[75vh] rounded-2xl object-contain" controls autoPlay />
              ) : (
                // eslint-disable-next-line @next/next/no-img-element
                <img src={items[activeIndex].media_url} alt="" className="w-full max-h-[75vh] rounded-2xl object-contain" />
              )}
              <div className="flex items-center justify-between text-white/70 text-sm">
                <p>{items[activeIndex].title}</p>
                <button onClick={() => setActiveIndex(null)} data-cursor="hover" className="hover:text-white">بستن ✕</button>
              </div>
            </motion.div>
          </motion.div>
        )}
      </AnimatePresence>
    </>
  );
}
