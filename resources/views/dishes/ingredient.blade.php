@extends('layouts.plain')

@section('title', $name.'を使う郷土料理'.number_format($dishes->count()).'品 | '.config('app.name'))
@section('description', $name.'を主な材料に使う郷土料理'.number_format($dishes->count()).'品を、都道府県をまたいで一覧にしました。出典は農林水産省「うちの郷土料理」。')

@section('content')
<div class="container my-4">
  <nav aria-label="パンくず" class="small mb-3">
    <a href="{{ route('dishes.index') }}">{{ config('app.name') }}</a>
    <span class="text-muted mx-1">/</span><span class="text-muted">{{ $name }}</span>
  </nav>

  <h1 class="h4 fw-bold">{{ $name }}を使う郷土料理</h1>
  <p class="text-muted">{{ number_format($dishes->count()) }}品を掲載しています。</p>

  <div class="row g-3">
    @foreach($dishes as $dish)
      <div class="col-12 col-md-6">
        <a href="{{ route('dishes.show', $dish) }}" class="d-block p-3 h-100 text-decoration-none dish-card">
          <span class="badge bg-light text-dark border mb-1">{{ $dish->area }}</span>
          <div class="fw-semibold">{{ $dish->name }}</div>
          @if($dish->ingredients)
            <div class="small text-muted">{{ $dish->ingredients }}</div>
          @endif
        </a>
      </div>
    @endforeach
  </div>
</div>
@endsection
