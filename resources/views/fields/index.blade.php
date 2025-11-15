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

                <form action="{{ route('fields.store') }}" method="POST" class="flex gap-3 items-end">
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
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            @forelse ($fields as $f)
                                <tr>
                                    <td class="px-4 py-2">{{ $f->name }}</td>
                                    <td class="px-4 py-2 text-sm text-gray-600">{{ $f->created_at?->format('Y-m-d H:i') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td class="px-4 py-6 text-center text-gray-500" colspan="2">Belum ada data.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

