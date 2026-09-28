@php
    $industries = $clients->pluck('industry')->filter()->unique()->values();
    $active = request('industry');
@endphp
<x-layouts.site title="همراهان | {{ $settings->site_name }}">

    <section class="container-px pt-6 pb-10">
        <span class="eyebrow">نمونه‌کارها</span>
        <h1 class="h-hero font-display mt-6">همراهانی که با هم ساختیم</h1>

        @if($industries->isNotEmpty())
            <div class="flex flex-wrap gap-3 mt-10">
                <a href="{{ route('clients.index') }}" class="tag-pill {{ !$active ? 'font-display' : '' }}" style="{{ !$active ? 'background:var(--color-fg);color:var(--color-bg)' : '' }}">همه</a>
                @foreach($industries as $industry)
                    <a href="{{ route('clients.index', ['industry' => $industry]) }}" class="tag-pill" style="{{ $active === $industry ? 'background:var(--color-fg);color:var(--color-bg)' : '' }}">{{ $industry }}</a>
                @endforeach
            </div>
        @endif
    </section>

    <section class="container-px pb-24">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($clients as $client)
                @continue($active && $client->industry !== $active)
                <a href="{{ route('clients.show', $client) }}" class="client-card p-6 flex flex-col gap-4 reveal-up" style="animation-delay: {{ $loop->index * 0.05 }}s">
                    @if($client->cover_image_url)
                        <div class="rounded-2xl overflow-hidden frame-pop aspect-16-9">
                            <img src="{{ $client->cover_image_url }}" alt="{{ $client->name }}" class="w-full h-full object-cover" loading="lazy">
                        </div>
                    @endif
                    <div class="flex items-center justify-between gap-3">
                        <span class="font-display font-extrabold text-lg">{{ $client->name }}</span>
                        @if($client->industry)<span class="tag-pill">{{ $client->industry }}</span>@endif
                    </div>
                    @if($client->short_description)
                        <p class="text-sm" style="color: var(--color-muted)">{{ \Illuminate\Support\Str::limit($client->short_description, 110) }}</p>
                    @endif
                </a>
            @endforeach
        </div>
    </section>

</x-layouts.site>
