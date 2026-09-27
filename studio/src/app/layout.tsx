import type { Metadata } from "next";
import "./globals.css";
import { getSettings } from "@/lib/queries";
import { buildThemeCss } from "@/lib/theme";
import { ensureDefaultAdmin } from "@/lib/auth";

ensureDefaultAdmin();

export const dynamic = "force-dynamic";

export async function generateMetadata(): Promise<Metadata> {
  const settings = getSettings();
  return {
    title: `${settings.site_name} | ${settings.tagline}`,
    description: settings.tagline,
    icons: settings.favicon_url ? [{ url: settings.favicon_url }] : undefined,
  };
}

export default function RootLayout({
  children,
}: Readonly<{
  children: React.ReactNode;
}>) {
  const settings = getSettings();
  const themeCss = buildThemeCss(settings);

  return (
    <html lang="fa" dir="rtl">
      <head>
        <style dangerouslySetInnerHTML={{ __html: themeCss }} />
      </head>
      <body>{children}</body>
    </html>
  );
}
