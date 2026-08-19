@extends('layouts.plain')

@section('title', config('app.name').' | 47都道府県の郷土料理'.number_format($total).'品')
@section('description', '47都道府県の郷土料理'.number_format($total).'品を、都道府県と食材から探せます。主な伝承地域と使用食材つき。出典は農林水産省「うちの郷土料理」。')

@push('structured-data')
<script type="application/ld+json">
{!! json_encode([
  '@@context' => 'https://schema.org',
  '@type' => 'WebSite',
  'name' => config('app.name'),
  'url' => url('/'),
  'description' => '47都道府県の郷土料理を、都道府県と食材から探せるサイト。',
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}
</script>
@endpush

@section('content')
<div class="container my-4">
  <div class="text-center mb-4">
    <h1 class="h3 fw-bold">47都道府県の郷土料理</h1>
    <p class="text-muted mb-0">{{ number_format($total) }}品を、都道府県と食材から探せます。</p>
  </div>

  @foreach(\App\Models\Dish::REGIONS as $region => $areas)
    <h2 class="h6 mt-4">{{ $region }}</h2>
    <div class="d-flex flex-wrap gap-2">
      @foreach($areas as $area)
        @if($counts->has($area))
          <a href="{{ route('dishes.area', $slugs[$area]) }}" class="btn btn-sm btn-outline-secondary pref-pill">
            {{ $area }} <span class="text-muted">{{ $counts[$area] }}</span>
          </a>
        @endif
      @endforeach
    </div>
  @endforeach

  <h2 class="h6 mt-5">新しく登録された料理</h2>
  <div class="row g-3">
    @foreach($newest as $dish)
      <div class="col-6 col-md-4 col-lg-3">
        <a href="{{ route('dishes.show', $dish) }}" class="d-block p-3 h-100 text-decoration-none dish-card">
          <span class="badge bg-light text-dark border mb-1">{{ $dish->area }}</span>
          <div class="fw-semibold">{{ $dish->name }}</div>
          @if($dish->ingredients)
            <div class="small text-muted">{{ \Illuminate\Support\Str::limit($dish->ingredients, 28) }}</div>
          @endif
        </a>
      </div>
    @endforeach
  </div>

  <p class="text-muted small mt-4">
    料理名・主な伝承地域・主な使用食材は {{ \App\Models\Dish::SOURCE_LABEL }} の記載にもとづいています。
    同じ名前でも地域や家庭によって作り方は異なります。詳しい由来や作り方は、各ページから農林水産省のページをご覧ください。
  </p>
</div>
@endsection
