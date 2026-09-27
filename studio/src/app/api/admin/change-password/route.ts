import { NextRequest, NextResponse } from "next/server";
import { db } from "@/lib/db";
import { getSession, verifyPassword, hashPassword } from "@/lib/auth";

export async function POST(req: NextRequest) {
  const session = await getSession();
  if (!session) return NextResponse.json({ error: "احراز هویت نشده" }, { status: 401 });

  try {
    const { currentPassword, newPassword } = await req.json();
    if (!newPassword || String(newPassword).length < 6) {
      return NextResponse.json({ error: "رمز جدید باید حداقل ۶ کاراکتر باشد" }, { status: 400 });
    }

    const user = db
      .prepare("SELECT * FROM admin_users WHERE id = ?")
      .get(session.uid) as { id: number; password_hash: string } | undefined;

    if (!user || !verifyPassword(currentPassword || "", user.password_hash)) {
      return NextResponse.json({ error: "رمز فعلی اشتباه است" }, { status: 400 });
    }

    db.prepare("UPDATE admin_users SET password_hash = ? WHERE id = ?").run(
      hashPassword(newPassword),
      user.id
    );

    return NextResponse.json({ ok: true });
  } catch {
    return NextResponse.json({ error: "خطای سرور" }, { status: 500 });
  }
}
