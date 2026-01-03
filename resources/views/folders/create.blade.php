<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-white">Create Folder</h2>
    </x-slot>

    @if ($errors->any())
        <div class="mb-4 p-3 rounded-md bg-red-50 text-red-700 text-sm">
            <ul class="list-disc ml-5">
                @foreach ($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <form method="POST" action="{{ route('folders.store') }}" class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @csrf

            <div>
                <x-input-label value="Name" />
                <x-text-input name="name" value="{{ old('name') }}" class="w-full" required />
            </div>
            <div>
                <x-input-label value="Slug (optional)" />
                <x-text-input name="slug" value="{{ old('slug') }}" class="w-full" />
            </div>
            <div>
                <x-input-label value="Parent Folder" />
                <select name="parent_id" class="w-full border-orc-navy/30 focus:border-orc-teal focus:ring-orc-teal rounded-md">
                    <option value="">-- None --</option>
                    @foreach($parents as $p)
                        <option value="{{ $p->id }}" @selected(old('parent_id') == $p->id)>{{ $p->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <x-input-label value="Organization Unit (optional)" />
                <select name="organization_unit_id" class="w-full border-orc-navy/30 focus:border-orc-teal focus:ring-orc-teal rounded-md">
                    <option value="">-- None --</option>
                    @foreach($units as $u)
                        <option value="{{ $u->id }}" @selected(old('organization_unit_id') == $u->id)>{{ $u->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="md:col-span-2">
                <x-input-label value="Description" />
                <textarea name="description" class="w-full border-orc-navy/30 focus:border-orc-teal focus:ring-orc-teal rounded-md" rows="3">{{ old('description') }}</textarea>
            </div>

            <div class="md:col-span-2 flex justify-end gap-2">
                <a href="{{ route('folders.index') }}" class="inline-flex items-center px-4 py-2 rounded-md bg-gray-200 text-gray-800 text-xs uppercase tracking-widest">Cancel</a>
                <x-primary-button>Create</x-primary-button>
            </div>
        </form>
    </div>
</x-app-layout>
