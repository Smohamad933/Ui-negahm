import { SignJWT, jwtVerify } from "jose";

const SECRET_STRING = process.env.SESSION_SECRET || "negaham-studio-dev-secret-change-me";
const SECRET = new TextEncoder().encode(SECRET_STRING);
export const SESSION_COOKIE = "negaham_admin_session";

export type SessionPayload = {
  uid: number;
  username: string;
};

export async function signSession(payload: SessionPayload) {
  return new SignJWT({ ...payload })
    .setProtectedHeader({ alg: "HS256" })
    .setIssuedAt()
    .setExpirationTime("30d")
    .sign(SECRET);
}

export async function verifySession(token: string): Promise<SessionPayload | null> {
  try {
    const { payload } = await jwtVerify(token, SECRET);
    if (typeof payload.uid === "number" && typeof payload.username === "string") {
      return { uid: payload.uid, username: payload.username };
    }
    return null;
  } catch {
    return null;
  }
}
