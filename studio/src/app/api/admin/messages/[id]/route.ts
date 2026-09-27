import { NextRequest, NextResponse } from "next/server";
import { markMessageRead, deleteMessage } from "@/lib/queries";

export async function PATCH(
  req: NextRequest,
  { params }: { params: Promise<{ id: string }> }
) {
  const { id } = await params;
  const body = await req.json();
  markMessageRead(Number(id), !!body.is_read);
  return NextResponse.json({ ok: true });
}

export async function DELETE(
  _req: NextRequest,
  { params }: { params: Promise<{ id: string }> }
) {
  const { id } = await params;
  deleteMessage(Number(id));
  return NextResponse.json({ ok: true });
}
