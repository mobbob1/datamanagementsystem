<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center space-x-3">
            <div class="h-6 w-6 rounded-full bg-white/20 flex items-center justify-center">
                <span class="w-2 h-2 bg-orc-gold rounded-full"></span>
            </div>
            <h2 class="font-semibold text-xl">{{ __('Dashboard') }}</h2>
        </div>
    </x-slot>

    <div class="space-y-8">
        <!-- Quick Stats -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                <div class="text-xs text-gray-500">Documents In Intake</div>
                <div class="mt-2 text-2xl font-semibold text-orc-navy">{{ number_format($stats['intake'] ?? 0) }}</div>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                <div class="text-xs text-gray-500">Awaiting Approval</div>
                <div class="mt-2 text-2xl font-semibold text-orc-gold">{{ number_format($stats['awaiting'] ?? 0) }}</div>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                <div class="text-xs text-gray-500">Archived</div>
                <div class="mt-2 text-2xl font-semibold text-orc-teal">{{ number_format($stats['archived'] ?? 0) }}</div>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                <div class="text-xs text-gray-500">Overdue Actions</div>
                <div class="mt-2 text-2xl font-semibold text-red-500">{{ number_format($stats['overdue'] ?? 0) }}</div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <div class="text-sm font-semibold text-orc-navy">Start New</div>
                <div class="mt-4 grid grid-cols-2 gap-3">
                    <a href="#" class="rounded-lg border border-gray-200 p-4 hover:border-orc-teal hover:shadow-sm transition">
                        <div class="text-sm">New Document</div>
                        <div class="text-xs text-gray-500">Create from type</div>
                    </a>
                    <a href="#" class="rounded-lg border border-gray-200 p-4 hover:border-orc-teal hover:shadow-sm transition">
                        <div class="text-sm">Upload File</div>
                        <div class="text-xs text-gray-500">Attach to record</div>
                    </a>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <div class="text-sm font-semibold text-orc-navy">My Tasks</div>
                <ul class="mt-4 space-y-3">
                    <li class="flex items-center justify-between text-sm">
                        <span class="text-gray-700">Approvals pending</span>
                        <span class="text-xs bg-orc-teal/10 text-orc-teal px-2 py-1 rounded-full">0</span>
                    </li>
                    <li class="flex items-center justify-between text-sm">
                        <span class="text-gray-700">Reviews assigned</span>
                        <span class="text-xs bg-orc-gold/10 text-orc-gold px-2 py-1 rounded-full">0</span>
                    </li>
                </ul>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <div class="text-sm font-semibold text-orc-navy">Shortcuts</div>
                <div class="mt-4 grid grid-cols-2 gap-3">
                    <a href="#" class="text-xs px-3 py-2 rounded-md bg-orc-navy text-white text-center hover:opacity-90">Document Types</a>
                    <a href="#" class="text-xs px-3 py-2 rounded-md bg-orc-teal text-white text-center hover:opacity-90">Templates</a>
                    <a href="#" class="text-xs px-3 py-2 rounded-md bg-orc-gold text-white text-center hover:opacity-90">Workflows</a>
                    <a href="#" class="text-xs px-3 py-2 rounded-md bg-gray-800 text-white text-center hover:opacity-90">Classifications</a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
