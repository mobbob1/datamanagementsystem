<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-white">Edit Workflow</h2>
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
            <form method="POST" action="{{ route('workflows.update', $workflow) }}" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @csrf
                @method('PUT')

                <div>
                    <x-input-label value="Key" />
                    <x-text-input name="key" value="{{ old('key', $workflow->key) }}" class="w-full" required />
                </div>
                <div>
                    <x-input-label value="Name" />
                    <x-text-input name="name" value="{{ old('name', $workflow->name) }}" class="w-full" required />
                </div>
                <div class="md:col-span-2">
                    <x-input-label value="Description" />
                    <textarea name="description" class="w-full border-orc-navy/30 focus:border-orc-teal focus:ring-orc-teal rounded-md" rows="3">{{ old('description', $workflow->description) }}</textarea>
                </div>
                <div class="md:col-span-2">
                    <x-input-label value="Document Type (optional)" />
                    <select name="document_type_id" class="w-full border-orc-navy/30 focus:border-orc-teal focus:ring-orc-teal rounded-md">
                        <option value="">-- None --</option>
                        @foreach($types as $t)
                            <option value="{{ $t->id }}" @selected(old('document_type_id', $workflow->document_type_id) == $t->id)>{{ $t->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="md:col-span-2">
                    <label class="inline-flex items-center">
                        <input type="checkbox" name="is_active" value="1" class="rounded border-orc-navy/30 text-orc-teal shadow-sm focus:ring-orc-teal" {{ old('is_active', $workflow->is_active) ? 'checked' : '' }}>
                        <span class="ml-2 text-sm text-gray-700">Active</span>
                    </label>
                </div>

                <div class="md:col-span-2 flex justify-end gap-2">
                    <a href="{{ route('workflows.index') }}" class="inline-flex items-center px-4 py-2 rounded-md bg-gray-200 text-gray-800 text-xs uppercase tracking-widest">Back</a>
                    <x-primary-button>Save</x-primary-button>
                </div>
            </form>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-between mb-3">
                <div class="font-semibold text-orc-navy">Steps</div>
            </div>

            <ul class="divide-y divide-gray-100 mb-4">
                @forelse($workflow->steps as $s)
                    <li class="py-3 flex items-center justify-between">
                        <div>
                            <div class="font-medium">{{ $s->position }}. {{ $s->name }}</div>
                            <div class="text-xs text-gray-500">key: {{ $s->key }} · assignee: {{ $s->assignee_type }} {{ $s->assignee_value ? '(' . $s->assignee_value . ')' : '' }} · requires approval: {{ $s->requires_approval ? 'yes' : 'no' }} · allow edit: {{ $s->allow_edit ? 'yes' : 'no' }}</div>
                        </div>
                        <div class="flex items-center gap-2">
                            <form method="POST" action="{{ route('workflows.steps.up', [$workflow, $s]) }}">
                                @csrf
                                <button class="text-xs px-2 py-1 rounded bg-gray-200 hover:bg-gray-300">Up</button>
                            </form>
                            <form method="POST" action="{{ route('workflows.steps.down', [$workflow, $s]) }}">
                                @csrf
                                <button class="text-xs px-2 py-1 rounded bg-gray-200 hover:bg-gray-300">Down</button>
                            </form>
                            <form method="POST" action="{{ route('workflows.steps.destroy', [$workflow, $s]) }}">
                                @csrf
                                @method('DELETE')
                                <button class="text-xs px-2 py-1 rounded bg-red-100 text-red-700 hover:bg-red-200" onclick="return confirm('Remove this step?')">Delete</button>
                            </form>
                        </div>
                    </li>
                @empty
                    <li class="py-3 text-sm text-gray-500">No steps yet.</li>
                @endforelse
            </ul>

            <form method="POST" action="{{ route('workflows.steps.store', $workflow) }}" class="grid grid-cols-1 md:grid-cols-2 gap-3">
                @csrf
                <div>
                    <x-input-label value="Step Key" />
                    <x-text-input name="key" class="w-full" required />
                </div>
                <div>
                    <x-input-label value="Step Name" />
                    <x-text-input name="name" class="w-full" required />
                </div>
                <div>
                    <x-input-label value="Assignee Type" />
                    <select id="assignee_type" name="assignee_type" class="w-full border-orc-navy/30 focus:border-orc-teal focus:ring-orc-teal rounded-md">
                        @foreach(['role','unit','user','registrar'] as $t)
                            <option value="{{ $t }}">{{ ucfirst($t) }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <x-input-label value="Assignee" />
                    <input type="hidden" name="assignee_value" id="assignee_value" />
                    <select id="assignee_value_role" class="w-full border-orc-navy/30 focus:border-orc-teal focus:ring-orc-teal rounded-md">
                        @foreach($roles as $r)
                            <option value="{{ $r->id }}">{{ $r->name }} ({{ $r->key }})</option>
                        @endforeach
                    </select>
                    <select id="assignee_value_unit" class="mt-2 w-full border-orc-navy/30 focus:border-orc-teal focus:ring-orc-teal rounded-md" style="display:none">
                        @foreach($units as $u)
                            <option value="{{ $u->id }}">{{ $u->name }}{{ $u->code ? ' ('.$u->code.')' : '' }}</option>
                        @endforeach
                    </select>
                    <select id="assignee_value_user" class="mt-2 w-full border-orc-navy/30 focus:border-orc-teal focus:ring-orc-teal rounded-md" style="display:none">
                        @foreach($users as $u)
                            <option value="{{ $u->id }}">{{ $u->name }}{{ $u->email ? ' · '.$u->email : '' }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="inline-flex items-center">
                        <input type="checkbox" name="requires_approval" value="1" class="rounded border-orc-navy/30 text-orc-teal shadow-sm focus:ring-orc-teal" checked>
                        <span class="ml-2 text-sm text-gray-700">Requires Approval</span>
                    </label>
                </div>
                <div>
                    <label class="inline-flex items-center">
                        <input type="checkbox" name="allow_edit" value="1" class="rounded border-orc-navy/30 text-orc-teal shadow-sm focus:ring-orc-teal">
                        <span class="ml-2 text-sm text-gray-700">Allow Edit</span>
                    </label>
                </div>
                <div class="md:col-span-2 text-right">
                    <x-primary-button>Add Step</x-primary-button>
                </div>
            </form>
            <script>
                (function(){
                    const typeSel = document.getElementById('assignee_type');
                    const hidden = document.getElementById('assignee_value');
                    const selects = {
                        role: document.getElementById('assignee_value_role'),
                        unit: document.getElementById('assignee_value_unit'),
                        user: document.getElementById('assignee_value_user'),
                    };
                    function updateVisibility(){
                        const t = typeSel.value;
                        for (const k of ['role','unit','user']){
                            if (selects[k]){
                                selects[k].style.display = (k===t) ? '' : 'none';
                            }
                        }
                        if (t === 'registrar'){
                            hidden.value = '';
                            return;
                        }
                        const sel = selects[t];
                        if (sel){ hidden.value = sel.value; }
                    }
                    for (const k of ['role','unit','user']){
                        if (selects[k]){
                            selects[k].addEventListener('change', updateVisibility);
                        }
                    }
                    typeSel.addEventListener('change', updateVisibility);
                    updateVisibility();
                })();
            </script>
        </div>
    </div>
</x-app-layout>
