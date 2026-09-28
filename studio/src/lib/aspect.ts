import type { AspectRatio } from "./queries";

/** Tailwind arbitrary-value aspect-ratio class for each fixed category shape. */
export const ASPECT_CLASS: Record<AspectRatio, string> = {
  "16:9": "aspect-[16/9]",
  "9:16": "aspect-[9/16]",
  "1:1": "aspect-square",
};

/** Grid column classes tuned per shape for a gallery of same-ratio tiles. */
export const ASPECT_GRID_CLASS: Record<AspectRatio, string> = {
  "16:9": "grid-cols-1 sm:grid-cols-2",
  "9:16": "grid-cols-2 sm:grid-cols-3 lg:grid-cols-4",
  "1:1": "grid-cols-2 sm:grid-cols-3",
};

export function normalizeAspectRatio(value: string | null | undefined): AspectRatio {
  return value === "9:16" || value === "1:1" ? value : "16:9";
}
