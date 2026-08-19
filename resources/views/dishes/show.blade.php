@extends('layouts.plain')

@section('title', $dish->name.'（'.$dish->area.'の郷土料理） | '.config('app.name'))
@section('description', $dish->name.'は'.$dish->area.'の郷土料理です。'.($dish->region ? '主な伝承地域は'.$dish->region.'。' : '').($dish->ingredients ? '主な使用食材は'.$dish->ingredients.'。' : '').'出典は農林水産省「うちの郷土料理」。')

@push('structured-data')
<script type="application/ld+json">
{!! json_encode([
  '@@context' => 'https://schema.org',
  '@type' => 'BreadcrumbList',
  'itemListElement' => [
      ['@type' => 'ListItem', 'position' => 1, 'name' => config('app.name'), 'item' => url('/')],
      ['@type' => 'ListItem', 'position' => 2, 'name' => $dish->area, 'item' => route('dishes.area', $dish->area_slug)],
      ['@type' => 'ListItem', 'position' => 3, 'name' => $dish->name, 'item' => route('dishes.show', $dish)],
  ],
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}
</script>
@endpush

@section('content')
<div class="container my-4" style="max-width: 760px;">
  <nav aria-label="パンくず" class="small mb-3">
    <a href="{{ route('dishes.index') }}">{{ config('app.name') }}</a>
    <span class="text-muted mx-1">/</span>
    <a href="{{ route('dishes.area', $dish->area_slug) }}">{{ $dish->area }}</a>
    <span class="text-muted mx-1">/</span><span class="text-muted">{{ $dish->name }}</span>
  </nav>

  <h1 class="h3 fw-bold">{{ $dish->name }}</h1>
  <p class="text-muted">{{ $dish->area }}の郷土料理</p>

  <table class="table">
    <tbody>
      <tr>
        <th scope="row" class="text-muted small" style="width: 10rem;">都道府県</th>
        <td><a href="{{ route('dishes.area', $dish->area_slug) }}">{{ $dish->area }}</a></td>
      </tr>
      @if($dish->region)
      <tr>
        <th scope="row" class="text-muted small">主な伝承地域</th>
        <td>{{ $dish->region }}</td>
      </tr>
      @endif
      @if($dish->ingredient_list)
      <tr>
        <th scope="row" class="text-muted small">主な使用食材</th>
        <td>
          @foreach($dish->ingredient_list as $ingredient)
            <a href="{{ route('dishes.ingredient', $ingredient) }}" class="badge bg-light text-dark border text-decoration-none mb-1">{{ $ingredient }}</a>
          @endforeach
        </td>
      </tr>
      @endif
    </tbody>
  </table>

  <div class="alert alert-light border small">
    この料理の由来・作り方・食べ方は、農林水産省のページに詳しく載っています。<br>
    <a href="{{ $dish->source_url }}" rel="nofollow noopener" target="_blank">{{ \App\Models\Dish::SOURCE_LABEL }}で「{{ $dish->name }}」を読む</a>
    @if($dish->confirmed_on)
      <span class="text-muted">（{{ $dish->confirmed_on->format('Y年n月j日') }}確認）</span>
    @endif
  </div>

  @if($others->isNotEmpty())
    <h2 class="h6 mt-4">{{ $dish->area }}のほかの郷土料理</h2>
    <div class="d-flex flex-wrap gap-2">
      @foreach($others as $other)
        <a href="{{ route('dishes.show', $other) }}" class="btn btn-sm btn-outline-secondary">{{ $other->name }}</a>
      @endforeach
    </div>
    <p class="mt-3"><a href="{{ route('dishes.area', $dish->area_slug) }}">{{ $dish->area }}の郷土料理をすべて見る</a></p>
  @endif
</div>
@endsection
