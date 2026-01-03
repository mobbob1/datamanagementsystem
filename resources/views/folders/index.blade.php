<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-white">Folders</h2>
            <a href="{{ route('folders.create') }}" class="inline-flex items-center px-3 py-2 rounded-md bg-white/20 text-white text-xs uppercase tracking-widest hover:bg-white/30">Create Folder</a>
        </div>
    </x-slot>

    @if (session('status'))
        <div class="mb-4 p-3 rounded-md bg-orc-teal/10 text-orc-teal text-sm">{{ session('status') }}</div>
    @endif

    <div class="bg-white rounded-xl shadow-sm border border-gray-100">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left font-medium text-gray-600">Name</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-600">Parent</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-600">Unit</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-600">Description</th>
                        <th class="px-4 py-3 text-right font-medium text-gray-600">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @forelse($folders as $f)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3">{{ $f->name }}</td>
                            <td class="px-4 py-3">{{ optional($f->parent)->name ?: '-' }}</td>
                            <td class="px-4 py-3">{{ optional($f->unit)->name ?: '-' }}</td>
                            <td class="px-4 py-3 text-gray-600">{{ \Illuminate\Support\Str::limit($f->description, 60) }}</td>
                            <td class="px-4 py-3 text-right">
                                <a href="{{ route('folders.edit', $f) }}" class="text-orc-teal hover:underline mr-3">Edit</a>
                                <form method="POST" action="{{ route('folders.destroy', $f) }}" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button class="text-red-600 hover:underline" onclick="return confirm('Delete this folder?')">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-6 text-center text-gray-500">No folders found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-3 border-t border-gray-100">{{ $folders->links() }}</div>
    </div>
</x-app-layout>
