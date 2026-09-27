import { NextResponse } from "next/server";
import { getMessages } from "@/lib/queries";

export async function GET() {
  return NextResponse.json(getMessages());
}
