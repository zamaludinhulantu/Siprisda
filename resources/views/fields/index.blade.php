<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Kelola Bidang Penelitian') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-6 rounded shadow">
                @if ($errors->any())
                    <div class="mb-4 p-3 rounded bg-red-50 text-red-700">
                        <ul class="list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                @if (session('success'))
                    <div class="mb-4 p-3 rounded bg-emerald-50 text-emerald-800">
                        {{ session('success') }}
                    </div>
                @endif
                @if (session('error'))
                    <div class="mb-4 p-3 rounded bg-rose-50 text-rose-800">
                        {{ session('error') }}
                    </div>
                @endif

                <form action="{{ route('fields.store') }}" method="POST" class="flex flex-wrap gap-3 items-end">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Nama Bidang</label>
                        <input type="text" name="name" class="border-gray-300 rounded w-72" placeholder="mis. Teknologi" required>
                    </div>
                    <button type="submit" class="inline-flex items-center rounded-md bg-gray-900 px-4 py-2 text-white hover:bg-gray-800">Tambah</button>
                </form>
            </div>

            <div class="bg-white p-6 rounded shadow mt-6">
                <h3 class="font-semibold mb-3">Daftar Bidang</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full table-auto">
                        <thead>
                            <tr class="bg-gray-100 text-sm">
                                <th class="px-4 py-2 text-left">Nama</th>
                                <th class="px-4 py-2 text-left">Dibuat</th>
                                <th class="px-4 py-2 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            @forelse ($fields as $f)
                                <tr>
                                    <td class="px-4 py-2">
                                        <form action="{{ route('fields.update', $f) }}" method="POST" class="flex items-center gap-2">
                                            @csrf
                                            @method('PATCH')
                                            <input type="text" name="name" value="{{ $f->name }}" class="border-gray-200 rounded w-full max-w-xs focus:border-orange-500 focus:ring-orange-500">
                                            <button type="submit" class="inline-flex items-center rounded bg-emerald-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-emerald-500">Simpan</button>
                                        </form>
                                    </td>
                                    <td class="px-4 py-2 text-sm text-gray-600">{{ $f->created_at?->format('Y-m-d H:i') }}</td>
                                    <td class="px-4 py-2 text-right">
                                        <form action="{{ route('fields.destroy', $f) }}" method="POST" onsubmit="return confirm('Hapus bidang ini?');" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="inline-flex items-center rounded border border-rose-200 px-3 py-1.5 text-xs font-semibold text-rose-700 hover:bg-rose-50">
                                                Hapus
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td class="px-4 py-6 text-center text-gray-500" colspan="3">Belum ada data.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
