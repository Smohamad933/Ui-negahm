import { NextRequest, NextResponse } from "next/server";
import { db } from "@/lib/db";
import { updateCategory, deleteCategory, getCategoryById } from "@/lib/queries";
import { uniqueSlug } from "@/lib/slug";

export async function PATCH(
  req: NextRequest,
  { params }: { params: Promise<{ id: string }> }
) {
  const { id } = await params;
  const categoryId = Number(id);
  const existing = getCategoryById(categoryId);
  if (!existing) return NextResponse.json({ error: "یافت نشد" }, { status: 404 });

  try {
    const body = await req.json();
    const patch: Record<string, unknown> = {};
    for (const f of ["title", "description", "cover_image_url"]) {
      if (f in body) patch[f] = body[f];
    }
    if ("aspect_ratio" in body && ["16:9", "9:16", "1:1"].includes(body.aspect_ratio)) {
      patch.aspect_ratio = body.aspect_ratio;
    }
    if (body.slug && body.slug !== existing.slug) {
      patch.slug = uniqueSlug(body.slug, (s) =>
        !!db
          .prepare("SELECT id FROM categories WHERE client_id = ? AND slug = ? AND id != ?")
          .get(existing.client_id, s, categoryId)
      );
    }
    updateCategory(categoryId, patch);
    return NextResponse.json({ ok: true });
  } catch (e) {
    console.error(e);
    return NextResponse.json({ error: "خطا در ذخیره تغییرات" }, { status: 500 });
  }
}

export async function DELETE(
  _req: NextRequest,
  { params }: { params: Promise<{ id: string }> }
) {
  const { id } = await params;
  deleteCategory(Number(id));
  return NextResponse.json({ ok: true });
}
