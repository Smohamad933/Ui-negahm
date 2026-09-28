import type { Metadata } from "next";
import Reveal from "@/components/site/Reveal";
import ClientsGrid from "@/components/site/ClientsGrid";
import { getClients } from "@/lib/queries";

export const dynamic = "force-dynamic";
export const metadata: Metadata = { title: "همراهان" };

export default function ClientsPage() {
  const clients = getClients({ onlyPublished: true });

  return (
    <div className="container-px py-10">
      <Reveal>
        <span className="eyebrow">همراهان</span>
        <h1 className="h-hero font-display mt-6 max-w-4xl">برندهایی که باهاشون همکاری کردیم.</h1>
      </Reveal>

      <div className="mt-20">
        <ClientsGrid clients={clients} />
      </div>
    </div>
  );
}
