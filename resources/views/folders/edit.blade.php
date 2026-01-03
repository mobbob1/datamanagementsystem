<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-white">Edit Folder</h2>
    </x-slot>

    @if (session('status'))
        <div class="mb-4 p-3 rounded-md bg-orc-teal/10 text-orc-teal text-sm">{{ session('status') }}</div>
    @endif

    @if ($errors->any())
        <div class="mb-4 p-3 rounded-md bg-red-50 text-red-700 text-sm">
            <ul class="list-disc ml-5">
                @foreach ($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <form method="POST" action="{{ route('folders.update', $folder) }}" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @csrf
                @method('PUT')

                <div>
                    <x-input-label value="Name" />
                    <x-text-input name="name" value="{{ old('name', $folder->name) }}" class="w-full" required />
                </div>
                <div>
                    <x-input-label value="Slug (optional)" />
                    <x-text-input name="slug" value="{{ old('slug', $folder->slug) }}" class="w-full" />
                </div>
                <div>
                    <x-input-label value="Parent Folder" />
                    <select name="parent_id" class="w-full border-orc-navy/30 focus:border-orc-teal focus:ring-orc-teal rounded-md">
                        <option value="">-- None --</option>
                        @foreach($parents as $p)
                            <option value="{{ $p->id }}" @selected(old('parent_id', $folder->parent_id) == $p->id)>{{ $p->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <x-input-label value="Organization Unit (optional)" />
                    <select name="organization_unit_id" class="w-full border-orc-navy/30 focus:border-orc-teal focus:ring-orc-teal rounded-md">
                        <option value="">-- None --</option>
                        @foreach($units as $u)
                            <option value="{{ $u->id }}" @selected(old('organization_unit_id', $folder->organization_unit_id) == $u->id)>{{ $u->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="md:col-span-2">
                    <x-input-label value="Description" />
                    <textarea name="description" class="w-full border-orc-navy/30 focus:border-orc-teal focus:ring-orc-teal rounded-md" rows="3">{{ old('description', $folder->description) }}</textarea>
                </div>

                <div class="md:col-span-2 flex justify-end gap-2">
                    <a href="{{ route('folders.index') }}" class="inline-flex items-center px-4 py-2 rounded-md bg-gray-200 text-gray-800 text-xs uppercase tracking-widest">Back</a>
                    <x-primary-button>Save</x-primary-button>
                </div>
            </form>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="font-semibold text-orc-navy mb-2">Permissions</div>

            <ul class="divide-y divide-gray-100 mb-4">
                @forelse($folder->permissions as $perm)
                    <li class="py-2 flex items-center justify-between">
                        <div>
                            <div class="text-sm">{{ optional($perm->user)->name }} <span class="text-xs text-gray-500">(ID: {{ $perm->user_id }})</span></div>
                            <div class="text-xs text-gray-500">view: {{ $perm->can_view ? 'yes' : 'no' }} · edit: {{ $perm->can_edit ? 'yes' : 'no' }}</div>
                        </div>
                        <form method="POST" action="{{ route('folders.permissions.remove', [$folder, $perm]) }}">
                            @csrf
                            @method('DELETE')
                            <button class="text-red-600 text-xs hover:underline">Remove</button>
                        </form>
                    </li>
                @empty
                    <li class="py-2 text-sm text-gray-500">No explicit permissions.</li>
                @endforelse
            </ul>

            <form method="POST" action="{{ route('folders.permissions.add', $folder) }}" class="grid grid-cols-1 md:grid-cols-4 gap-2 items-end">
                @csrf
                <div class="md:col-span-2">
                    <x-input-label value="User ID" />
                    <x-text-input name="user_id" type="number" min="1" class="w-full" required />
                </div>
                <label class="inline-flex items-center">
                    <input type="checkbox" name="can_view" value="1" class="rounded border-orc-navy/30 text-orc-teal shadow-sm focus:ring-orc-teal" checked>
                    <span class="ml-2 text-sm text-gray-700">View</span>
                </label>
                <label class="inline-flex items-center">
                    <input type="checkbox" name="can_edit" value="1" class="rounded border-orc-navy/30 text-orc-teal shadow-sm focus:ring-orc-teal">
                    <span class="ml-2 text-sm text-gray-700">Edit</span>
                </label>
                <div class="md:col-span-4 text-right">
                    <x-primary-button>Add/Update Permission</x-primary-button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
