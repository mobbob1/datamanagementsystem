<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl">Classifications</h2>
            <a href="{{ route('classifications.create') }}" class="inline-flex items-center px-3 py-2 rounded-md bg-orc-teal text-white text-sm hover:opacity-90">Add Classification</a>
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
                        <th class="px-4 py-3 text-left font-medium text-gray-600">Key</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-600">Name</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-600">Clearance</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-600">Status</th>
                        <th class="px-4 py-3 text-right font-medium text-gray-600">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($classifications as $cl)
                        <tr>
                            <td class="px-4 py-3">{{ $cl->key }}</td>
                            <td class="px-4 py-3">{{ $cl->name }}</td>
                            <td class="px-4 py-3">Level {{ $cl->clearance_level }}</td>
                            <td class="px-4 py-3">
                                <span class="text-xs px-2 py-1 rounded-full {{ $cl->is_active ? 'bg-orc-teal/10 text-orc-teal' : 'bg-gray-200 text-gray-700' }}">{{ $cl->is_active ? 'Active' : 'Inactive' }}</span>
                            </td>
                            <td class="px-4 py-3 text-right space-x-2">
                                <a href="{{ route('classifications.edit', $cl) }}" class="text-orc-navy hover:underline">Edit</a>
                                <form action="{{ route('classifications.destroy', $cl) }}" method="POST" class="inline" onsubmit="return confirm('Delete this classification?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:underline">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td class="px-4 py-6 text-center text-gray-500" colspan="5">No records found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3">{{ $classifications->links() }}</div>
    </div>
</x-app-layout>
