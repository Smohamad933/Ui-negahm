@php
    $ratio = $category->normalizedAspectRatio();
    $tileClass = \App\Support\Aspect::TILE_CLASS[$ratio];
    $gridClass = \App\Support\Aspect::GRID_CLASS[$ratio];
@endphp
<x-layouts.site title="{{ $category->title }} — {{ $client->name }} | {{ $settings->site_name }}">

    <section class="container-px pt-6 pb-10">
        <a href="{{ route('clients.show', $client) }}" class="tag-pill inline-flex mb-8">→ بازگشت به {{ $client->name }}</a>

        <span class="eyebrow">{{ $client->name }}</span>
        <h1 class="h-hero font-display mt-6">{{ $category->title }}</h1>
        @if($category->description)
            <p class="max-w-2xl mt-6 text-base md:text-lg" style="color: var(--color-muted)">{{ $category->description }}</p>
        @endif

        @if($otherCategories->isNotEmpty())
            <div class="flex flex-wrap gap-3 mt-8">
                @foreach($otherCategories as $other)
                    <a href="{{ route('clients.category', ['client' => $client, 'category' => $other]) }}" class="tag-pill">{{ $other->title }}</a>
                @endforeach
            </div>
        @endif
    </section>

    <section class="container-px pb-24">
        @if($items->isEmpty())
            <p class="reveal-up" style="color: var(--color-muted)">هنوز موردی در این دسته‌بندی ثبت نشده است.</p>
        @else
            {{-- all items share the SAME fixed aspect ratio -> a uniform grid never crops or gaps --}}
            <div class="{{ $gridClass }}">
                @foreach($items as $item)
                    <a href="#lightbox-{{ $item->id }}" class="client-card block reveal-up {{ $tileClass }} overflow-hidden" style="animation-delay: {{ $loop->index * 0.05 }}s">
                        @if($item->media_type === 'video')
                            <video src="{{ $item->media_url }}" class="w-full h-full object-cover" muted loop playsinline autoplay></video>
                        @else
                            <img src="{{ $item->media_url }}" alt="{{ $item->title ?: $category->title }}" class="w-full h-full object-cover" loading="lazy">
                        @endif
                    </a>
                @endforeach
            </div>

            @foreach($items as $item)
                <div id="lightbox-{{ $item->id }}" class="lightbox">
                    <a href="#" class="lightbox-close">بستن ✕</a>
                    @if($item->media_type === 'video')
                        <video src="{{ $item->media_url }}" controls autoplay></video>
                    @else
                        <img src="{{ $item->media_url }}" alt="{{ $item->title ?: $category->title }}">
                    @endif
                </div>
            @endforeach
        @endif
    </section>

</x-layouts.site>
