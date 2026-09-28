@props(['title' => null])
<!doctype html>
<html lang="fa" dir="rtl" class="no-js">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? ($settings->site_name . ' | ' . $settings->tagline) }}</title>
    <meta name="description" content="{{ $settings->tagline }}">
    @if($settings->favicon_url)
        <link rel="icon" href="{{ $settings->favicon_url }}">
    @endif
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <style>{!! \App\Support\Theme::css($settings) !!}</style>
    <script>document.documentElement.classList.remove('no-js');</script>
</head>
<body>
    <div class="grain-overlay"></div>

    @include('partials.site.header', ['settings' => $settings])

    <main class="pt-24">
        {{ $slot }}
    </main>

    @include('partials.site.footer', ['settings' => $settings])

    <script src="{{ asset('js/app.js') }}" defer></script>
</body>
</html>
