import type { Metadata } from "next";
import Reveal from "@/components/site/Reveal";
import ContactForm from "@/components/site/ContactForm";
import { getSettings } from "@/lib/queries";

export const dynamic = "force-dynamic";
export const metadata: Metadata = { title: "تماس با ما" };

export default function ContactPage() {
  const settings = getSettings();

  return (
    <div className="container-px py-10">
      <Reveal>
        <span className="eyebrow">تماس با ما</span>
        <h1 className="h-hero font-display mt-6 max-w-4xl">بیا با هم یه چیز خاص بسازیم.</h1>
      </Reveal>

      <div className="grid md:grid-cols-[1fr_1.3fr] gap-16 mt-20">
        <Reveal className="flex flex-col gap-10">
          <div>
            <span className="eyebrow">ایمیل</span>
            <a href={`mailto:${settings.contact_email}`} className="block text-2xl md:text-3xl font-bold mt-3 hover:text-[var(--color-primary)] transition-colors" dir="ltr">
              {settings.contact_email}
            </a>
          </div>
          <div>
            <span className="eyebrow">تلفن</span>
            <p className="text-2xl md:text-3xl font-bold mt-3" dir="ltr">{settings.contact_phone}</p>
          </div>
          <div>
            <span className="eyebrow">آدرس</span>
            <p className="text-lg text-[var(--color-muted)] mt-3 leading-relaxed">{settings.contact_address}</p>
          </div>
          <div className="flex gap-5 font-display uppercase tracking-widest text-sm">
            {settings.social_instagram && <a href={settings.social_instagram} target="_blank" className="hover:text-[var(--color-primary)] transition-colors">Instagram</a>}
            {settings.social_telegram && <a href={settings.social_telegram} target="_blank" className="hover:text-[var(--color-primary)] transition-colors">Telegram</a>}
            {settings.social_whatsapp && <a href={settings.social_whatsapp} target="_blank" className="hover:text-[var(--color-primary)] transition-colors">WhatsApp</a>}
            {settings.social_linkedin && <a href={settings.social_linkedin} target="_blank" className="hover:text-[var(--color-primary)] transition-colors">LinkedIn</a>}
          </div>
          {settings.contact_map_embed && (
            <div
              className="w-full aspect-video rounded-3xl overflow-hidden frame-pop"
              dangerouslySetInnerHTML={{ __html: settings.contact_map_embed }}
            />
          )}
        </Reveal>

        <Reveal delay={100}>
          <div className="rounded-3xl frame-pop p-6 md:p-10">
            <ContactForm />
          </div>
        </Reveal>
      </div>
    </div>
  );
}
