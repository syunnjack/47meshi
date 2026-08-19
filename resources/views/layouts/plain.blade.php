<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
  <meta name="theme-color" content="#b5482f">

  <title>@yield('title', config('app.name').' | 47都道府県の郷土料理')</title>
  <meta name="description" content="@yield('description', '47都道府県の郷土料理を、都道府県と食材から探せます。出典は農林水産省「うちの郷土料理」。')">

  @php
      // url()->current() はクエリを落とすため、内容が変わる page だけを残す。
      $canonicalQuery = array_filter(request()->only(['page']), fn ($value) => $value !== null && $value !== '' && $value !== '1');
      $canonicalUrl = url()->current().($canonicalQuery ? '?'.http_build_query($canonicalQuery) : '');
  @endphp
  <link rel="canonical" href="{{ $canonicalUrl }}">

  <meta property="og:site_name" content="{{ config('app.name') }}">
  <meta property="og:type" content="website">
  <meta property="og:title" content="@yield('title', config('app.name').' | 47都道府県の郷土料理')">
  <meta property="og:description" content="@yield('description', '47都道府県の郷土料理を、都道府県と食材から探せます。出典は農林水産省「うちの郷土料理」。')">
  <meta property="og:url" content="{{ $canonicalUrl }}">
  <meta property="og:locale" content="ja_JP">
  <meta name="twitter:card" content="summary">

  @if(config('services.google_site_verification'))
  <meta name="google-site-verification" content="{{ config('services.google_site_verification') }}">
  @endif

  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
  <style>
    body { background-color: #fdf8f3; font-family: system-ui, -apple-system, "Hiragino Sans", "Noto Sans JP", sans-serif; }
    a { color: #a8412a; }
    .dish-card { border: 1px solid #eadfd3; background: #fff; border-radius: .5rem; }
    .dish-card:hover { border-color: #d9a58a; }
    .pref-pill { text-decoration: none; }
  </style>
  @stack('structured-data')

  @if(config('services.ga_measurement_id'))
  <script async src="https://www.googletagmanager.com/gtag/js?id={{ config('services.ga_measurement_id') }}"></script>
  <script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());
    gtag('config', '{{ config('services.ga_measurement_id') }}');
  </script>
  @endif
</head>
<body>
  <header class="border-bottom bg-white">
    <div class="container py-3 d-flex justify-content-between align-items-center">
      <a href="{{ route('dishes.index') }}" class="fw-bold fs-5 text-decoration-none">🍚 {{ config('app.name') }}</a>
      <a href="{{ route('about') }}" class="small">このサイトについて</a>
    </div>
  </header>

  @yield('content')

  <footer class="border-top mt-5 py-4 bg-white">
    <div class="container small text-muted">
      <p class="mb-1">
        掲載している郷土料理は
        <a href="{{ \App\Models\Dish::SOURCE_URL }}" rel="nofollow noopener" target="_blank">{{ \App\Models\Dish::SOURCE_LABEL }}</a>
        をもとにしています。
      </p>
      <p class="mb-0">&copy; {{ date('Y') }} {{ config('app.name') }}</p>
    </div>
  </footer>
</body>
</html>
