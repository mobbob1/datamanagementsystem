<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl">Add Organization Unit</h2>
            <a href="{{ route('organization-units.index') }}" class="text-sm text-orc-navy hover:underline">Back</a>
        </div>
    </x-slot>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 max-w-3xl">
        <form method="POST" action="{{ route('organization-units.store') }}" class="space-y-5">
            @csrf

            <div>
                <x-input-label for="name" value="Name" />
                <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" value="{{ old('name') }}" required />
                <x-input-error :messages="$errors->get('name')" class="mt-2" />
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <x-input-label for="code" value="Code" />
                    <x-text-input id="code" name="code" type="text" class="mt-1 block w-full" value="{{ old('code') }}" />
                    <x-input-error :messages="$errors->get('code')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="type" value="Type" />
                    <select id="type" name="type" class="mt-1 block w-full border-orc-navy/30 focus:border-orc-teal focus:ring-orc-teal rounded-md">
                        @php($types = ['directorate' => 'Directorate','department' => 'Department','unit' => 'Unit'])
                        @foreach ($types as $k => $v)
                            <option value="{{ $k }}" {{ old('type','department') === $k ? 'selected' : '' }}>{{ $v }}</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('type')" class="mt-2" />
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <x-input-label for="parent_id" value="Parent Unit (optional)" />
                    <select id="parent_id" name="parent_id" class="mt-1 block w-full border-orc-navy/30 focus:border-orc-teal focus:ring-orc-teal rounded-md">
                        <option value="">-- None --</option>
                        @foreach ($parents as $p)
                            <option value="{{ $p->id }}" {{ old('parent_id') == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('parent_id')" class="mt-2" />
                </div>
                <div class="flex items-end">
                    <label class="inline-flex items-center mt-6">
                        <input type="checkbox" name="is_active" value="1" class="rounded border-orc-navy/30 text-orc-teal shadow-sm focus:ring-orc-teal" {{ old('is_active',1) ? 'checked' : '' }}>
                        <span class="ml-2 text-sm text-gray-700">Active</span>
                    </label>
                </div>
            </div>

            <div class="flex items-center justify-end space-x-3">
                <x-secondary-button type="button" onclick="window.history.back()">Cancel</x-secondary-button>
                <x-primary-button>Save Unit</x-primary-button>
            </div>
        </form>
    </div>
</x-app-layout>
