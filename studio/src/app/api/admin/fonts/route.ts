import { NextResponse } from "next/server";
import { getFonts } from "@/lib/queries";

export async function GET() {
  return NextResponse.json(getFonts());
}
