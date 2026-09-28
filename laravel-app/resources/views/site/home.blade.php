<x-layouts.site :title="$settings->site_name . ' | ' . $settings->tagline">

    {{-- HERO --}}
    <section class="relative container-px pt-6 pb-24 overflow-hidden">
        <div class="blob hero-blob" style="width:420px;height:420px;top:-120px;left:-140px;background:var(--color-accent);opacity:.55"></div>
        <div class="blob hero-blob" style="width:280px;height:280px;bottom:-80px;right:-60px;background:var(--color-secondary);opacity:.3;animation-delay:.4s"></div>

        <span class="eyebrow reveal-up is-visible">{{ $settings->tagline }}</span>

        <h1 class="h-hero font-display mt-6 max-w-5xl">
            @foreach(explode('؛', $settings->hero_title) as $i => $line)
                <span class="reveal-line hero-line"><span>{{ trim($line) }}@if(!$loop->last)؛@endif</span></span>
            @endforeach
        </h1>

        <div class="mt-10 flex flex-col md:flex-row md:items-end justify-between gap-8">
            <p class="hero-fade max-w-xl text-base md:text-lg" style="color: var(--color-muted)">
                {{ $settings->hero_subtitle }}
            </p>
            <a href="{{ $settings->hero_cta_link ?: route('clients.index') }}" class="hero-fade btn-pill btn-solid shrink-0">
                {{ $settings->hero_cta_text ?: 'دیدن نمونه‌کارها' }} ↗
            </a>
        </div>
    </section>

    {{-- CLIENT LOGO MARQUEE --}}
    @if($clients->isNotEmpty())
        <section class="marquee-band py-6 mb-24">
            <div class="marquee-row">
                <div class="marquee-track">
                    @for($r = 0; $r < 2; $r++)
                        @foreach($clients as $c)
                            <span class="font-display font-extrabold text-2xl md:text-4xl px-8 whitespace-nowrap opacity-90">{{ $c->name }} <span class="opacity-40">✦</span></span>
                        @endforeach
                    @endfor
                </div>
            </div>
        </section>
    @endif

    {{-- FEATURED WORK (masonry, mixed ratios -> no crop / no gap) --}}
    @if($featured->isNotEmpty())
        <section class="container-px py-10 reveal-up">
            <div class="flex items-end justify-between gap-6 mb-10 flex-wrap">
                <div>
                    <span class="eyebrow">نمونه‌کارهای منتخب</span>
                    <h2 class="h-section font-display mt-4">چند نگاه از کارهایی که ساختیم</h2>
                </div>
                <a href="{{ route('clients.index') }}" class="btn-pill">همه همراهان ↗</a>
            </div>

            <div class="masonry masonry-3">
                @foreach($featured as $item)
                    <a href="{{ route('clients.category', ['client' => $item->client_slug, 'category' => $item->category_slug]) }}"
                       class="client-card block reveal-up" style="animation-delay: {{ $loop->index * 0.06 }}s">
                        @if($item->media_type === 'video')
                            <video src="{{ $item->media_url }}" class="w-full h-auto block" muted loop playsinline autoplay></video>
                        @else
                            <img src="{{ $item->media_url }}" alt="{{ $item->title ?: $item->client_name }}" class="w-full h-auto block" loading="lazy">
                        @endif
                        <div class="p-4 flex items-center justify-between gap-3">
                            <span class="font-display font-bold text-sm">{{ $item->client_name }}</span>
                            <span class="tag-pill">{{ $item->category_title }}</span>
                        </div>
                    </a>
                @endforeach
            </div>
        </section>
    @endif

    {{-- FEATURED CLIENTS --}}
    @if($featuredClients->isNotEmpty())
        <section class="container-px py-16 reveal-up">
            <span class="eyebrow">همراهان ویژه</span>
            <h2 class="h-section font-display mt-4 mb-10">برندهایی که با هم بزرگ شدیم</h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($featuredClients as $client)
                    <a href="{{ route('clients.show', $client) }}" class="client-card p-6 flex flex-col gap-4 reveal-up" style="animation-delay: {{ $loop->index * 0.07 }}s">
                        @if($client->cover_image_url)
                            <div class="rounded-2xl overflow-hidden frame-pop aspect-16-9">
                                <img src="{{ $client->cover_image_url }}" alt="{{ $client->name }}" class="w-full h-full object-cover">
                            </div>
                        @endif
                        <div class="flex items-center justify-between gap-3">
                            <span class="font-display font-extrabold text-lg">{{ $client->name }}</span>
                            @if($client->industry)<span class="tag-pill">{{ $client->industry }}</span>@endif
                        </div>
                        @if($client->short_description)
                            <p class="text-sm" style="color: var(--color-muted)">{{ \Illuminate\Support\Str::limit($client->short_description, 100) }}</p>
                        @endif
                    </a>
                @endforeach
            </div>
        </section>
    @endif

    {{-- CTA --}}
    <section class="container-px py-24 reveal-up">
        <div class="rounded-[36px] p-10 md:p-16 text-center frame-pop" style="background: var(--color-primary); color:#fff;">
            <h2 class="h-section font-display" style="color:#fff">{{ $settings->about_title }}</h2>
            <a href="{{ route('contact') }}" class="btn-pill btn-invert mt-8 inline-flex" style="background:#fff;color:var(--color-fg)">شروع گفت‌وگو ↗</a>
        </div>
    </section>

</x-layouts.site>
