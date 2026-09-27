import { NextRequest, NextResponse } from "next/server";
import { db } from "@/lib/db";
import {
  getClientById,
  updateClient,
  deleteClient,
  getCategoriesByClient,
  getItemsByCategory,
} from "@/lib/queries";
import { uniqueSlug } from "@/lib/slug";

export async function GET(
  _req: NextRequest,
  { params }: { params: Promise<{ id: string }> }
) {
  const { id } = await params;
  const client = getClientById(Number(id));
  if (!client) return NextResponse.json({ error: "یافت نشد" }, { status: 404 });

  const categories = getCategoriesByClient(client.id).map((cat) => ({
    ...cat,
    items: getItemsByCategory(cat.id),
  }));

  return NextResponse.json({ ...client, categories });
}

export async function PATCH(
  req: NextRequest,
  { params }: { params: Promise<{ id: string }> }
) {
  const { id } = await params;
  const clientId = Number(id);
  const existing = getClientById(clientId);
  if (!existing) return NextResponse.json({ error: "یافت نشد" }, { status: 404 });

  try {
    const body = await req.json();
    const patch: Record<string, unknown> = {};

    const fields = [
      "name",
      "logo_url",
      "cover_image_url",
      "accent_color",
      "short_description",
      "industry",
      "website_url",
      "year",
    ];
    for (const f of fields) {
      if (f in body) patch[f] = body[f];
    }
    if ("featured" in body) patch.featured = body.featured ? 1 : 0;
    if ("published" in body) patch.published = body.published ? 1 : 0;

    if (body.slug && body.slug !== existing.slug) {
      patch.slug = uniqueSlug(body.slug, (s) =>
        s !== existing.slug &&
        !!db.prepare("SELECT id FROM clients WHERE slug = ?").get(s)
      );
    }

    updateClient(clientId, patch);
    return NextResponse.json(getClientById(clientId));
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
  deleteClient(Number(id));
  return NextResponse.json({ ok: true });
}
