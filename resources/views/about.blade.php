@extends('layouts.plain')

@section('title', 'このサイトについて | '.config('app.name'))
@section('description', config('app.name').'の掲載データの出所と、扱い方について説明しています。')

@section('content')
<div class="container my-4" style="max-width: 720px;">
  <h1 class="h4 fw-bold mb-4">このサイトについて</h1>

  <section class="mb-4">
    <h2 class="h6">サイトの目的</h2>
    <p class="text-muted small">
      「{{ config('app.name') }}」は、47都道府県の郷土料理を、都道府県と食材から探せるようにしたサイトです。
      旅先で何を食べるか決めるとき、家にある食材から郷土料理を探したいとき、地域の食文化を調べたいときにお使いください。
    </p>
  </section>

  <section class="mb-4">
    <h2 class="h6">掲載しているデータについて</h2>
    <p class="text-muted small">
      料理名・都道府県・主な伝承地域・主な使用食材は、
      <a href="{{ \App\Models\Dish::SOURCE_URL }}" rel="nofollow noopener" target="_blank">{{ \App\Models\Dish::SOURCE_LABEL }}</a>
      の記載にもとづいています。由来・作り方・食べ方の解説は転載せず、各料理のページから農林水産省のページへリンクしています。
      写真も掲載していません。
    </p>
    <p class="text-muted small">
      同じ名前の料理でも、地域や家庭によって材料や作り方は異なります。ここに載せているのは
      「その県の郷土料理として選定されたもの」であり、県内のすべての作り方を代表するものではありません。
    </p>
  </section>

  <section class="mb-4">
    <h2 class="h6">お店の情報について</h2>
    <p class="text-muted small">
      提供しているお店の情報は掲載していません。実際に食べられる場所は、各自治体の観光情報などをご確認ください。
    </p>
  </section>


  <section class="mb-4">
    <h2 class="h6">お問い合わせ</h2>
    <p class="text-muted small">
      掲載内容の誤りのご指摘、掲載を希望されない旨のご連絡は、下記へお送りください。
      内容を確認のうえ対応します。
    </p>
    <p class="small">
      <a href="mailto:{{ config('mail.contact_address') }}">{{ config('mail.contact_address') }}</a>
    </p>
    <p class="text-muted small">
      個別のご相談・お問い合わせの仲介は行っておりません。施設・教室へのご用件は、
      各施設へ直接お問い合わせください。
    </p>
  </section>
  <a href="{{ route('dishes.index') }}" class="d-block text-center text-muted mt-4">トップページに戻る</a>
</div>
@endsection
