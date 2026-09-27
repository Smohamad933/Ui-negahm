// Generates URL-friendly slugs while preserving Persian characters (nicer, readable URLs
// for a Persian-language site). Falls back to a random id if nothing usable remains.

export function baseSlug(input: string): string {
  const slug = input
    .trim()
    .toLowerCase()
    .replace(/[\u200c\s]+/g, "-") // spaces & ZWNJ -> dash
    .replace(/[^a-z0-9\u0600-\u06FF-]+/g, "") // strip anything not latin/digit/persian/dash
    .replace(/-+/g, "-")
    .replace(/^-+|-+$/g, "");

  return slug || `item-${Date.now().toString(36)}`;
}

export function uniqueSlug(input: string, exists: (slug: string) => boolean): string {
  const base = baseSlug(input);
  let candidate = base;
  let i = 2;
  while (exists(candidate)) {
    candidate = `${base}-${i}`;
    i += 1;
  }
  return candidate;
}
