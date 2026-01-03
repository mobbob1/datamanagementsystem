<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl">Bulk Upload</h2>
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
        <form method="POST" action="{{ route('documents.bulk.store') }}" enctype="multipart/form-data" x-data="bulkUpload()" class="space-y-5">
            @csrf

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
                <x-input-label for="title" value="Default Title (optional)" />
                <x-text-input id="title" name="title" type="text" class="mt-1 block w-full" value="{{ old('title') }}" placeholder="If empty, filenames will be used" />
                <x-input-error :messages="$errors->get('title')" class="mt-2" />
            </div>

            <div>
                <x-input-label value="Files" />
                <div class="mt-1 border-2 border-dashed rounded-md p-6 text-center border-orc-navy/30" :class="{'bg-orc-teal/5': dragging}" @dragover.prevent="dragging=true" @dragleave.prevent="dragging=false" @drop.prevent="handleDrop($event)">
                    <p class="text-sm text-gray-600">Drag and drop files here, or click to select</p>
                    <input type="file" name="files[]" multiple class="hidden" x-ref="file" @change="updateList" />
                    <div class="mt-3">
                        <x-secondary-button type="button" @click="$refs.file.click()">Choose Files</x-secondary-button>
                    </div>
                    <template x-if="files.length">
                        <ul class="mt-4 text-left text-sm space-y-1">
                            <template x-for="f in files" :key="f.name">
                                <li class="flex items-center justify-between">
                                    <span x-text="f.name"></span>
                                    <span class="text-xs text-gray-500" x-text="(f.size/1024).toFixed(1)+' KB'"></span>
                                </li>
                            </template>
                        </ul>
                    </template>
                </div>
                <x-input-error :messages="$errors->get('files')" class="mt-2" />
            </div>

            <div class="flex items-center justify-end space-x-3">
                <x-secondary-button type="button" onclick="window.history.back()">Cancel</x-secondary-button>
                <x-primary-button>Start Upload</x-primary-button>
            </div>
        </form>
    </div>

    <script>
        function bulkUpload(){
            return {
                dragging: false,
                files: [],
                handleDrop(e){
                    this.dragging=false;
                    const dt = e.dataTransfer;
                    if (!dt || !dt.files) return;
                    this.$refs.file.files = dt.files;
                    this.updateList();
                },
                updateList(){
                    this.files = Array.from(this.$refs.file.files || []);
                }
            }
        }
    </script>
</x-app-layout>
