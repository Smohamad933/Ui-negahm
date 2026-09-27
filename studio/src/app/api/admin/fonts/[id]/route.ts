import { NextRequest, NextResponse } from "next/server";
import { deleteFont } from "@/lib/queries";

export async function DELETE(
  _req: NextRequest,
  { params }: { params: Promise<{ id: string }> }
) {
  const { id } = await params;
  deleteFont(Number(id));
  return NextResponse.json({ ok: true });
}
