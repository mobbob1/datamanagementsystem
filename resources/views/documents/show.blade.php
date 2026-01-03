<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <div class="text-xs text-gray-500">Doc No: {{ $document->doc_number }}</div>
                <h2 class="font-semibold text-xl">{{ $document->title }}</h2>
            </div>
            <div class="flex items-center space-x-2">
                @can('manageLegalHold', $document)
                    <form method="POST" action="{{ route('documents.legal-hold.toggle', $document) }}">
                        @csrf
                        <x-secondary-button type="submit">{{ $document->legal_hold ? 'Disable Legal Hold' : 'Enable Legal Hold' }}</x-secondary-button>
                    </form>
                @endcan
                @if($document->locked_by)
                    <form method="POST" action="{{ route('documents.unlock', $document) }}">
                        @csrf
                        <x-primary-button type="submit">Unlock</x-primary-button>
                    </form>
                @else
                    <form method="POST" action="{{ route('documents.lock', $document) }}">
                        @csrf
                        <x-secondary-button type="submit">Lock</x-secondary-button>
                    </form>
                @endif
                <a href="{{ route('documents.index') }}" class="text-sm text-orc-navy hover:underline">Back</a>
            </div>
        </div>
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

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <!-- Current File Preview Links -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                <div class="p-4 border-b border-gray-100 flex items-center justify-between">
                    <div class="font-semibold text-orc-navy">Files & Versions</div>
                </div>
                <div class="p-4">
                    <ul class="divide-y divide-gray-100">
                        @foreach ($document->files as $f)
                            <li class="py-3 flex items-center justify-between">
                                <div>
                                    <div class="font-medium">v{{ $f->version }} · {{ $f->original_name }}</div>
                                    <div class="text-xs text-gray-500">{{ number_format($f->size/1024,1) }} KB · {{ $f->mime }}</div>
                                </div>
                                <div class="space-x-3">
                                    <a href="{{ route('files.preview', $f) }}" target="_blank" class="text-orc-teal hover:underline">Preview</a>
                                    <a href="{{ route('files.download', $f) }}" class="text-orc-gold hover:underline">Download</a>
                                    @if($f->is_current)
                                        <span class="text-xs px-2 py-1 rounded-full bg-orc-teal/10 text-orc-teal">Current</span>
                                    @endif
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>

            <!-- Upload New Version -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
                <div class="font-semibold text-orc-navy mb-2">Upload New Version</div>
                <form method="POST" action="{{ route('documents.version.upload', $document) }}" enctype="multipart/form-data" class="flex items-center space-x-3">
                    @csrf
                    <input name="file" type="file" class="border-orc-navy/30 focus:border-orc-teal focus:ring-orc-teal rounded-md" required />
                    <x-primary-button>Upload</x-primary-button>
                </form>
            </div>

            <!-- Workflow -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
                <div class="font-semibold text-orc-navy mb-2">Workflow</div>
                @if($workflowInstance)
                    <div class="text-sm mb-3">
                        <div class="text-xs text-gray-500">Status</div>
                        <div><span class="text-xs px-2 py-1 rounded-full {{ $workflowInstance->status === 'running' ? 'bg-amber-100 text-amber-700' : ($workflowInstance->status === 'completed' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700') }}">{{ ucfirst($workflowInstance->status) }}</span></div>
                        @if($workflowInstance->currentStep)
                            <div class="mt-2 text-xs text-gray-500">Current Step</div>
                            <div class="text-sm">{{ $workflowInstance->currentStep->name }}</div>
                        @endif
                    </div>
                    @if(isset($workflowSteps) && $workflowSteps->count())
                        <div class="mb-3">
                            <div class="text-xs text-gray-500 mb-1">Steps</div>
                            <ol class="space-y-2">
                                @foreach($workflowSteps as $s)
                                    <li class="flex items-center justify-between rounded-md border px-3 py-2 {{ $s['status']==='current' ? 'border-amber-300 bg-amber-50' : ($s['status']==='done' ? 'border-green-300 bg-green-50' : 'border-gray-200 bg-gray-50') }}">
                                        <div>
                                            <div class="font-medium text-sm">{{ $s['position'] }}. {{ $s['name'] }}</div>
                                            <div class="text-xs text-gray-600">Assignee: {{ $s['assignee'] }}</div>
                                        </div>
                                        <div>
                                            @if($s['status']==='done')
                                                <span class="text-[10px] px-2 py-1 rounded-full bg-green-100 text-green-700">Done</span>
                                            @elseif($s['status']==='current')
                                                <span class="text-[10px] px-2 py-1 rounded-full bg-amber-100 text-amber-700">Current</span>
                                            @elseif($s['status']==='canceled')
                                                <span class="text-[10px] px-2 py-1 rounded-full bg-red-100 text-red-700">Canceled</span>
                                            @else
                                                <span class="text-[10px] px-2 py-1 rounded-full bg-gray-200 text-gray-700">Pending</span>
                                            @endif
                                        </div>
                                    </li>
                                @endforeach
                            </ol>
                        </div>
                    @endif
                    @if($workflowInstance->status === 'running')
                        @if(!empty($canActOnStep))
                            <div class="flex items-center gap-2">
                                <form method="POST" action="{{ route('documents.workflow.approve', $document) }}">
                                    @csrf
                                    <x-primary-button>Approve</x-primary-button>
                                </form>
                                <form method="POST" action="{{ route('documents.workflow.reject', $document) }}">
                                    @csrf
                                    <x-secondary-button>Reject</x-secondary-button>
                                </form>
                            </div>
                        @else
                            <div class="text-xs text-gray-500">You are not assigned to act on this step.</div>
                        @endif
                    @endif
                @else
                    <div class="text-sm text-gray-600">No workflow instance for this document.</div>
                @endif
            </div>
        </div>

        <!-- Metadata Card -->
        <div class="space-y-6">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
                <div class="text-xs text-gray-500">Status</div>
                <div class="mt-1 text-sm"><span class="text-xs px-2 py-1 rounded-full {{ $document->status === 'draft' ? 'bg-gray-200 text-gray-700' : 'bg-orc-teal/10 text-orc-teal' }}">{{ ucfirst($document->status) }}</span></div>
                <div class="mt-4 text-xs text-gray-500">Type</div>
                <div class="text-sm">{{ optional($document->type)->name }}</div>
                <div class="mt-4 text-xs text-gray-500">Classification</div>
                <div class="text-sm">{{ optional($document->classification)->name }}</div>
                <div class="mt-4 text-xs text-gray-500">Origin Unit</div>
                <div class="text-sm">{{ optional($document->originUnit)->name ?: '-' }}</div>
                <div class="mt-4 text-xs text-gray-500">Legal Hold</div>
                <div class="text-sm">{{ $document->legal_hold ? 'Yes' : 'No' }}</div>
                <div class="mt-4 text-xs text-gray-500">Retention Until</div>
                <div class="text-sm">{{ $document->retention_until ? $document->retention_until->format('Y-m-d') : '-' }}</div>
                <div class="mt-4 text-xs text-gray-500">Locked By</div>
                <div class="text-sm">{{ optional($document->locker)->name ?: '-' }}</div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
                <div class="font-semibold text-orc-navy mb-2">Permissions</div>
                <ul class="divide-y divide-gray-100 mb-3">
                    @forelse($document->permissions as $perm)
                        <li class="py-2 flex items-center justify-between">
                            <div>
                                <div class="text-sm">{{ optional($perm->user)->name }} <span class="text-xs text-gray-500">(ID: {{ $perm->user_id }})</span></div>
                                <div class="text-xs text-gray-500">view: {{ $perm->can_view ? 'yes' : 'no' }} · edit: {{ $perm->can_edit ? 'yes' : 'no' }}</div>
                            </div>
                            @can('update', $document)
                            <form method="POST" action="{{ route('documents.permissions.remove', [$document, $perm]) }}">
                                @csrf
                                @method('DELETE')
                                <button class="text-red-600 text-xs hover:underline">Remove</button>
                            </form>
                            @endcan
                        </li>
                    @empty
                        <li class="py-2 text-sm text-gray-500">No explicit permissions.</li>
                    @endforelse
                </ul>
                @can('update', $document)
                <form method="POST" action="{{ route('documents.permissions.add', $document) }}" class="grid grid-cols-1 sm:grid-cols-4 gap-2 items-end">
                    @csrf
                    <div class="sm:col-span-2">
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
                    <div class="sm:col-span-4 text-right">
                        <x-primary-button>Add/Update Permission</x-primary-button>
                    </div>
                </form>
                @endcan
            </div>

            <!-- Actions -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 space-y-4">
                <div class="font-semibold text-orc-navy">Actions</div>
                @can('update', $document)
                <form method="POST" action="{{ route('documents.rename', $document) }}" class="flex items-end gap-2">
                    @csrf
                    <div class="flex-1">
                        <x-input-label value="Rename Title" />
                        <x-text-input name="title" value="{{ old('title', $document->title) }}" class="w-full" required />
                    </div>
                    <x-secondary-button type="submit">Rename</x-secondary-button>
                </form>

                <form method="POST" action="{{ route('documents.move', $document) }}" class="grid grid-cols-1 gap-2">
                    @csrf
                    <div>
                        <x-input-label value="Change Document Type" />
                        <select name="document_type_id" class="w-full border-orc-navy/30 focus:border-orc-teal focus:ring-orc-teal rounded-md">
                            <option value="">-- keep --</option>
                            @foreach($types as $t)
                                <option value="{{ $t->id }}">{{ $t->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <x-input-label value="Change Classification" />
                        <select name="classification_id" class="w-full border-orc-navy/30 focus:border-orc-teal focus:ring-orc-teal rounded-md">
                            <option value="">-- keep --</option>
                            @foreach($classifications as $c)
                                <option value="{{ $c->id }}">{{ $c->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <x-input-label value="Change Origin Unit" />
                        <select name="origin_unit_id" class="w-full border-orc-navy/30 focus:border-orc-teal focus:ring-orc-teal rounded-md">
                            <option value="">-- keep --</option>
                            @foreach($units as $u)
                                <option value="{{ $u->id }}">{{ $u->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <x-input-label value="Move to Folder" />
                        <select name="folder_id" class="w-full border-orc-navy/30 focus:border-orc-teal focus:ring-orc-teal rounded-md">
                            <option value="">-- keep --</option>
                            @foreach($folders as $f)
                                <option value="{{ $f->id }}">{{ $f->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="text-right">
                        <x-primary-button>Apply Changes</x-primary-button>
                    </div>
                </form>

                <form method="POST" action="{{ route('documents.copy', $document) }}">
                    @csrf
                    <x-secondary-button>Make a Copy</x-secondary-button>
                </form>
                @endcan

                @can('delete', $document)
                <form method="POST" action="{{ route('documents.destroy', $document) }}" onsubmit="return confirm('Delete this document? This cannot be undone.')">
                    @csrf
                    @method('DELETE')
                    <button class="inline-flex items-center px-3 py-2 rounded-md bg-red-100 text-red-700 text-xs uppercase tracking-widest hover:bg-red-200">Delete Document</button>
                </form>
                @endcan
            </div>
        </div>
    </div>
</x-app-layout>
