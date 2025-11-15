@extends('layouts.public')

@section('title', 'Statistik | '.config('app.name','Aplikasi'))

@push('head')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endpush

@section('hero')
    <div class="grid gap-6 lg:grid-cols-2">
        <div>
            <p class="text-xs font-semibold uppercase tracking-[0.3em] text-orange-500">Pantauan Publik</p>
            <h1 class="text-3xl sm:text-4xl font-bold text-gray-900 mt-3">Statistik Penelitian Terverifikasi</h1>
            <p class="text-gray-600 mt-4 text-lg">Grafik ini menampilkan distribusi penelitian berdasarkan bidang dan tahun. Data diperbarui otomatis setelah admin menyetujui penelitian.</p>
        </div>
        <div class="rounded-2xl border border-orange-100 bg-white/80 backdrop-blur p-6 shadow-sm">
            <p class="text-sm font-semibold text-gray-900">Mengapa penting?</p>
            <ul class="mt-4 space-y-3 text-sm text-gray-600">
                <li class="flex items-center gap-3"><span class="h-2 w-2 rounded-full bg-orange-500"></span>Mengamati prioritas riset daerah</li>
                <li class="flex items-center gap-3"><span class="h-2 w-2 rounded-full bg-emerald-500"></span>Menemukan tren kolaborasi lintas institusi</li>
                <li class="flex items-center gap-3"><span class="h-2 w-2 rounded-full bg-sky-500"></span>Mendukung pengambilan keputusan berbasis data</li>
            </ul>
        </div>
    </div>
@endsection

@section('content')
    <section class="rounded-2xl border border-gray-100 bg-white/95 backdrop-blur shadow-sm p-6">
        <h2 class="text-xl font-semibold text-gray-900">Jumlah Penelitian per Bidang (Approved)</h2>
        <div class="mt-6">
            <canvas id="chartField" height="120"></canvas>
        </div>
    </section>

    <section class="rounded-2xl border border-gray-100 bg-white/95 backdrop-blur shadow-sm p-6">
        <h2 class="text-xl font-semibold text-gray-900">Jumlah Penelitian per Tahun (Approved)</h2>
        <div class="mt-6">
            <canvas id="chartYear" height="120"></canvas>
        </div>
    </section>
@endsection

@push('scripts')
<script>
    const fieldLabels = @json($perField->pluck('name'));
    const fieldData = @json($perField->pluck('researches_count'));
    const ctx1 = document.getElementById('chartField');
    new Chart(ctx1, {type:'bar', data:{labels:fieldLabels, datasets:[{label:'Jumlah', data:fieldData, backgroundColor:'#fb923c', borderRadius:6}]}, options:{plugins:{legend:{display:false}}}});

    const yearLabels = @json($perYear->pluck('year'));
    const yearData = @json($perYear->pluck('total'));
    const ctx2 = document.getElementById('chartYear');
    new Chart(ctx2, {
        type:'line',
        data:{labels:yearLabels, datasets:[{label:'Per Tahun', data:yearData, borderColor:'#0ea5e9', backgroundColor:'rgba(14,165,233,.2)', tension:0.35, fill:true, pointRadius:4, pointBackgroundColor:'#0ea5e9'}]},
        options:{plugins:{legend:{display:false}}, scales:{y:{beginAtZero:true}}}
    });
</script>
@endpush
