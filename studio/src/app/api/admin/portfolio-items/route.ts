import { NextRequest, NextResponse } from "next/server";
import { createPortfolioItem } from "@/lib/queries";

export async function POST(req: NextRequest) {
  try {
    const body = await req.json();
    const categoryId = Number(body.category_id);
    const mediaUrl = (body.media_url || "").trim();
    if (!categoryId || !mediaUrl) {
      return NextResponse.json({ error: "رسانه و دسته‌بندی الزامی است" }, { status: 400 });
    }

    const id = createPortfolioItem({
      category_id: categoryId,
      title: body.title,
      description: body.description,
      media_url: mediaUrl,
      media_type: body.media_type || "image",
      featured_home: body.featured_home ? 1 : 0,
    });

    return NextResponse.json({ ok: true, id });
  } catch (e) {
    console.error(e);
    return NextResponse.json({ error: "خطا در افزودن نمونه‌کار" }, { status: 500 });
  }
}
