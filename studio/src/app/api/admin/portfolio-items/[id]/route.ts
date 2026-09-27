import { NextRequest, NextResponse } from "next/server";
import { updatePortfolioItem, deletePortfolioItem } from "@/lib/queries";

export async function PATCH(
  req: NextRequest,
  { params }: { params: Promise<{ id: string }> }
) {
  const { id } = await params;
  try {
    const body = await req.json();
    const patch: Record<string, unknown> = {};
    for (const f of ["title", "description", "media_url", "media_type"]) {
      if (f in body) patch[f] = body[f];
    }
    if ("featured_home" in body) patch.featured_home = body.featured_home ? 1 : 0;
    if ("order_index" in body) patch.order_index = Number(body.order_index);
    updatePortfolioItem(Number(id), patch);
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
  deletePortfolioItem(Number(id));
  return NextResponse.json({ ok: true });
}
