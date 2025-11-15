@extends('layouts.public')

@section('title', $news->title.' | '.config('app.name','Aplikasi'))

@section('content')
    <article class="max-w-3xl mx-auto rounded-2xl border border-gray-100 bg-white/95 backdrop-blur shadow-sm p-6">
        <p class="text-xs font-semibold uppercase tracking-[0.3em] text-orange-500">Berita</p>
        <h1 class="text-3xl font-bold text-gray-900 mt-2">{{ $news->title }}</h1>
        <p class="text-gray-500 text-sm mt-1">{{ optional($news->published_at)->format('d M Y H:i') }}</p>
        @if($news->cover_image)
            <img src="{{ asset($news->cover_image) }}" alt="{{ $news->title }}" class="mt-5 rounded-2xl border border-gray-100">
        @endif
        <div class="prose max-w-none mt-6 text-gray-700">
            {!! nl2br(e($news->body)) !!}
        </div>
    </article>
    <div class="max-w-3xl mx-auto">
        <a href="{{ route('news.index') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-orange-600 mt-4">
            <i class="fas fa-arrow-left text-xs"></i> Kembali ke daftar berita
        </a>
    </div>
@endsection
