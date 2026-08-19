@extends('layouts.plain')

@section('title', $area.'の郷土料理'.number_format($dishes->count()).'品 | '.config('app.name'))
@section('description', $area.'の郷土料理'.number_format($dishes->count()).'品を一覧で紹介します。主な伝承地域と使用食材つき。出典は農林水産省「うちの郷土料理」。')

@push('structured-data')
<script type="application/ld+json">
{!! json_encode([
  '@@context' => 'https://schema.org',
  '@type' => 'BreadcrumbList',
  'itemListElement' => [
      ['@type' => 'ListItem', 'position' => 1, 'name' => config('app.name'), 'item' => url('/')],
      ['@type' => 'ListItem', 'position' => 2, 'name' => $area, 'item' => route('dishes.area', $areaSlug)],
  ],
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}
</script>
@endpush

@section('content')
<div class="container my-4">
  <nav aria-label="パンくず" class="small mb-3">
    <a href="{{ route('dishes.index') }}">{{ config('app.name') }}</a>
    <span class="text-muted mx-1">/</span><span class="text-muted">{{ $area }}</span>
  </nav>

  <h1 class="h4 fw-bold">{{ $area }}の郷土料理</h1>
  <p class="text-muted">{{ number_format($dishes->count()) }}品を掲載しています。</p>

  <div class="row g-3">
    @foreach($dishes as $dish)
      <div class="col-12 col-md-6">
        <a href="{{ route('dishes.show', $dish) }}" class="d-block p-3 h-100 text-decoration-none dish-card">
          <div class="fw-semibold">{{ $dish->name }}</div>
          @if($dish->region)
            <div class="small text-muted">主な伝承地域: {{ $dish->region }}</div>
          @endif
          @if($dish->ingredients)
            <div class="small text-muted">主な使用食材: {{ $dish->ingredients }}</div>
          @endif
        </a>
      </div>
    @endforeach
  </div>

  <p class="text-muted small mt-4">
    出典: <a href="{{ \App\Models\Dish::SOURCE_URL }}" rel="nofollow noopener" target="_blank">{{ \App\Models\Dish::SOURCE_LABEL }}</a>
    （{{ optional($dishes->first()->confirmed_on)->format('Y年n月j日') }}確認）
  </p>
</div>
@endsection
