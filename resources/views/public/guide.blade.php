@extends('layouts.public')

@section('title', 'Panduan | '.config('app.name','Aplikasi'))

@section('hero')
    <div class="max-w-3xl">
        <p class="text-xs font-semibold uppercase tracking-[0.3em] text-orange-500">Panduan Singkat</p>
        <h1 class="text-3xl sm:text-4xl font-bold text-gray-900 mt-3">Alur Pengajuan Penelitian ke Bappeda</h1>
        <p class="text-gray-600 mt-4 text-lg">Ikuti langkah demi langkah berikut agar pengajuan cepat diverifikasi Kesbangpol lalu terbit di portal publik.</p>
    </div>
@endsection

@section('content')
    <section class="rounded-2xl border border-gray-100 bg-white/95 backdrop-blur shadow-sm p-6">
        <ol class="space-y-6">
            <li class="flex gap-4">
                <div class="flex flex-col items-center">
                    <span class="h-10 w-10 rounded-full bg-orange-100 text-orange-600 font-semibold flex items-center justify-center">1</span>
                    <span class="flex-1 w-px bg-orange-100"></span>
                </div>
                <div>
                    <h2 class="text-lg font-semibold text-gray-900">Buat Akun & Profil</h2>
                    <p class="text-gray-600 mt-1">Registrasi sebagai peneliti, lengkapi data diri, institusi, dan kontak yang aktif.</p>
                </div>
            </li>
            <li class="flex gap-4">
                <div class="flex flex-col items-center">
                    <span class="h-10 w-10 rounded-full bg-orange-100 text-orange-600 font-semibold flex items-center justify-center">2</span>
                    <span class="flex-1 w-px bg-orange-100"></span>
                </div>
                <div>
                    <h2 class="text-lg font-semibold text-gray-900">Ajukan Penelitian</h2>
                    <p class="text-gray-600 mt-1">Isi judul, abstrak, kata kunci, jadwal, serta unggah PDF surat pengantar atau proposal.</p>
                </div>
            </li>
            <li class="flex gap-4">
                <div class="flex flex-col items-center">
                    <span class="h-10 w-10 rounded-full bg-orange-100 text-orange-600 font-semibold flex items-center justify-center">3</span>
                    <span class="flex-1 w-px bg-orange-100"></span>
                </div>
                <div>
                    <h2 class="text-lg font-semibold text-gray-900">Verifikasi Kesbangpol</h2>
                    <p class="text-gray-600 mt-1">Pastikan Surat Keterangan Penelitian terbit. Status Kesbangpol ditandai otomatis oleh admin.</p>
                </div>
            </li>
            <li class="flex gap-4">
                <div class="flex flex-col items-center">
                    <span class="h-10 w-10 rounded-full bg-orange-100 text-orange-600 font-semibold flex items-center justify-center">4</span>
                    <span class="flex-1 w-px bg-orange-100"></span>
                </div>
                <div>
                    <h2 class="text-lg font-semibold text-gray-900">Perbaikan & Komentar</h2>
                    <p class="text-gray-600 mt-1">Cek catatan admin di dashboard. Unggah revisi atau lengkapi data jika diminta.</p>
                </div>
            </li>
            <li class="flex gap-4">
                <div class="flex flex-col items-center">
                    <span class="h-10 w-10 rounded-full bg-orange-100 text-orange-600 font-semibold flex items-center justify-center">5</span>
                </div>
                <div>
                    <h2 class="text-lg font-semibold text-gray-900">Unggah Hasil & Publikasi</h2>
                    <p class="text-gray-600 mt-1">Unggah dokumen akhir. Setelah disetujui admin, penelitian tampil di katalog publik dan statistik.</p>
                </div>
            </li>
        </ol>
    </section>

    <section class="rounded-2xl border border-gray-100 bg-white/90 p-6 shadow-sm">
        <h2 class="text-lg font-semibold text-gray-900">Kontak Penting</h2>
        <div class="grid gap-4 sm:grid-cols-2 mt-4 text-sm text-gray-600">
            <div class="rounded-xl border border-gray-100 p-4">
                <p class="font-semibold text-gray-900">Helpdesk Bappeda</p>
                <p>Email: publikasi@bappeda.go.id</p>
                <p>Telepon: (0435) 123-456</p>
            </div>
            <div class="rounded-xl border border-gray-100 p-4">
                <p class="font-semibold text-gray-900">Kesbangpol</p>
                <p>Email: perizinan@kesbang.go.id</p>
                <p>Telepon: (0435) 222-890</p>
            </div>
        </div>
    </section>
@endsection
