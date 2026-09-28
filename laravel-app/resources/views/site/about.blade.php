<x-layouts.site title="درباره ما | {{ $settings->site_name }}">

    <section class="container-px pt-6 pb-16">
        <div class="grid md:grid-cols-2 gap-12 items-center">
            <div>
                <span class="eyebrow">درباره ما</span>
                <h1 class="h-hero font-display mt-6">{{ $settings->about_title }}</h1>
                <p class="mt-8 text-base md:text-lg leading-8" style="color: var(--color-muted)">{{ $settings->about_body }}</p>
            </div>
            @if($settings->about_image_url)
                <div class="rounded-[32px] overflow-hidden frame-pop aspect-1-1 reveal-up">
                    <img src="{{ $settings->about_image_url }}" alt="{{ $settings->site_name }}" class="w-full h-full object-cover">
                </div>
            @endif
        </div>
    </section>

    <section class="container-px py-16 reveal-up">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
            <div class="client-card p-8 text-center">
                <span class="h-hero font-display text-pop block" style="font-size:clamp(2.5rem,5vw,4rem)">{{ \Illuminate\Support\Number::format($clientCount) }}</span>
                <span class="tag-pill mt-4 inline-flex">همراه فعال</span>
            </div>
            <div class="client-card p-8 text-center">
                <span class="h-hero font-display text-pop block" style="font-size:clamp(2.5rem,5vw,4rem)">{{ \Illuminate\Support\Number::format($projectCount) }}</span>
                <span class="tag-pill mt-4 inline-flex">پروژه اجرا شده</span>
            </div>
            <div class="client-card p-8 text-center">
                <span class="h-hero font-display text-pop block" style="font-size:clamp(2.5rem,5vw,4rem)">{{ \Illuminate\Support\Number::format($itemCount) }}</span>
                <span class="tag-pill mt-4 inline-flex">قطعه محتوا</span>
            </div>
        </div>
    </section>

    <section class="container-px py-24 reveal-up">
        <div class="rounded-[36px] p-10 md:p-16 text-center frame-pop" style="background: var(--color-secondary); color:#fff;">
            <h2 class="h-section font-display" style="color:#fff">بیایید با هم یک روایت تازه بسازیم.</h2>
            <a href="{{ route('contact') }}" class="btn-pill btn-invert mt-8 inline-flex" style="background:#fff;color:var(--color-fg)">تماس با ما ↗</a>
        </div>
    </section>

</x-layouts.site>
