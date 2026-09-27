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
    }, rootRef);
    return () => ctx.revert();
  }, []);

  const lines = title.split("\n");

  return (
    <div ref={rootRef} className="container-px min-h-[92vh] flex flex-col justify-center gap-10 relative">
      <div className="absolute -z-10 top-[-10%] right-[-10%] w-[60vw] h-[60vw] max-w-[720px] max-h-[720px] rounded-full opacity-20 blur-[120px]" style={{ background: "radial-gradient(circle, var(--color-primary), transparent 70%)" }} />
      <div className="absolute -z-10 bottom-[-15%] left-[-10%] w-[50vw] h-[50vw] max-w-[600px] max-h-[600px] rounded-full opacity-20 blur-[120px]" style={{ background: "radial-gradient(circle, var(--color-secondary), transparent 70%)" }} />

      <span className="eyebrow hero-fade">استودیو تبلیغاتی · خلاقیت بدون مرز</span>

      <h1 className="h-hero font-display max-w-6xl">
        {lines.map((line, i) => (
          <span key={i} className="reveal-line hero-line">
            <span>{line}</span>
          </span>
        ))}
      </h1>

      <div className="hero-fade flex flex-wrap items-center gap-6">
        <p className="max-w-md text-[var(--color-muted)] text-lg leading-relaxed">{subtitle}</p>
        <Link href={ctaLink} data-cursor="hover" className="btn-pill btn-solid">
          {ctaText} ↗
        </Link>
      </div>
    </div>
  );
}
