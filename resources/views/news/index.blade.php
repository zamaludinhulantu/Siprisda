@extends('layouts.public')

@section('title', 'Berita | '.config('app.name','Aplikasi'))

@section('hero')
    <div class="grid gap-6 lg:grid-cols-2">
        <div>
            <p class="text-xs font-semibold uppercase tracking-[0.3em] text-orange-500">Berita Terbaru</p>
            <h1 class="text-3xl sm:text-4xl font-bold text-gray-900 mt-3">Aktivitas dan Rilis Resmi Bappeda</h1>
            <p class="text-gray-600 mt-4 text-lg">Ikuti perkembangan kebijakan, seminar, dan publikasi terbaru terkait penelitian di Gorontalo.</p>
        </div>
    </div>
@endsection

@section('content')
    <section class="space-y-6">
        @forelse($news as $item)
            <article class="rounded-2xl border border-gray-100 bg-white/95 backdrop-blur shadow-sm p-6">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <h2 class="text-xl font-semibold text-gray-900">
                        <a href="{{ route('news.show', $item->slug) }}" class="hover:text-orange-600">{{ $item->title }}</a>
                    </h2>
                    <span class="text-xs text-gray-500">{{ optional($item->published_at)->format('d M Y') }}</span>
                </div>
                @if($item->excerpt)
                    <p class="mt-3 text-gray-600">{{ $item->excerpt }}</p>
                @endif
                <a href="{{ route('news.show', $item->slug) }}" class="mt-4 inline-flex items-center gap-2 text-sm font-semibold text-orange-600">
                    Baca selengkapnya <i class="fas fa-arrow-right text-xs"></i>
                </a>
            </article>
        @empty
            <p class="text-sm text-gray-500">Belum ada berita.</p>
        @endforelse
    </section>
    <div>
        {{ $news->links() }}
    </div>
@endsection
