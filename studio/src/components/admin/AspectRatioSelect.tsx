"use client";

import type { AspectRatio } from "@/lib/queries";

export const ASPECT_RATIO_OPTIONS: { value: AspectRatio; label: string }[] = [
  { value: "16:9", label: "۱۶:۹ — افقی (کمپین، وب‌سایت)" },
  { value: "9:16", label: "۹:۱۶ — عمودی (ریلز، استوری)" },
  { value: "1:1", label: "۱:۱ — مربع (پست، محصول)" },
];

export default function AspectRatioSelect({
  value,
  onChange,
  className = "admin-input",
}: {
  value: AspectRatio;
  onChange: (v: AspectRatio) => void;
  className?: string;
}) {
  return (
    <select
      className={className}
      value={value}
      onChange={(e) => onChange(e.target.value as AspectRatio)}
    >
      {ASPECT_RATIO_OPTIONS.map((opt) => (
        <option key={opt.value} value={opt.value}>
          {opt.label}
        </option>
      ))}
    </select>
  );
}
