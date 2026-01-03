<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl">Edit Classification</h2>
            <a href="{{ route('classifications.index') }}" class="text-sm text-orc-navy hover:underline">Back</a>
        </div>
    </x-slot>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 max-w-3xl">
        <form method="POST" action="{{ route('classifications.update', $classification) }}" class="space-y-5">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <x-input-label for="key" value="Key" />
                    <x-text-input id="key" name="key" type="text" class="mt-1 block w-full" value="{{ old('key', $classification->key) }}" required />
                    <x-input-error :messages="$errors->get('key')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="name" value="Name" />
                    <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" value="{{ old('name', $classification->name) }}" required />
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>
            </div>

            <div>
                <x-input-label for="description" value="Description" />
                <textarea id="description" name="description" rows="3" class="mt-1 block w-full border-orc-navy/30 focus:border-orc-teal focus:ring-orc-teal rounded-md">{{ old('description', $classification->description) }}</textarea>
                <x-input-error :messages="$errors->get('description')" class="mt-2" />
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <x-input-label for="clearance_level" value="Clearance Level" />
                    <x-text-input id="clearance_level" name="clearance_level" type="number" min="1" max="10" class="mt-1 block w-full" value="{{ old('clearance_level', $classification->clearance_level) }}" required />
                    <x-input-error :messages="$errors->get('clearance_level')" class="mt-2" />
                </div>
                <div class="flex items-end">
                    <label class="inline-flex items-center mt-6">
                        <input type="checkbox" name="is_active" value="1" class="rounded border-orc-navy/30 text-orc-teal shadow-sm focus:ring-orc-teal" {{ old('is_active', $classification->is_active) ? 'checked' : '' }}>
                        <span class="ml-2 text-sm text-gray-700">Active</span>
                    </label>
                </div>
            </div>

            <div class="flex items-center justify-end space-x-3">
                <x-secondary-button type="button" onclick="window.history.back()">Cancel</x-secondary-button>
                <x-primary-button>Update Classification</x-primary-button>
            </div>
        </form>
    </div>
</x-app-layout>
