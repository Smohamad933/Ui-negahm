import { NextRequest, NextResponse } from "next/server";
import { getSettings, updateSettings } from "@/lib/queries";

const ALLOWED_FIELDS = [
  "site_name",
  "tagline",
  "logo_url",
  "favicon_url",
  "color_bg",
  "color_fg",
  "color_primary",
  "color_secondary",
  "color_accent",
  "color_muted",
  "font_family",
  "hero_title",
  "hero_subtitle",
  "hero_cta_text",
  "hero_cta_link",
  "hero_media_url",
  "about_title",
  "about_body",
  "about_image_url",
  "contact_address",
  "contact_phone",
  "contact_email",
  "contact_map_embed",
  "social_instagram",
  "social_telegram",
  "social_whatsapp",
  "social_linkedin",
  "footer_text",
];

export async function GET() {
  return NextResponse.json(getSettings());
}

export async function PATCH(req: NextRequest) {
  try {
    const body = await req.json();
    const patch: Record<string, unknown> = {};
    for (const key of ALLOWED_FIELDS) {
      if (key in body) patch[key] = body[key];
    }
    updateSettings(patch);
    return NextResponse.json(getSettings());
  } catch (e) {
    console.error(e);
    return NextResponse.json({ error: "خطا در ذخیره تنظیمات" }, { status: 500 });
  }
}
