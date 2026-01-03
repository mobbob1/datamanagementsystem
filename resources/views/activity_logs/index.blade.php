<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-white">Activity Logs</h2>
    </x-slot>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 mb-4">
        <form method="GET" class="grid grid-cols-1 lg:grid-cols-7 gap-3 text-sm">
            <input type="text" name="q" placeholder="Search action/subject/ip/agent" value="{{ request('q') }}" class="w-full border-orc-navy/30 focus:border-orc-teal focus:ring-orc-teal rounded-md" />
            <select name="action" class="w-full border-orc-navy/30 focus:border-orc-teal focus:ring-orc-teal rounded-md">
                <option value="">All Actions</option>
                @foreach($distinctActions as $a)
                    <option value="{{ $a }}" @selected(request('action') == $a)>{{ $a }}</option>
                @endforeach
            </select>
            <input type="text" name="user" placeholder="User ID" value="{{ request('user') }}" class="w-full border-orc-navy/30 focus:border-orc-teal focus:ring-orc-teal rounded-md" />
            <input type="text" name="document" placeholder="Document ID" value="{{ request('document') }}" class="w-full border-orc-navy/30 focus:border-orc-teal focus:ring-orc-teal rounded-md" />
            <input type="date" name="from" value="{{ request('from') }}" class="w-full border-orc-navy/30 focus:border-orc-teal focus:ring-orc-teal rounded-md" />
            <input type="date" name="to" value="{{ request('to') }}" class="w-full border-orc-navy/30 focus:border-orc-teal focus:ring-orc-teal rounded-md" />
            <div class="flex items-center gap-2">
                <x-secondary-button type="submit">Filter</x-secondary-button>
                <a href="{{ route('activity-logs.index') }}" class="inline-flex items-center px-4 py-2 rounded-md bg-gray-200 text-gray-800 text-xs uppercase tracking-widest">Reset</a>
                <a href="{{ route('activity-logs.export', request()->query()) }}" class="inline-flex items-center px-4 py-2 rounded-md bg-orc-teal/10 text-orc-teal text-xs uppercase tracking-widest">Export CSV</a>
            </div>
        </form>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left font-medium text-gray-600">Time</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-600">User</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-600">Action</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-600">Subject</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-600">IP</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-600">User Agent</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @forelse($logs as $log)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3">{{ $log->created_at->format('Y-m-d H:i:s') }}</td>
                            <td class="px-4 py-3">{{ optional($log->user)->name ?: 'System' }} ({{ $log->user_id }})</td>
                            <td class="px-4 py-3">{{ $log->action }}</td>
                            <td class="px-4 py-3">{{ class_basename($log->subject_type) }} #{{ $log->subject_id }}</td>
                            <td class="px-4 py-3">{{ $log->ip_address }}</td>
                            <td class="px-4 py-3 truncate max-w-xs" title="{{ $log->user_agent }}">{{ \Illuminate\Support\Str::limit($log->user_agent, 60) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-6 text-center text-gray-500">No logs found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-3 border-t border-gray-100">{{ $logs->links() }}</div>
    </div>
</x-app-layout>
