<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-white">Edit Document Type</h2>
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
        <form method="POST" action="{{ route('document-types.update', $type) }}" class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @csrf
            @method('PUT')

            <div>
                <x-input-label value="Key" />
                <x-text-input name="key" value="{{ old('key', $type->key) }}" class="w-full" required />
            </div>
            <div>
                <x-input-label value="Name" />
                <x-text-input name="name" value="{{ old('name', $type->name) }}" class="w-full" required />
            </div>
            <div class="md:col-span-2">
                <x-input-label value="Description" />
                <textarea name="description" class="w-full border-orc-navy/30 focus:border-orc-teal focus:ring-orc-teal rounded-md" rows="3">{{ old('description', $type->description) }}</textarea>
            </div>
            <div>
                <x-input-label value="Default Classification" />
                <select name="default_classification_id" class="w-full border-orc-navy/30 focus:border-orc-teal focus:ring-orc-teal rounded-md">
                    <option value="">-- None --</option>
                    @foreach($classifications as $c)
                        <option value="{{ $c->id }}" @selected(old('default_classification_id', $type->default_classification_id) == $c->id)>{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <x-input-label value="Default Retention (months)" />
                <x-text-input type="number" name="default_retention_months" min="0" value="{{ old('default_retention_months', $type->default_retention_months) }}" class="w-full" />
            </div>
            <div class="md:col-span-2">
                <label class="inline-flex items-center">
                    <input type="checkbox" name="is_active" value="1" class="rounded border-orc-navy/30 text-orc-teal shadow-sm focus:ring-orc-teal" {{ old('is_active', $type->is_active) ? 'checked' : '' }}>
                    <span class="ml-2 text-sm text-gray-700">Active</span>
                </label>
            </div>

            <div class="md:col-span-2 flex justify-end gap-2">
                <a href="{{ route('document-types.index') }}" class="inline-flex items-center px-4 py-2 rounded-md bg-gray-200 text-gray-800 text-xs uppercase tracking-widest">Cancel</a>
                <x-primary-button>Update</x-primary-button>
            </div>
        </form>
    </div>
</x-app-layout>
