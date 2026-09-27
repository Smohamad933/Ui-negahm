import fs from "fs";
import path from "path";
import crypto from "crypto";

// Stored outside /public on purpose: Next.js bakes the public/ folder into the
// production build, so files written there at runtime (e.g. admin uploads)
// would 404 until the next rebuild. Instead we keep uploads in a writable
// data directory and serve them through a dynamic route handler (see
// src/app/uploads/[...path]/route.ts) so new files are available immediately.
const uploadsRoot = path.join(process.cwd(), "data", "uploads");

function ensureDir(dir: string) {
  if (!fs.existsSync(dir)) fs.mkdirSync(dir, { recursive: true });
}

export async function saveUploadedFile(
  file: File,
  subfolder: "images" | "fonts"
): Promise<{ url: string; ext: string }> {
  const dir = path.join(uploadsRoot, subfolder);
  ensureDir(dir);

  const originalName = file.name || "file";
  const ext = (originalName.split(".").pop() || "bin").toLowerCase();
  const safeExt = ext.replace(/[^a-z0-9]/g, "");
  const uniqueName = `${Date.now()}-${crypto
    .randomBytes(6)
    .toString("hex")}.${safeExt}`;

  const arrayBuffer = await file.arrayBuffer();
  const buffer = Buffer.from(arrayBuffer);
  fs.writeFileSync(path.join(dir, uniqueName), buffer);

  return { url: `/uploads/${subfolder}/${uniqueName}`, ext: safeExt };
}

// Best-effort removal of a previously uploaded file, given the public URL
// that was returned by saveUploadedFile (e.g. "/uploads/fonts/xyz.woff2").
// Silently does nothing for URLs that don't point into our uploads dir
// (e.g. seed/demo images under /seed-demo/, which must never be deleted).
export function deleteUploadedFile(url: string | null | undefined) {
  if (!url || !url.startsWith("/uploads/")) return;
  const relative = url.slice("/uploads/".length);
  if (relative.includes("..")) return;
  const filePath = path.join(uploadsRoot, relative);
  if (!filePath.startsWith(uploadsRoot)) return;
  try {
    if (fs.existsSync(filePath)) fs.unlinkSync(filePath);
  } catch {
    // ignore - non-fatal cleanup
  }
}

export const ALLOWED_IMAGE_TYPES = [
  "image/png",
  "image/jpeg",
  "image/webp",
  "image/gif",
  "image/svg+xml",
];

export const ALLOWED_VIDEO_TYPES = ["video/mp4", "video/webm"];

export const ALLOWED_FONT_EXTENSIONS = ["woff2", "woff", "ttf", "otf"];

export function fontFormatFromExt(ext: string) {
  switch (ext) {
    case "woff2":
      return "woff2";
    case "woff":
      return "woff";
    case "ttf":
      return "truetype";
    case "otf":
      return "opentype";
    default:
      return ext;
  }
}
