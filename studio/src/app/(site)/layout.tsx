import { getSettings } from "@/lib/queries";
import Header from "@/components/site/Header";
import Footer from "@/components/site/Footer";
import GrainOverlay from "@/components/site/GrainOverlay";
import SmoothScroll from "@/components/site/SmoothScroll";
import CustomCursor from "@/components/site/CustomCursor";
import PageTransition from "@/components/site/PageTransition";

export const dynamic = "force-dynamic";

export default function SiteLayout({ children }: { children: React.ReactNode }) {
  const settings = getSettings();

  return (
    <SmoothScroll>
      <GrainOverlay />
      <CustomCursor />
      <Header settings={settings} />
      <PageTransition>
        <main className="pt-24">{children}</main>
        <Footer settings={settings} />
      </PageTransition>
    </SmoothScroll>
  );
}
