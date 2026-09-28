<x-layouts.site title="{{ $client->name }} | {{ $settings->site_name }}">

    <section class="container-px pt-6 pb-10">
        <a href="{{ route('clients.index') }}" class="tag-pill inline-flex mb-8">→ بازگشت به همراهان</a>

        <div class="flex flex-col md:flex-row md:items-end justify-between gap-6">
            <div>
                @if($client->industry)<span class="eyebrow">{{ $client->industry }}</span>@endif
                <h1 class="h-hero font-display mt-6">{{ $client->name }}</h1>
                @if($client->short_description)
                    <p class="max-w-2xl mt-6 text-base md:text-lg" style="color: var(--color-muted)">{{ $client->short_description }}</p>
                @endif
            </div>
            @if($client->website_url)
                <a href="{{ $client->website_url }}" target="_blank" rel="noopener" class="btn-pill shrink-0">وب‌سایت ↗</a>
            @endif
        </div>

        <div class="flex flex-wrap gap-3 mt-8 text-sm" style="color: var(--color-muted)">
            @if($client->year)<span class="tag-pill">سال {{ $client->year }}</span>@endif
        </div>
    </section>

    <section class="container-px pb-24">
        @if($categories->isEmpty())
            <p class="reveal-up" style="color: var(--color-muted)">هنوز نمونه‌کاری برای این همراه ثبت نشده است.</p>
        @else
            <div class="masonry masonry-3">
                @foreach($categories as $category)
                    <a href="{{ route('clients.category', ['client' => $client, 'category' => $category]) }}"
                       class="client-card block reveal-up" style="animation-delay: {{ $loop->index * 0.06 }}s">
                        @if($category->cover_image_url)
                            <div class="{{ \App\Support\Aspect::TILE_CLASS[$category->normalizedAspectRatio()] }} overflow-hidden">
                                <img src="{{ $category->cover_image_url }}" alt="{{ $category->title }}" class="w-full h-full object-cover" loading="lazy">
                            </div>
                        @endif
                        <div class="p-4">
                            <span class="font-display font-bold">{{ $category->title }}</span>
                        </div>
                    </a>
                @endforeach
            </div>
        @endif
    </section>

</x-layouts.site>
