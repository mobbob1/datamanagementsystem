<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-white">Templates</h2>
            @can('admin')
            <a href="{{ route('templates.create') }}" class="inline-flex items-center px-3 py-2 rounded-md bg-white/20 text-white text-xs uppercase tracking-widest hover:bg-white/30">Create Template</a>
            @endcan
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
                        <th class="px-4 py-3 text-left font-medium text-gray-600">Key</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-600">Document Type</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-600">Active</th>
                        <th class="px-4 py-3 text-right font-medium text-gray-600">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @forelse($templates as $t)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3">{{ $t->name }}</td>
                            <td class="px-4 py-3 text-gray-600">{{ $t->key }}</td>
                            <td class="px-4 py-3">{{ optional($t->documentType)->name ?: '-' }}</td>
                            <td class="px-4 py-3">
                                <span class="text-xs px-2 py-1 rounded-full {{ $t->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-200 text-gray-700' }}">{{ $t->is_active ? 'Active' : 'Inactive' }}</span>
                            </td>
                            <td class="px-4 py-3 text-right">
                                @can('admin')
                                <a href="{{ route('templates.edit', $t) }}" class="text-orc-teal hover:underline mr-3">Edit</a>
                                <form method="POST" action="{{ route('templates.destroy', $t) }}" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button class="text-red-600 hover:underline" onclick="return confirm('Delete this template?')">Delete</button>
                                </form>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-6 text-center text-gray-500">No templates found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-3 border-t border-gray-100">{{ $templates->links() }}</div>
    </div>
</x-app-layout>
