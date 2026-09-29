<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        @php
            // Rendered server-side so crawlers and link-preview bots (which don't run JS) see it.
            // ponytail: not updated on client-side navigation; only matters for the first load, which is what bots fetch.
            $appName = config('app.name', 'Laravel');
            $meta = $page['props']['meta'] ?? [];
            $metaTitle = filled($meta['title'] ?? null) ? "{$meta['title']} - {$appName}" : $appName;
            $metaImage = ($meta['image'] ?? null) ?: ($page['props']['generalSettings']['church_logo'] ?? null);
        @endphp
        <title inertia>{{ $metaTitle }}</title>
        <meta property="og:type" content="website">
        <meta property="og:site_name" content="{{ $appName }}">
        <meta property="og:title" content="{{ $metaTitle }}">
        <meta property="og:url" content="{{ url()->current() }}">
        @if (filled($meta['description'] ?? null))
            <meta name="description" content="{{ $meta['description'] }}">
            <meta property="og:description" content="{{ $meta['description'] }}">
        @endif
        @if ($metaImage)
            <meta property="og:image" content="{{ url(Storage::disk('public')->url($metaImage)) }}">
            <meta name="twitter:card" content="summary_large_image">
        @endif

        <!-- Scripts -->
        @routes
        @vite(['resources/js/app.ts', "resources/js/Pages/{$page['component']}.vue"])
        @vite('resources/css/app.css')
        @inertiaHead
    </head>
    <body class="bg-white font-sans text-ink antialiased">
        @inertia
    </body>
</html>
