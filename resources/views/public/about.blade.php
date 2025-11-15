@extends('layouts.public')

@section('title', 'Tentang | '.config('app.name','Aplikasi'))

@section('hero')
    <div class="max-w-3xl">
        <p class="text-xs font-semibold uppercase tracking-[0.3em] text-orange-500">Tentang Kami</p>
        <h1 class="text-3xl sm:text-4xl font-bold text-gray-900 mt-3">Portal Penelitian Terbuka Bappeda</h1>
        <p class="text-gray-600 mt-4 text-lg">Misi kami adalah memastikan setiap penelitian strategis daerah terdokumentasi, dapat diakses publik, dan menjadi dasar keputusan pemerintah.</p>
    </div>
@endsection

@section('content')
    <section class="rounded-2xl border border-gray-100 bg-white/95 backdrop-blur shadow-sm p-6">
        <h2 class="text-xl font-semibold text-gray-900">Visi</h2>
        <p class="text-gray-600 mt-2 leading-7">
            Mewujudkan ekosistem penelitian yang transparan, inklusif, dan berorientasi pada kebutuhan pembangunan daerah melalui pemanfaatan teknologi informasi.
        </p>
    </section>

    <section class="rounded-2xl border border-gray-100 bg-white/90 p-6 shadow-sm">
        <h2 class="text-xl font-semibold text-gray-900">Nilai Utama</h2>
        <ul class="mt-4 grid gap-4 sm:grid-cols-3 text-sm text-gray-600">
            <li class="rounded-xl border border-gray-100 bg-white p-4">
                <p class="font-semibold text-gray-900">Transparansi</p>
                <p>Membuka akses publik terhadap data penelitian.</p>
            </li>
            <li class="rounded-xl border border-gray-100 bg-white p-4">
                <p class="font-semibold text-gray-900">Kolaborasi</p>
                <p>Menghubungkan peneliti, institusi, dan pemangku kepentingan.</p>
            </li>
            <li class="rounded-xl border border-gray-100 bg-white p-4">
                <p class="font-semibold text-gray-900">Akurasi</p>
                <p>Menjaga kualitas data melalui kurasi berlapis.</p>
            </li>
        </ul>
    </section>
@endsection
