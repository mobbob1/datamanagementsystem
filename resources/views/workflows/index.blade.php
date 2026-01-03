<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-white">Workflows</h2>
            <a href="{{ route('workflows.create') }}" class="inline-flex items-center px-3 py-2 rounded-md bg-white/20 text-white text-xs uppercase tracking-widest hover:bg-white/30">Create Workflow</a>
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
                        <th class="px-4 py-3 text-left font-medium text-gray-600">Steps</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-600">Active</th>
                        <th class="px-4 py-3 text-right font-medium text-gray-600">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @forelse($defs as $w)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3">{{ $w->name }}</td>
                            <td class="px-4 py-3 text-gray-600">{{ $w->key }}</td>
                            <td class="px-4 py-3">{{ optional($w->documentType)->name ?: '-' }}</td>
                            <td class="px-4 py-3">{{ $w->steps_count }}</td>
                            <td class="px-4 py-3"><span class="text-xs px-2 py-1 rounded-full {{ $w->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-200 text-gray-700' }}">{{ $w->is_active ? 'Active' : 'Inactive' }}</span></td>
                            <td class="px-4 py-3 text-right">
                                <a href="{{ route('workflows.edit', $w) }}" class="text-orc-teal hover:underline mr-3">Edit</a>
                                <form method="POST" action="{{ route('workflows.destroy', $w) }}" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button class="text-red-600 hover:underline" onclick="return confirm('Delete this workflow?')">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-6 text-center text-gray-500">No workflows defined.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-3 border-t border-gray-100">{{ $defs->links() }}</div>
    </div>
</x-app-layout>
