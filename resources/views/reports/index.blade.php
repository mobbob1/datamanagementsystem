<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-white">Reports & Analytics</h2>
    </x-slot>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="text-sm text-gray-500">Total Documents</div>
            <div class="text-3xl font-bold text-orc-navy mt-1">{{ number_format($stats['documents_total']) }}</div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="text-sm text-gray-500">Archived Documents</div>
            <div class="text-3xl font-bold text-orc-navy mt-1">{{ number_format($stats['documents_archived']) }}</div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="text-sm text-gray-500">Disposed Documents</div>
            <div class="text-3xl font-bold text-orc-navy mt-1">{{ number_format($stats['documents_disposed']) }}</div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="text-sm text-gray-500">Documents on Legal Hold</div>
            <div class="text-3xl font-bold text-orc-navy mt-1">{{ number_format($stats['documents_legal_hold']) }}</div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="text-sm text-gray-500">Documents (Last 7 Days)</div>
            <div class="text-3xl font-bold text-orc-navy mt-1">{{ number_format($stats['documents_last_7_days']) }}</div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="text-sm text-gray-500">Workflows Running</div>
            <div class="text-3xl font-bold text-orc-navy mt-1">{{ number_format($stats['workflows_running']) }}</div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="text-sm text-gray-500">Workflows Completed</div>
            <div class="text-3xl font-bold text-orc-navy mt-1">{{ number_format($stats['workflows_completed']) }}</div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 mb-4">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-3 items-end">
            <div>
                <label class="text-xs text-gray-600">Report Type</label>
                <select name="type" class="w-full border-orc-navy/30 focus:border-orc-teal focus:ring-orc-teal rounded-md">
                    <option value="documents" @selected($type==='documents')>Documents</option>
                    <option value="workflows" @selected($type==='workflows')>Workflows</option>
                    <option value="activity_logs" @selected($type==='activity_logs')>Activity Logs</option>
                </select>
            </div>
            <div>
                <label class="text-xs text-gray-600">From</label>
                <input type="date" name="from" value="{{ $from }}" class="w-full border-orc-navy/30 focus:border-orc-teal focus:ring-orc-teal rounded-md" />
            </div>
            <div>
                <label class="text-xs text-gray-600">To</label>
                <input type="date" name="to" value="{{ $to }}" class="w-full border-orc-navy/30 focus:border-orc-teal focus:ring-orc-teal rounded-md" />
            </div>
            <div class="flex gap-2">
                <x-secondary-button type="submit">View</x-secondary-button>
                <a href="{{ route('reports.index') }}" class="inline-flex items-center px-4 py-2 rounded-md bg-gray-200 text-gray-800 text-xs uppercase tracking-widest">Reset</a>
            </div>
            <div class="text-right">
                <a href="{{ route('reports.export', ['type' => $type, 'from' => $from, 'to' => $to]) }}" class="inline-flex items-center px-4 py-2 rounded-md bg-orc-teal text-white text-xs uppercase tracking-widest">Export CSV</a>
            </div>
        </form>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        @foreach ($columns as $col)
                            <th class="px-4 py-3 text-left font-medium text-gray-600">{{ $col }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @forelse ($rows as $r)
                        <tr class="hover:bg-gray-50">
                            @foreach ($r as $cell)
                                <td class="px-4 py-3">{{ $cell }}</td>
                            @endforeach
                        </tr>
                    @empty
                        <tr>
                            <td class="px-4 py-6 text-center text-gray-500" colspan="{{ count($columns) }}">No data found for selection.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
