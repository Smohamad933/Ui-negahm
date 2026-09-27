import type { ReactNode } from "react";

export default function Marquee({ children }: { children: ReactNode[] }) {
  return (
    <div className="marquee-row overflow-hidden w-full">
      <div className="marquee-track">
        {children}
        {children}
      </div>
    </div>
  );
}
