"use client";

import { useEffect, useRef } from "react";
import { gsap } from "gsap";
import Link from "next/link";

export default function Hero({
  title,
  subtitle,
  ctaText,
  ctaLink,
}: {
  title: string;
  subtitle: string;
  ctaText: string;
  ctaLink: string;
}) {
  const rootRef = useRef<HTMLDivElement>(null);

  useEffect(() => {
    const ctx = gsap.context(() => {
      gsap.fromTo(
        ".hero-line > span",
        { yPercent: 115, rotate: 4 },
        {
          yPercent: 0,
          rotate: 0,
          duration: 1.1,
          ease: "power4.out",
          stagger: 0.09,
          delay: 0.15,
        }
      );
      gsap.fromTo(
        ".hero-fade",
        { opacity: 0, y: 24 },
        { opacity: 1, y: 0, duration: 1, ease: "power3.out", delay: 0.9 }
      );
      gsap.fromTo(
        ".hero-blob",
        { opacity: 0, scale: 0.6 },
        { opacity: 1, scale: 1, duration: 1.2, ease: "back.out(1.6)", stagger: 0.15, delay: 0.1 }
      );
    }, rootRef);
    return () => ctx.revert();
  }, []);

  const lines = title.split("\n");

  return (
    <div ref={rootRef} className="container-px min-h-[92vh] flex flex-col justify-center gap-10 relative overflow-hidden">
      <div
        className="hero-blob blob absolute top-[6%] right-[4%] w-[220px] h-[220px] md:w-[280px] md:h-[280px]"
        style={{ background: "var(--color-accent)", border: "3px solid var(--color-fg)" }}
      />
      <div
        className="hero-blob blob absolute bottom-[8%] left-[2%] w-[160px] h-[160px] md:w-[220px] md:h-[220px]"
        style={{ background: "var(--color-secondary)", border: "3px solid var(--color-fg)", animationDelay: "-3s" }}
      />
      <div
        className="hero-blob blob absolute top-[38%] left-[18%] w-[70px] h-[70px] hidden md:block"
        style={{ background: "var(--color-primary)", border: "3px solid var(--color-fg)", animationDelay: "-5s" }}
      />
      <span className="tag-scatter hidden md:block w-16 h-16 top-[20%] right-[28%]" style={{ background: "var(--color-bg)" }} />

      <span className="eyebrow hero-fade w-fit">استودیو تبلیغاتی · خلاقیت بدون مرز</span>

      <h1 className="h-hero font-display max-w-6xl">
        {lines.map((line, i) => (
          <span key={i} className="reveal-line hero-line">
            <span className={i === 0 ? "text-pop" : ""}>{line}</span>
          </span>
        ))}
      </h1>

      <div className="hero-fade flex flex-wrap items-center gap-6">
        <p className="max-w-md text-[var(--color-muted)] text-lg leading-relaxed font-medium">{subtitle}</p>
        <Link href={ctaLink} data-cursor="hover" className="btn-pill btn-solid">
          {ctaText} ↗
        </Link>
      </div>
    </div>
  );
}
