@extends('layouts.public')

@section('title', 'Institusi | '.config('app.name','Aplikasi'))

@section('hero')
    @php
        $totalInstitutions = method_exists($institutions, 'total') ? $institutions->total() : $institutions->count();
    @endphp
    <div class="grid gap-6 lg:grid-cols-[minmax(0,3fr)_minmax(0,2fr)] items-center">
        <div>
            <p class="text-xs font-semibold uppercase tracking-[0.3em] text-orange-500">Direktori Resmi</p>
            <h1 class="text-3xl sm:text-4xl font-bold text-gray-900 mt-3">Institusi Kontributor Penelitian</h1>
            <p class="text-gray-600 mt-4 text-lg">Daftar lengkap instansi yang telah menyetor penelitian ke Bappeda. Gunakan daftar berikut untuk menemukan kolaborator dan menelusuri karya mereka.</p>
        </div>
        <div class="rounded-2xl border border-orange-100 bg-white/80 backdrop-blur p-6 shadow-sm">
            <p class="text-sm font-semibold text-gray-900">Ringkasan Cepat</p>
            <ul class="mt-4 space-y-3 text-sm text-gray-600">
                <li class="flex items-center gap-3">
                    <span class="h-2 w-2 rounded-full bg-orange-500"></span>
                    Total institusi aktif: {{ number_format($totalInstitutions) }}
                </li>
                <li class="flex items-center gap-3">
                    <span class="h-2 w-2 rounded-full bg-gray-400"></span>
                    Penelitian terverifikasi otomatis
                </li>
                <li class="flex items-center gap-3">
                    <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                    Klik institusi untuk melihat katalog publik
                </li>
            </ul>
        </div>
    </div>
@endsection

@section('content')
    <section class="rounded-2xl border border-gray-100 bg-white/95 backdrop-blur shadow-sm">
        <div class="flex flex-wrap items-center justify-between gap-3 px-6 py-4 border-b border-gray-100">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Direktori Institusi</p>
                <p class="text-sm text-gray-500">Menampilkan daftar institusi beserta jumlah penelitian disetujui.</p>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100 text-sm">
                <thead class="bg-gray-50 text-xs font-semibold uppercase tracking-wide text-gray-500">
                    <tr>
                        <th class="px-6 py-3 text-left">Institusi</th>
                        <th class="px-6 py-3 text-left">Tipe</th>
                        <th class="px-6 py-3 text-left">Kota</th>
                        <th class="px-6 py-3 text-left">Jumlah Penelitian</th>
                        <th class="px-6 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @forelse($institutions as $inst)
                        <tr class="hover:bg-orange-50/40 transition">
                            <td class="px-6 py-4">
                                <p class="font-semibold text-gray-900">{{ $inst->name }}</p>
                                <p class="text-xs text-gray-500">ID #{{ $inst->id }}</p>
                            </td>
                            <td class="px-6 py-4 text-gray-700">{{ $inst->type ?? '-' }}</td>
                            <td class="px-6 py-4 text-gray-700">{{ $inst->city ?? '-' }}</td>
                            <td class="px-6 py-4 text-gray-700">{{ number_format($inst->researches_count ?? 0) }}</td>
                            <td class="px-6 py-4 text-right">
                                <a href="/?institution={{ $inst->id }}" class="inline-flex items-center gap-2 rounded-lg border border-gray-200 px-3 py-1.5 text-xs font-semibold text-gray-700 hover:bg-white">
                                    <i class="fas fa-arrow-up-right-from-square text-[11px]"></i> Buka Katalog
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-6 text-center text-gray-500 text-sm">Belum ada data institusi.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
@endsection
