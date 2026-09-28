<footer class="relative container-px pt-24 pb-10 mt-20" style="background: var(--color-fg); color: var(--color-bg)">
    <div class="flex flex-col gap-14">
        <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-8">
            <div>
                <span class="eyebrow eyebrow-invert">همکاری با ما</span>
                <h3 class="h-section mt-4 max-w-xl" style="color: var(--color-bg)">ایده‌ی بعدی برندت رو با هم بسازیم.</h3>
            </div>
            <a href="{{ route('contact') }}" class="btn-pill btn-solid btn-invert whitespace-nowrap">شروع پروژه ↗</a>
        </div>

        <div style="border-top: 3px dashed color-mix(in srgb, var(--color-bg) 35%, transparent)"></div>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-sm">
            <div class="flex flex-col gap-3">
                <span class="opacity-60 font-display uppercase tracking-widest text-xs font-bold">صفحات</span>
                <a href="{{ route('home') }}" class="hover:opacity-80">خانه</a>
                <a href="{{ route('clients.index') }}" class="hover:opacity-80">همراهان</a>
                <a href="{{ route('about') }}" class="hover:opacity-80">درباره ما</a>
                <a href="{{ route('contact') }}" class="hover:opacity-80">تماس با ما</a>
            </div>
            <div class="flex flex-col gap-3">
                <span class="opacity-60 font-display uppercase tracking-widest text-xs font-bold">تماس</span>
                <a href="mailto:{{ $settings->contact_email }}" class="hover:opacity-80">{{ $settings->contact_email }}</a>
                <span dir="ltr" class="text-right md:text-left">{{ $settings->contact_phone }}</span>
                <span class="opacity-80">{{ $settings->contact_address }}</span>
            </div>
            <div class="flex flex-col gap-3">
                <span class="opacity-60 font-display uppercase tracking-widest text-xs font-bold">شبکه‌های اجتماعی</span>
                @if($settings->social_instagram)<a href="{{ $settings->social_instagram }}" target="_blank" rel="noopener" class="hover:opacity-80">اینستاگرام</a>@endif
                @if($settings->social_telegram)<a href="{{ $settings->social_telegram }}" target="_blank" rel="noopener" class="hover:opacity-80">تلگرام</a>@endif
                @if($settings->social_whatsapp)<a href="{{ $settings->social_whatsapp }}" target="_blank" rel="noopener" class="hover:opacity-80">واتس‌اپ</a>@endif
                @if($settings->social_linkedin)<a href="{{ $settings->social_linkedin }}" target="_blank" rel="noopener" class="hover:opacity-80">لینکدین</a>@endif
            </div>
            <div class="flex flex-col gap-3">
                <span class="opacity-60 font-display uppercase tracking-widest text-xs font-bold">استودیو</span>
                <span class="opacity-80">{{ $settings->tagline }}</span>
            </div>
        </div>

        <div style="border-top: 3px dashed color-mix(in srgb, var(--color-bg) 35%, transparent)"></div>

        <div class="flex flex-col md:flex-row justify-between gap-3 text-xs opacity-70 font-display uppercase tracking-widest font-bold">
            <span>© {{ date('Y') }} {{ $settings->site_name }}</span>
            <span>{{ $settings->footer_text }}</span>
        </div>
    </div>
</footer>
