<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3 w-full">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.3em] text-orange-500">Modul Admin</p>
                <h1 class="text-2xl font-semibold text-gray-900">Data Penelitian</h1>
                <p class="text-sm text-gray-500">Kelola pengajuan penelitian dan pastikan informasi publik konsisten.</p>
            </div>
            <a href="{{ route('researches.create') }}" class="inline-flex items-center gap-2 rounded-lg bg-gray-900 px-4 py-2 text-sm font-semibold text-white hover:bg-gray-800">
                <i class="fas fa-upload text-xs"></i> Unggah Baru
            </a>
        </div>
    </x-slot>

    <div class="space-y-6">
        <section class="rounded-2xl border border-orange-100 bg-white/90 p-6 shadow-sm">
            <form method="GET" action="{{ route('admin.researches.index') }}" class="grid gap-4 md:grid-cols-4">
                <div class="md:col-span-2">
                    <label for="q" class="text-sm font-medium text-gray-700">Cari Judul / Peneliti</label>
                    <input type="text" id="q" name="q" value="{{ request('q') }}" placeholder="Contoh: Ketahanan pangan"
                           class="mt-1 w-full rounded-lg border-gray-200 focus:border-orange-500 focus:ring-orange-500">
                </div>
                <div>
                    <label for="status" class="text-sm font-medium text-gray-700">Status</label>
                    <select id="status" name="status" class="mt-1 w-full rounded-lg border-gray-200 focus:border-orange-500 focus:ring-orange-500">
                        <option value="">Semua</option>
                        @foreach(['submitted' => 'Diajukan', 'approved' => 'Disetujui', 'rejected' => 'Ditolak'] as $value => $label)
                            <option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-end gap-2">
                    <button type="submit" class="flex-1 inline-flex items-center justify-center rounded-lg bg-gray-900 px-4 py-2 text-sm font-semibold text-white hover:bg-gray-800">Terapkan</button>
                    @if(request()->hasAny(['q','status']))
                        <a href="{{ route('admin.researches.index') }}" class="inline-flex items-center justify-center rounded-lg border border-gray-200 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">Reset</a>
                    @endif
                </div>
            </form>
        </section>

        @php
            $isPaginator = method_exists($researches, 'firstItem');
            $startNumber = $isPaginator ? $researches->firstItem() : 1;
            $endNumber = $isPaginator ? $researches->lastItem() : $researches->count();
            $total = method_exists($researches, 'total') ? $researches->total() : $researches->count();
        @endphp
        <section class="rounded-2xl border border-gray-100 bg-white/95 backdrop-blur p-0 shadow">
            <div class="flex flex-wrap items-center justify-between gap-3 px-6 py-4 border-b border-gray-100">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Daftar Penelitian</p>
                    <p class="text-sm text-gray-500">Menampilkan {{ $startNumber }}-{{ $endNumber }} dari {{ $total }} entri</p>
                </div>
                <div class="inline-flex items-center gap-2 rounded-full bg-orange-50 px-3 py-1 text-xs font-semibold text-orange-700">
                    <span class="h-2 w-2 rounded-full bg-orange-500"></span>
                    Sinkron real-time
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100 text-sm">
                    <thead class="bg-gray-50 text-xs font-semibold uppercase tracking-wide text-gray-500">
                        <tr>
                            <th class="px-6 py-3 text-left">Judul</th>
                            <th class="px-6 py-3 text-left">Peneliti</th>
                            <th class="px-6 py-3 text-left">Bidang</th>
                            <th class="px-6 py-3 text-left">Status</th>
                            <th class="px-6 py-3 text-left">Tanggal</th>
                            <th class="px-6 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white">
                        @forelse($researches as $research)
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
                            <tr class="hover:bg-orange-50/40 transition">
                                <td class="px-6 py-4">
                                    <p class="font-semibold text-gray-900 line-clamp-2">{{ $research->title ?? $research->judul ?? '-' }}</p>
                                    <p class="text-xs text-gray-500 mt-1">ID #{{ $research->id }}</p>
                                </td>
                                <td class="px-6 py-4 text-gray-700">{{ $research->author ?? optional($research->user)->name ?? '-' }}</td>
                                <td class="px-6 py-4 text-gray-700">{{ optional($research->field)->name ?? '-' }}</td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold {{ $statusInfo['class'] }}">
                                        {{ $statusInfo['label'] }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-gray-600">
                                    {{ optional($research->created_at)->format('d M Y') ?? '-' }}
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <a href="{{ route('admin.researches.show', $research) }}" class="inline-flex items-center gap-1 rounded-lg border border-gray-200 px-3 py-1.5 text-xs font-semibold text-gray-700 hover:bg-gray-50">
                                        <i class="fas fa-eye text-[11px]"></i> Detail
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-6 text-center text-sm text-gray-500">Belum ada data yang memenuhi filter.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        <div>
            {{ $researches->links() }}
        </div>
    </div>
</x-app-layout>
