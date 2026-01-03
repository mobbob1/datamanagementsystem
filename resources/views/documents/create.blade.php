<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl">Upload Document</h2>
            <a href="{{ route('documents.index') }}" class="text-sm text-orc-navy hover:underline">Back to list</a>
        </div>
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

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 max-w-4xl">
        <form method="POST" action="{{ route('documents.store') }}" enctype="multipart/form-data" class="space-y-5">
            @csrf
            <div>
                <x-input-label for="title" value="Title" />
                <x-text-input id="title" name="title" type="text" class="mt-1 block w-full" value="{{ old('title') }}" required />
                <x-input-error :messages="$errors->get('title')" class="mt-2" />
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <x-input-label for="document_type_id" value="Document Type" />
                    <select id="document_type_id" name="document_type_id" class="mt-1 block w-full border-orc-navy/30 focus:border-orc-teal focus:ring-orc-teal rounded-md" required>
                        <option value="">-- Select --</option>
                        @foreach ($types as $t)
                            <option value="{{ $t->id }}" {{ old('document_type_id') == $t->id ? 'selected' : '' }}>{{ $t->name }}</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('document_type_id')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="classification_id" value="Classification" />
                    <select id="classification_id" name="classification_id" class="mt-1 block w-full border-orc-navy/30 focus:border-orc-teal focus:ring-orc-teal rounded-md" required>
                        <option value="">-- Select --</option>
                        @foreach ($classifications as $c)
                            <option value="{{ $c->id }}" {{ old('classification_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('classification_id')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="origin_unit_id" value="Origin Unit (optional)" />
                    <select id="origin_unit_id" name="origin_unit_id" class="mt-1 block w-full border-orc-navy/30 focus:border-orc-teal focus:ring-orc-teal rounded-md">
                        <option value="">-- None --</option>
                        @foreach ($units as $u)
                            <option value="{{ $u->id }}" {{ old('origin_unit_id') == $u->id ? 'selected' : '' }}>{{ $u->name }}</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('origin_unit_id')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="folder_id" value="Folder (optional)" />
                    <select id="folder_id" name="folder_id" class="mt-1 block w-full border-orc-navy/30 focus:border-orc-teal focus:ring-orc-teal rounded-md">
                        <option value="">-- None --</option>
                        @foreach (\App\Models\Folder::orderBy('name')->get() as $f)
                            <option value="{{ $f->id }}" {{ old('folder_id') == $f->id ? 'selected' : '' }}>{{ $f->name }}</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('folder_id')" class="mt-2" />
                </div>
            </div>

            <div>
                <x-input-label for="files" value="Files" />
                <input id="files" name="files[]" type="file" multiple class="mt-1 block w-full border-orc-navy/30 focus:border-orc-teal focus:ring-orc-teal rounded-md" required />
                <p class="mt-1 text-xs text-gray-500">PDF, Word, Excel, images. Max 20MB each.</p>
                <x-input-error :messages="$errors->get('files')" class="mt-2" />
            </div>

            <div class="flex items-center justify-end space-x-3">
                <x-secondary-button type="button" onclick="window.history.back()">Cancel</x-secondary-button>
                <x-primary-button>Upload</x-primary-button>
            </div>
        </form>
    </div>
</x-app-layout>
