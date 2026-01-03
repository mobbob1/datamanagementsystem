<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-white">Users & Roles</h2>
    </x-slot>

    @if (session('status'))
        <div class="mb-4 p-3 rounded-md bg-orc-teal/10 text-orc-teal text-sm">{{ session('status') }}</div>
    @endif

    <div class="mb-4 flex justify-end">
        <a href="{{ route('users.create') }}" class="inline-flex items-center px-4 py-2 rounded-md bg-orc-teal text-white text-xs uppercase tracking-widest">Create User</a>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left font-medium text-gray-600">Name</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-600">Email</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-600">Role</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-600">Unit</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-600">Status</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-600">Clearance</th>
                        <th class="px-4 py-3 text-right font-medium text-gray-600">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @forelse($users as $u)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3">{{ $u->name }}</td>
                            <td class="px-4 py-3 text-gray-600">{{ $u->email }}</td>
                            <td class="px-4 py-3">{{ optional($u->role)->name ?: '-' }}</td>
                            <td class="px-4 py-3">{{ optional($u->organizationUnit)->name ?: '-' }}</td>
                            <td class="px-4 py-3">{{ ucfirst($u->status) }}</td>
                            <td class="px-4 py-3">{{ $u->clearance_level }}</td>
                            <td class="px-4 py-3 text-right">
                                <a href="{{ route('users.edit', $u) }}" class="text-orc-teal hover:underline">Edit</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-6 text-center text-gray-500">No users found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-3 border-t border-gray-100">{{ $users->links() }}</div>
    </div>
</x-app-layout>
