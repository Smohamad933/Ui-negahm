import bcrypt from "bcryptjs";
import { cookies } from "next/headers";
import { db } from "./db";
import { verifySession, SESSION_COOKIE, type SessionPayload } from "./auth-edge";

export { signSession, verifySession, SESSION_COOKIE } from "./auth-edge";
export type { SessionPayload } from "./auth-edge";

export async function getSession(): Promise<SessionPayload | null> {
  const store = await cookies();
  const token = store.get(SESSION_COOKIE)?.value;
  if (!token) return null;
  return verifySession(token);
}

export function hashPassword(password: string) {
  return bcrypt.hashSync(password, 10);
}

export function verifyPassword(password: string, hash: string) {
  return bcrypt.compareSync(password, hash);
}

export function findAdminByUsername(username: string) {
  return db
    .prepare("SELECT * FROM admin_users WHERE username = ?")
    .get(username) as
    | { id: number; username: string; password_hash: string; name: string }
    | undefined;
}

export function ensureDefaultAdmin() {
  const count = (
    db.prepare("SELECT COUNT(*) as c FROM admin_users").get() as { c: number }
  ).c;
  if (count === 0) {
    const defaultUser = process.env.ADMIN_USERNAME || "admin";
    const defaultPass = process.env.ADMIN_PASSWORD || "Negaham@2026";
    db.prepare(
      "INSERT INTO admin_users (username, password_hash, name) VALUES (?, ?, ?)"
    ).run(defaultUser, hashPassword(defaultPass), "مدیر سایت");
    console.log(
      `[negaham-studio] Default admin created -> username: ${defaultUser} / password: ${defaultPass}`
    );
  }
}
