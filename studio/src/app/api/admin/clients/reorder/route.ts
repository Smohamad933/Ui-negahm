import { NextRequest, NextResponse } from "next/server";
import { reorderClients } from "@/lib/queries";

export async function POST(req: NextRequest) {
  try {
    const { orderedIds } = await req.json();
    if (!Array.isArray(orderedIds)) {
      return NextResponse.json({ error: "ورودی نامعتبر" }, { status: 400 });
    }
    reorderClients(orderedIds.map(Number));
    return NextResponse.json({ ok: true });
  } catch (e) {
    console.error(e);
    return NextResponse.json({ error: "خطا در جابه‌جایی" }, { status: 500 });
  }
}
