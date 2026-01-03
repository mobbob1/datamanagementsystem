<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center space-x-2 text-white">
            <h2 class="font-semibold text-xl">Folders</h2>
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

    <!-- Breadcrumbs -->
    <nav class="mb-4 text-sm text-gray-600">
        <ol class="flex items-center space-x-1">
            <li>
                <a href="{{ route('folders.browse') }}" class="text-orc-teal hover:underline">Home</a>
            </li>
            @foreach($breadcrumbs as $crumb)
                <li><span class="text-gray-400">/</span></li>
                <li>
                    @if(!$loop->last)
                        <a href="{{ route('folders.browse', $crumb) }}" class="text-orc-teal hover:underline">{{ $crumb->name }}</a>
                    @else
                        <span class="text-gray-700">{{ $crumb->name }}</span>
                    @endif
                </li>
            @endforeach
        </ol>
    </nav>

    <!-- Search -->
    <form method="GET" action="{{ $current ? route('folders.browse', $current) : route('folders.browse') }}" class="mb-4 flex items-center gap-2">
        <input type="text" name="q" value="{{ $q ?? '' }}" placeholder="Search folders or documents" class="w-full border-orc-navy/30 focus:border-orc-teal focus:ring-orc-teal rounded-md" />
        @if(!empty($q))
            <a href="{{ $current ? route('folders.browse', $current) : route('folders.browse') }}" class="inline-flex items-center px-3 py-2 rounded-md bg-gray-200 text-gray-800 text-xs uppercase tracking-widest">Clear</a>
        @endif
        <x-secondary-button type="submit">Search</x-secondary-button>
    </form>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Folders column -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                <div class="p-4 border-b border-gray-100 font-semibold text-orc-navy">Folders</div>
                <div class="p-2">
                    <ul class="divide-y divide-gray-100">
                        @forelse($folders as $f)
                            @php($childCount = $f->children()->count())
                            @php($docCount = $f->documents()->count())
                            @php($lastUpdated = $f->documents()->max('updated_at'))
                            <li>
                                <a href="{{ route('folders.browse', $f) }}" class="flex items-center justify-between px-3 py-3 hover:bg-gray-50">
                                    <div>
                                        <div class="font-medium">{{ $f->name }}</div>
                                        <div class="text-xs text-gray-500">{{ $childCount }} subfolder{{ $childCount === 1 ? '' : 's' }} · {{ $docCount }} document{{ $docCount === 1 ? '' : 's' }}</div>
                                        <div class="text-[11px] text-gray-400">Last updated: {{ $lastUpdated ? date('Y-m-d H:i', strtotime($lastUpdated)) : '—' }}</div>
                                    </div>
                                    <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                </a>
                            </li>
                        @empty
                            <li class="px-3 py-4 text-sm text-gray-500">No folders available.</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>

        <!-- Documents column -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                <div class="p-4 border-b border-gray-100 font-semibold text-orc-navy flex items-center justify-between">
                    <div>Documents @if($current) <span class="text-gray-500 font-normal">in {{ $current->name }}</span> @endif</div>
                    @if(!empty($current) && !empty($canUpload))
                        <a href="{{ route('documents.create', ['folder_id' => $current->id]) }}" class="inline-flex items-center px-3 py-2 rounded-md bg-orc-teal text-white text-xs uppercase tracking-widest">Upload to this folder</a>
                    @endif
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left font-medium text-gray-600">Doc No.</th>
                                <th class="px-4 py-3 text-left font-medium text-gray-600">Title</th>
                                <th class="px-4 py-3 text-left font-medium text-gray-600">Type</th>
                                <th class="px-4 py-3 text-left font-medium text-gray-600">Classification</th>
                                <th class="px-4 py-3 text-left font-medium text-gray-600">Status</th>
                                <th class="px-4 py-3 text-left font-medium text-gray-600">Created</th>
                                <th class="px-4 py-3"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white">
                            @forelse($documents as $d)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3">{{ $d->doc_number }}</td>
                                    <td class="px-4 py-3">{{ $d->title }}</td>
                                    <td class="px-4 py-3">{{ optional($d->type)->name }}</td>
                                    <td class="px-4 py-3">{{ optional($d->classification)->name }}</td>
                                    <td class="px-4 py-3">
                                        <span class="text-xs px-2 py-1 rounded-full {{ $d->status === 'draft' ? 'bg-gray-200 text-gray-700' : 'bg-orc-teal/10 text-orc-teal' }}">{{ ucfirst($d->status) }}</span>
                                    </td>
                                    <td class="px-4 py-3">{{ optional($d->created_at)->format('Y-m-d') }}</td>
                                    <td class="px-4 py-3 text-right">
                                        <a href="{{ route('documents.show', $d) }}" class="text-orc-teal hover:underline">View</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td class="px-4 py-6 text-center text-gray-500" colspan="7">No documents in this folder.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
