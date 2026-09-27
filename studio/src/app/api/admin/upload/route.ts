import { NextRequest, NextResponse } from "next/server";
import { saveUploadedFile, ALLOWED_IMAGE_TYPES, ALLOWED_VIDEO_TYPES, ALLOWED_FONT_EXTENSIONS } from "@/lib/uploads";
import { addFont } from "@/lib/queries";

export const runtime = "nodejs";

export async function POST(req: NextRequest) {
  try {
    const form = await req.formData();
    const file = form.get("file");
    const kind = (form.get("kind") as string) || "image"; // image | video | font

    if (!(file instanceof File)) {
      return NextResponse.json({ error: "فایلی ارسال نشده است" }, { status: 400 });
    }

    if (kind === "font") {
      const ext = (file.name.split(".").pop() || "").toLowerCase();
      if (!ALLOWED_FONT_EXTENSIONS.includes(ext)) {
        return NextResponse.json({ error: "فرمت فونت پشتیبانی نمی‌شود (woff2, woff, ttf, otf)" }, { status: 400 });
      }
      const familyName = (form.get("familyName") as string) || "فونت سفارشی";
      const weight = (form.get("weight") as string) || "400";
      const style = (form.get("style") as string) || "normal";

      const { url } = await saveUploadedFile(file, "fonts");
      addFont({ family_name: familyName, weight, style, format: ext, file_url: url });

      return NextResponse.json({ ok: true, url, familyName });
    }

    if (kind === "video") {
      if (!ALLOWED_VIDEO_TYPES.includes(file.type)) {
        return NextResponse.json({ error: "فرمت ویدیو پشتیبانی نمی‌شود" }, { status: 400 });
      }
      const { url } = await saveUploadedFile(file, "images");
      return NextResponse.json({ ok: true, url });
    }

    if (!ALLOWED_IMAGE_TYPES.includes(file.type)) {
      return NextResponse.json({ error: "فرمت تصویر پشتیبانی نمی‌شود" }, { status: 400 });
    }
    if (file.size > 12 * 1024 * 1024) {
      return NextResponse.json({ error: "حجم فایل نباید بیشتر از ۱۲ مگابایت باشد" }, { status: 400 });
    }

    const { url } = await saveUploadedFile(file, "images");
    return NextResponse.json({ ok: true, url });
  } catch (e) {
    console.error(e);
    return NextResponse.json({ error: "خطا در آپلود فایل" }, { status: 500 });
  }
}
