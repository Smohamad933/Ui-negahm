import { NextRequest, NextResponse } from "next/server";
import { db } from "@/lib/db";
import { getClients, createClient } from "@/lib/queries";
import { uniqueSlug } from "@/lib/slug";

export async function GET() {
  return NextResponse.json(getClients());
}

export async function POST(req: NextRequest) {
  try {
    const body = await req.json();
    const name = (body.name || "").trim();
    if (!name) return NextResponse.json({ error: "نام کارفرما الزامی است" }, { status: 400 });

    const slug = uniqueSlug(body.slug || name, (s) =>
      !!db.prepare("SELECT id FROM clients WHERE slug = ?").get(s)
    );

    const id = createClient({
      slug,
      name,
      logo_url: body.logo_url,
      cover_image_url: body.cover_image_url,
      accent_color: body.accent_color,
      short_description: body.short_description,
      industry: body.industry,
      website_url: body.website_url,
      year: body.year,
      featured: body.featured ? 1 : 0,
      published: body.published === false ? 0 : 1,
    });

    return NextResponse.json({ ok: true, id, slug });
  } catch (e) {
    console.error(e);
    return NextResponse.json({ error: "خطا در ایجاد کارفرما" }, { status: 500 });
  }
}
