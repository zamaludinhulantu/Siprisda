<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3 w-full">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.3em] text-orange-500">Riset Saya</p>
                <h2 class="text-2xl font-semibold text-gray-900">{{ __('Daftar Penelitian') }}</h2>
                <p class="text-sm text-gray-500">Pantau status pengajuan dan buka detail untuk memperbarui informasi.</p>
            </div>
            <a href="{{ route('researches.create') }}" class="inline-flex items-center gap-2 rounded-lg bg-gray-900 px-4 py-2 text-sm font-semibold text-white hover:bg-gray-800">
                <i class="fas fa-plus text-xs"></i> Unggah Penelitian
            </a>
        </div>
    </x-slot>

    @php
        $isPaginator = method_exists($researches, 'firstItem');
        $startNumber = $isPaginator ? $researches->firstItem() : 1;
        $endNumber = $isPaginator ? $researches->lastItem() : $researches->count();
        $total = method_exists($researches, 'total') ? $researches->total() : $researches->count();
    @endphp

    <div class="space-y-6">
        <section class="rounded-2xl border border-gray-100 bg-white/95 backdrop-blur shadow-sm">
            <div class="flex flex-wrap items-center justify-between gap-3 px-6 py-4 border-b border-gray-100">
                <div>
                    <p class="text-xs uppercase font-semibold tracking-wide text-gray-500">Total Pengajuan</p>
                    <p class="text-sm text-gray-500">Menampilkan {{ $startNumber }}-{{ $endNumber }} dari {{ $total }} penelitian</p>
                </div>
                <div class="inline-flex items-center gap-2 rounded-full bg-orange-50 px-3 py-1 text-xs font-semibold text-orange-700">
                    <span class="h-2 w-2 rounded-full bg-orange-500"></span>
                    Status realtime
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100 text-sm">
                    <thead class="bg-gray-50 text-xs font-semibold uppercase tracking-wide text-gray-500">
                        <tr>
                            <th class="px-6 py-3 text-left">Judul</th>
                            <th class="px-6 py-3 text-left">Bidang</th>
                            <th class="px-6 py-3 text-left">Institusi</th>
                            <th class="px-6 py-3 text-left">Status</th>
                            <th class="px-6 py-3 text-left">Diajukan</th>
                            <th class="px-6 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white">
                        @forelse ($researches as $research)
                            @php
                                $status = (string)($research->status ?? 'draft');
                                $statusMap = [
                                    'approved' => ['label' => 'Disetujui', 'class' => 'bg-emerald-50 text-emerald-700'],
                                    'rejected' => ['label' => 'Ditolak', 'class' => 'bg-rose-50 text-rose-700'],
                                    'submitted' => ['label' => 'Diajukan', 'class' => 'bg-amber-50 text-amber-700'],
                                    'kesbang_verified' => ['label' => 'Disetujui Kesbang', 'class' => 'bg-cyan-50 text-cyan-700'],
                                    'default' => ['label' => 'Draft', 'class' => 'bg-gray-50 text-gray-600'],
                                ];
                                $statusInfo = $statusMap[$status] ?? $statusMap['default'];
                            @endphp
                            <tr class="hover:bg-orange-50/30 transition">
                                <td class="px-6 py-4">
                                    <p class="font-semibold text-gray-900 line-clamp-2">{{ $research->title }}</p>
                                    <p class="text-xs text-gray-500 mt-1">Penulis: {{ $research->author ?? '-' }}</p>
                                </td>
                                <td class="px-6 py-4 text-gray-700">{{ optional($research->field)->name ?? '-' }}</td>
                                <td class="px-6 py-4 text-gray-700">{{ optional($research->institution)->name ?? '-' }}</td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold {{ $statusInfo['class'] }}">
                                        {{ $statusInfo['label'] }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-gray-600">
                                    {{ optional($research->created_at)->format('d M Y') ?? '-' }}
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <a href="{{ route('researches.show', $research->id) }}" class="inline-flex items-center gap-1 rounded-lg border border-gray-200 px-3 py-1.5 text-xs font-semibold text-gray-700 hover:bg-white">
                                        <i class="fas fa-eye text-[11px]"></i> Detail
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-6 text-center text-gray-500 text-sm">Belum ada penelitian. Unggah penelitian pertama Anda.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-4">
                {{ $researches->links() }}
            </div>
        </section>
    </div>
</x-app-layout>
