<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
  <url>
    <loc>{{ url('/') }}</loc>
    <priority>1.0</priority>
  </url>
  <url>
    <loc>{{ route('about') }}</loc>
    <priority>0.3</priority>
  </url>
@foreach ($areaSlugs as $areaSlug)
  <url>
    <loc>{{ route('dishes.area', $areaSlug) }}</loc>
    <priority>0.8</priority>
  </url>
@endforeach
@foreach ($dishes as $dish)
  <url>
    <loc>{{ route('dishes.show', $dish) }}</loc>
    <lastmod>{{ $dish->updated_at->toAtomString() }}</lastmod>
    <priority>0.7</priority>
  </url>
@endforeach
</urlset>
