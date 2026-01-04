<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl">Documents</h2>
            <div class="space-x-2">
                <a href="{{ route('documents.create') }}" class="inline-flex items-center px-3 py-2 rounded-md bg-orc-teal text-white text-sm hover:opacity-90">Upload</a>
                <a href="{{ route('documents.bulk.create') }}" class="inline-flex items-center px-3 py-2 rounded-md bg-orc-navy text-white text-sm hover:opacity-90">Bulk Upload</a>
            </div>

    <script>
    document.addEventListener('DOMContentLoaded', function(){
        const qInput = document.getElementById('q');
        const box = document.getElementById('suggestions');
        let timer=null;
        function hide(){ box.classList.add('hidden'); box.innerHTML=''; }
        function show(items){
            if(!items || items.length===0){ hide(); return; }
            box.innerHTML = items.map(i=>`<a href="/documents/${i.id}" class="block px-3 py-2 hover:bg-gray-50"><span class="font-mono text-xs text-gray-600">${i.doc_number}</span> <span class="ml-2">${i.title}</span></a>`).join('');
            box.classList.remove('hidden');
        }
        qInput.addEventListener('input', function(){
            const v = this.value.trim();
            if (timer) clearTimeout(timer);
            if (v.length < 2) { hide(); return; }
            timer = setTimeout(()=>{
                fetch(`{{ route('documents.suggest') }}?q=`+encodeURIComponent(v))
                  .then(r=>r.json())
                  .then(show)
                  .catch(()=>hide());
            }, 200);
        });
        document.addEventListener('click', function(e){ if(!box.contains(e.target) && e.target!==qInput){ hide(); }});
    });
    </script>
        </div>
    </x-slot>

    @if (session('status'))
        <div class="mb-4 p-3 rounded-md bg-orc-teal/10 text-orc-teal text-sm">{{ session('status') }}</div>
    @endif

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 mb-6">
        <form method="GET" class="grid grid-cols-1 lg:grid-cols-7 gap-3">
            <div class="lg:col-span-2">
                <div class="relative">
                    <x-text-input id="q" name="q" placeholder="Search title or document number" value="{{ request('q') }}" class="w-full" autocomplete="off" />
                    <div id="suggestions" class="absolute z-10 mt-1 bg-white border border-gray-200 rounded-md shadow-sm w-full hidden"></div>
                </div>
                <div class="mt-1 text-xs"><a href="{{ route('scan') }}" class="text-orc-teal hover:underline">Scan QR/Barcode</a></div>
            </div>
            <div>
                <select name="type" class="w-full border-orc-navy/30 focus:border-orc-teal focus:ring-orc-teal rounded-md">
                    <option value="">All Types</option>
                    @foreach ($types as $t)
                        <option value="{{ $t->id }}" {{ request('type') == $t->id ? 'selected' : '' }}>{{ $t->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <select name="classification" class="w-full border-orc-navy/30 focus:border-orc-teal focus:ring-orc-teal rounded-md">
                    <option value="">All Classifications</option>
                    @foreach ($classifications as $c)
                        <option value="{{ $c->id }}" {{ request('classification') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <select name="unit" class="w-full border-orc-navy/30 focus:border-orc-teal focus:ring-orc-teal rounded-md">
                    <option value="">All Units</option>
                    @foreach ($units as $u)
                        <option value="{{ $u->id }}" {{ request('unit') == $u->id ? 'selected' : '' }}>{{ $u->name }}</option>
                    @endforeach
                </select>
            </div>
            @isset($folders)
            <div>
                <select name="folder" class="w-full border-orc-navy/30 focus:border-orc-teal focus:ring-orc-teal rounded-md">
                    <option value="">All Folders</option>
                    @foreach ($folders as $f)
                        <option value="{{ $f->id }}" {{ request('folder') == $f->id ? 'selected' : '' }}>{{ $f->name }}</option>
                    @endforeach
                </select>
            </div>
            @endisset
            <div class="flex space-x-2">
                <input type="date" name="from" value="{{ request('from') }}" class="w-full border-orc-navy/30 focus:border-orc-teal focus:ring-orc-teal rounded-md" />
                <input type="date" name="to" value="{{ request('to') }}" class="w-full border-orc-navy/30 focus:border-orc-teal focus:ring-orc-teal rounded-md" />
            </div>
            <div class="lg:col-span-6 flex justify-end space-x-2">
                <x-secondary-button type="submit">Filter</x-secondary-button>
                <a href="{{ request()->routeIs('archive.index') ? route('archive.index') : route('documents.index') }}" class="inline-flex items-center px-4 py-2 rounded-md bg-gray-200 text-gray-800 text-xs uppercase tracking-widest">Reset</a>
            </div>
        </form>
    </div>

    @if(isset($savedSearches) && $savedSearches->count())
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-3 mb-4 flex items-center flex-wrap gap-2">
        <div class="text-xs text-gray-500 mr-2">Saved searches:</div>
        @foreach($savedSearches as $s)
            <a href="{{ route('documents.index', ['saved' => $s->id]) }}" class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-orc-teal/10 text-orc-teal hover:bg-orc-teal/20">{{ $s->name }}</a>
        @endforeach
    </div>
    @endif

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-3 mb-6">
        <form method="POST" action="{{ route('documents.search.save') }}" class="flex items-center justify-end gap-2">
            @csrf
            <input type="hidden" name="q" value="{{ request('q') }}" />
            <input type="hidden" name="status" value="{{ request('status') }}" />
            <input type="hidden" name="type" value="{{ request('type') }}" />
            <input type="hidden" name="classification" value="{{ request('classification') }}" />
            <input type="hidden" name="unit" value="{{ request('unit') }}" />
            <input type="hidden" name="folder" value="{{ request('folder') }}" />
            <input type="hidden" name="owner" value="{{ request('owner') }}" />
            <input type="hidden" name="from" value="{{ request('from') }}" />
            <input type="hidden" name="to" value="{{ request('to') }}" />
            <x-text-input name="name" placeholder="Save search as..." class="w-64" />
            <x-primary-button>Save Search</x-primary-button>
        </form>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left font-medium text-gray-600">Doc No.</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-600">Title</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-600">Type</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-600">Classification</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-600">Unit</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-600">Status</th>
                        <th class="px-4 py-3 text-right font-medium text-gray-600">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($documents as $d)
                        <tr>
                            <td class="px-4 py-3 font-mono">{{ $d->doc_number }}</td>
                            <td class="px-4 py-3">{{ $d->title }}</td>
                            <td class="px-4 py-3">{{ optional($d->type)->name }}</td>
                            <td class="px-4 py-3">{{ optional($d->classification)->name }}</td>
                            <td class="px-4 py-3">{{ optional($d->originUnit)->name }}</td>
                            <td class="px-4 py-3">
                                <span class="text-xs px-2 py-1 rounded-full {{ $d->status === 'draft' ? 'bg-gray-200 text-gray-700' : 'bg-orc-teal/10 text-orc-teal' }}">{{ ucfirst($d->status) }}</span>
                            </td>
                            <td class="px-4 py-3 text-right space-x-3">
                                <a href="{{ route('documents.show', $d) }}" class="text-orc-navy hover:underline">View</a>
                                @php($current = $d->files->first())
                                @if($current)
                                    <a href="{{ route('files.preview', $current) }}" target="_blank" class="text-orc-teal hover:underline">Preview</a>
                                    <a href="{{ route('files.download', $current) }}" class="text-orc-gold hover:underline">Download</a>
                                @endif
                                @if(auth()->user() && method_exists(auth()->user(), 'isAdmin') && auth()->user()->isAdmin())
                                    @if(!$d->archived_at && !$d->disposed_at)
                                        <form method="POST" action="{{ route('documents.archive', $d) }}" class="inline">
                                            @csrf
                                            <button type="submit" class="text-red-600 hover:underline" onclick="return confirm('Archive this document?');">Archive</button>
                                        </form>
                                    @elseif($d->archived_at)
                                        <form method="POST" action="{{ route('documents.unarchive', $d) }}" class="inline">
                                            @csrf
                                            <button type="submit" class="text-gray-600 hover:underline" onclick="return confirm('Unarchive this document?');">Unarchive</button>
                                        </form>
                                    @endif
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td class="px-4 py-6 text-center text-gray-500" colspan="7">No documents found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3">{{ $documents->links() }}</div>
    </div>
</x-app-layout>
