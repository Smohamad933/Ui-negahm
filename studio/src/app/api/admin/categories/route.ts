import { NextRequest, NextResponse } from "next/server";
import { db } from "@/lib/db";
import { createCategory } from "@/lib/queries";
import { uniqueSlug } from "@/lib/slug";

export async function POST(req: NextRequest) {
  try {
    const body = await req.json();
    const clientId = Number(body.client_id);
    const title = (body.title || "").trim();
    if (!clientId || !title) {
      return NextResponse.json({ error: "عنوان و کارفرما الزامی است" }, { status: 400 });
    }

    const slug = uniqueSlug(body.slug || title, (s) =>
      !!db
        .prepare("SELECT id FROM categories WHERE client_id = ? AND slug = ?")
        .get(clientId, s)
    );

    const allowedRatios = ["16:9", "9:16", "1:1"];
    const aspectRatio = allowedRatios.includes(body.aspect_ratio)
      ? body.aspect_ratio
      : "16:9";

    const id = createCategory({
      client_id: clientId,
      title,
      slug,
      description: body.description,
      cover_image_url: body.cover_image_url,
      aspect_ratio: aspectRatio,
    });

    return NextResponse.json({ ok: true, id, slug });
  } catch (e) {
    console.error(e);
    return NextResponse.json({ error: "خطا در ایجاد دسته‌بندی" }, { status: 500 });
  }
}
