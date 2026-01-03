<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-white">Training & Guides</h2>
    </x-slot>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <div class="font-semibold text-orc-navy mb-2">Getting Started</div>
                <ol class="list-decimal ml-6 text-sm text-gray-700 space-y-2">
                    <li>Upload a document (choose Document Type, Classification, and Files).</li>
                    <li>Understand classifications: you can only use those at or below your clearance level.</li>
                    <li>If your document type has an active workflow, it starts automatically.</li>
                </ol>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <div class="font-semibold text-orc-navy mb-3">Workflows (End-to-End)</div>
                @forelse($workflows as $wf)
                    <div class="mb-6">
                        <div class="text-sm font-medium text-orc-navy flex items-center justify-between">
                            <span>{{ $wf->name }}</span>
                            <span class="text-xs">
                                @if($wf->documentType)
                                    <a class="text-orc-teal hover:underline" href="{{ route('documents.create', ['type' => $wf->document_type_id, 'classification' => $publicClassificationId]) }}" title="Create a document using this workflow's type">Practice: Create with this type</a>
                                @endif
                            </span>
                        </div>
                        <div class="text-xs text-gray-500 mb-2">Key: {{ $wf->key }} · {{ optional($wf->documentType)->name ?: 'No type bound' }}</div>
                        <ol class="space-y-1 text-sm">
                            @forelse($wf->steps as $s)
                                <li class="flex items-center justify-between rounded border border-gray-200 bg-gray-50 px-3 py-2">
                                    <div>
                                        <div class="font-medium">{{ $s->position }}. {{ $s->name }}</div>
                                        <div class="text-xs text-gray-600">Assignee: {{ $s->assignee_display ?? (ucfirst($s->assignee_type).' ('.$s->assignee_value.')') }} · Requires Approval: {{ $s->requires_approval ? 'Yes' : 'No' }} · Allow Edit: {{ $s->allow_edit ? 'Yes' : 'No' }}</div>
                                    </div>
                                </li>
                            @empty
                                <li class="text-xs text-gray-500">No steps configured.</li>
                            @endforelse
                        </ol>
                    </div>
                @empty
                    <div class="text-sm text-gray-600">No workflows configured yet.</div>
                @endforelse
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <div class="font-semibold text-orc-navy mb-2">Practice Demo</div>
                <div class="text-sm text-gray-700">
                    Use the links above to create a practice document with the workflow's Document Type. Choose the <strong>Public</strong> classification if prompted to avoid clearance blocks. Then open the document and follow the Workflow card to simulate approvals. Only users matching the current step's assignee (role/unit/user/registrar) will see Approve/Reject.
                </div>
            </div>
        </div>

        <div class="space-y-6">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <div class="font-semibold text-orc-navy mb-2">User Levels</div>
                <div class="text-xs text-gray-500 mb-2">If your user list is large, filter below.</div>
                <input type="text" id="roleFilter" placeholder="Filter roles..." class="mb-2 w-full border-orc-navy/30 focus:border-orc-teal focus:ring-orc-teal rounded-md" />
                <ul id="roleList" class="text-sm text-gray-700 space-y-1 max-h-64 overflow-auto">
                    @foreach($roles as $r)
                        <li data-name="{{ strtolower($r->name.' '.$r->key) }}">• {{ $r->name }} <span class="text-xs text-gray-500">({{ $r->key }})</span></li>
                    @endforeach
                </ul>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <div class="font-semibold text-orc-navy mb-2">Organization Units</div>
                <input type="text" id="unitFilter" placeholder="Filter units..." class="mb-2 w-full border-orc-navy/30 focus:border-orc-teal focus:ring-orc-teal rounded-md" />
                <ul id="unitList" class="text-sm text-gray-700 space-y-1 max-h-64 overflow-auto">
                    @foreach($units as $u)
                        <li data-name="{{ strtolower($u->name.' '.($u->code ?? '')) }}">• {{ $u->name }} @if($u->code)<span class="text-xs text-gray-500">({{ $u->code }})</span>@endif</li>
                    @endforeach
                </ul>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <div class="font-semibold text-orc-navy mb-2">Tips</div>
                <ul class="text-sm text-gray-700 space-y-2">
                    <li>• Approve/Reject is only available to the assigned step actor (role/unit/user/registrar).</li>
                    <li>• Use the workflow tracker on the document page to see progress and next assignee.</li>
                    <li>• Classification access requires user clearance >= classification level.</li>
                </ul>
            </div>
        </div>
    </div>
    <script>
        // Simple client-side filter for roles and units
        (function(){
            function setupFilter(inputId, listId){
                const input = document.getElementById(inputId);
                const list = document.getElementById(listId);
                if (!input || !list) return;
                input.addEventListener('input', () => {
                    const q = input.value.trim().toLowerCase();
                    for (const li of list.children){
                        const name = li.getAttribute('data-name') || '';
                        li.style.display = name.includes(q) ? '' : 'none';
                    }
                });
            }
            setupFilter('roleFilter','roleList');
            setupFilter('unitFilter','unitList');
        })();
    </script>
</x-app-layout>
