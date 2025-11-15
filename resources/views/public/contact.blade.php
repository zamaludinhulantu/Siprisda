@extends('layouts.public')

@section('title', 'Kontak | '.config('app.name','Aplikasi'))

@section('hero')
    <div class="max-w-3xl">
        <p class="text-xs font-semibold uppercase tracking-[0.3em] text-orange-500">Hubungi Kami</p>
        <h1 class="text-3xl sm:text-4xl font-bold text-gray-900 mt-3">Tim Pengelola Portal Penelitian</h1>
        <p class="text-gray-600 mt-4 text-lg">Silakan hubungi kami untuk pertanyaan, masukan, atau permintaan data tambahan.</p>
    </div>
@endsection

@section('content')
    <section class="rounded-2xl border border-gray-100 bg-white/95 backdrop-blur shadow-sm p-6">
        <div class="grid gap-6 sm:grid-cols-2 text-sm text-gray-600">
            <div>
                <p class="text-xs uppercase font-semibold text-gray-500">Email</p>
                <p class="text-gray-900 mt-1">publikasi@bappeda.go.id</p>
            </div>
            <div>
                <p class="text-xs uppercase font-semibold text-gray-500">Telepon</p>
                <p class="text-gray-900 mt-1">(0435) 123-456</p>
            </div>
            <div>
                <p class="text-xs uppercase font-semibold text-gray-500">Alamat</p>
                <p class="text-gray-900 mt-1">Jl. Pembangunan No. 1, Kota Gorontalo</p>
            </div>
            <div>
                <p class="text-xs uppercase font-semibold text-gray-500">Jam Layanan</p>
                <p class="text-gray-900 mt-1">Senin - Jumat, 08.00 - 16.00 WITA</p>
            </div>
        </div>
    </section>
@endsection
