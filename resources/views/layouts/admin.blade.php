<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'ORC DMS') }}</title>

        <!-- Assets (Mix) -->
        <link rel="stylesheet" href="{{ mix('css/app.css') }}">
        <script src="{{ mix('js/app.js') }}" defer></script>
    </head>
    <body class="font-sans antialiased bg-gray-50">
        <div class="min-h-screen flex">
            <!-- Sidebar -->
            <aside class="hidden md:flex md:flex-col md:w-64 bg-orc-navy text-white">
                <div class="h-20 flex items-center px-6 border-b border-white/10">
                    <img src="{{ asset('images/orc-logo.png') }}" alt="ORC" class="h-10 w-10 rounded-full bg-white p-1 mr-3">
                    <div class="leading-tight">
                        <div class="text-[10px] uppercase tracking-wider text-orc-teal/90">Office of the Registrar of Companies</div>
                        <div class="font-semibold text-sm">DMS Admin</div>
                    </div>
                </div>
                <nav class="flex-1 overflow-y-auto py-4">
                    <a href="{{ route('dashboard') }}" class="flex items-center px-6 py-3 text-sm hover:bg-white/10 {{ request()->routeIs('dashboard') ? 'bg-white/10' : '' }}">
                        <span class="w-2 h-2 rounded-full bg-orc-teal mr-3"></span>
                        Dashboard
                    </a>
                    <div class="mt-4 px-6 text-[10px] uppercase tracking-wider text-white/60">Documents</div>
                    <a href="{{ route('documents.index') }}" class="flex items-center px-6 py-3 text-sm hover:bg-white/10 {{ request()->is('documents') ? 'bg-white/10' : '' }}">
                        <span class="w-2 h-2 rounded-full bg-white/30 mr-3"></span>
                        Browse
                    </a>
                    <a href="{{ route('documents.pending') }}" class="flex items-center px-6 py-3 text-sm hover:bg-white/10 {{ request()->is('documents-pending') ? 'bg-white/10' : '' }}">
                        <span class="w-2 h-2 rounded-full bg-white/30 mr-3"></span>
                        Pending / Awaiting Approval
                    </a>
                    <a href="{{ route('documents.create') }}" class="flex items-center px-6 py-3 text-sm hover:bg-white/10 {{ request()->is('documents/create') ? 'bg-white/10' : '' }}">
                        <span class="w-2 h-2 rounded-full bg-white/30 mr-3"></span>
                        Upload
                    </a>
                    <a href="{{ route('documents.bulk.create') }}" class="flex items-center px-6 py-3 text-sm hover:bg-white/10 {{ request()->is('documents-bulk') ? 'bg-white/10' : '' }}">
                        <span class="w-2 h-2 rounded-full bg-white/30 mr-3"></span>
                        Bulk Upload
                    </a>
                    <a href="{{ route('folders.browse') }}" class="flex items-center px-6 py-3 text-sm hover:bg-white/10 {{ request()->is('folders/browse*') ? 'bg-white/10' : '' }}">
                        <span class="w-2 h-2 rounded-full bg-white/30 mr-3"></span>
                        Folders
                    </a>
                    <a href="{{ route('scan') }}" class="flex items-center px-6 py-3 text-sm hover:bg-white/10 {{ request()->is('scan') ? 'bg-white/10' : '' }}">
                        <span class="w-2 h-2 rounded-full bg-white/30 mr-3"></span>
                        Scan
                    </a>
                    <a href="{{ route('archive.index') }}" class="flex items-center px-6 py-3 text-sm hover:bg-white/10 {{ request()->is('archive') ? 'bg-white/10' : '' }}">
                        <span class="w-2 h-2 rounded-full bg-white/30 mr-3"></span>
                        Archive
                    </a>
                    <a href="{{ route('reports.index') }}" class="flex items-center px-6 py-3 text-sm hover:bg-white/10 {{ request()->is('reports') ? 'bg-white/10' : '' }}">
                        <span class="w-2 h-2 rounded-full bg-white/30 mr-3"></span>
                        Reports & Analytics
                    </a>
                    <a href="{{ route('training.index') }}" class="flex items-center px-6 py-3 text-sm hover:bg-white/10 {{ request()->is('training') ? 'bg-white/10' : '' }}">
                        <span class="w-2 h-2 rounded-full bg-white/30 mr-3"></span>
                        Training & Guides
                    </a>
                    @can('admin')
                    <div class="mt-4 px-6 text-[10px] uppercase tracking-wider text-white/60">Configuration</div>
                    <a href="{{ route('document-types.index') }}" class="flex items-center px-6 py-3 text-sm hover:bg-white/10 {{ request()->is('document-types*') ? 'bg-white/10' : '' }}">
                        <span class="w-2 h-2 rounded-full bg-white/30 mr-3"></span>
                        Document Types
                    </a>
                    <a href="{{ route('folders.index') }}" class="flex items-center px-6 py-3 text-sm hover:bg-white/10 {{ request()->is('folders*') ? 'bg-white/10' : '' }}">
                        <span class="w-2 h-2 rounded-full bg-white/30 mr-3"></span>
                        Folders
                    </a>
                    <a href="{{ route('templates.index') }}" class="flex items-center px-6 py-3 text-sm hover:bg-white/10 {{ request()->is('templates*') ? 'bg-white/10' : '' }}">
                        <span class="w-2 h-2 rounded-full bg-white/30 mr-3"></span>
                        Templates
                    </a>
                    <a href="{{ route('workflows.index') }}" class="flex items-center px-6 py-3 text-sm hover:bg-white/10 {{ request()->is('workflows*') ? 'bg-white/10' : '' }}">
                        <span class="w-2 h-2 rounded-full bg-white/30 mr-3"></span>
                        Workflows
                    </a>
                    <a href="{{ route('organization-units.index') }}" class="flex items-center px-6 py-3 text-sm hover:bg-white/10 {{ request()->is('organization-units*') ? 'bg-white/10' : '' }}">
                        <span class="w-2 h-2 rounded-full bg-white/30 mr-3"></span>
                        Organization Units
                    </a>
                    <a href="{{ route('classifications.index') }}" class="flex items-center px-6 py-3 text-sm hover:bg-white/10 {{ request()->is('classifications*') ? 'bg-white/10' : '' }}">
                        <span class="w-2 h-2 rounded-full bg-white/30 mr-3"></span>
                        Classifications
                    </a>
                    <a href="{{ route('users.index') }}" class="flex items-center px-6 py-3 text-sm hover:bg-white/10 {{ request()->is('users*') ? 'bg-white/10' : '' }}">
                        <span class="w-2 h-2 rounded-full bg-white/30 mr-3"></span>
                        Users & Roles
                    </a>
                    <a href="{{ route('activity-logs.index') }}" class="flex items-center px-6 py-3 text-sm hover:bg-white/10 {{ request()->is('activity-logs') ? 'bg-white/10' : '' }}">
                        <span class="w-2 h-2 rounded-full bg-white/30 mr-3"></span>
                        Activity Logs
                    </a>
                    @endcan
                </nav>
                <div class="p-6 border-t border-white/10 text-xs text-white/60">
                    © {{ date('Y') }} ORC DMS
                </div>
            </aside>

            <!-- Main Column -->
            <div class="flex-1 flex flex-col min-w-0">
                <!-- Header -->
                <header class="sticky top-0 z-20 bg-gradient-to-r from-orc-teal to-orc-navy text-white shadow">
                    <div class="flex items-center justify-between px-4 sm:px-6 lg:px-8 h-16">
                        <div class="flex items-center">
                            <button class="md:hidden mr-3 inline-flex items-center justify-center p-2 rounded-md bg-white/10 hover:bg-white/20 focus:outline-none" aria-label="Open Menu">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                            </button>
                            <div class="text-sm sm:text-base font-semibold">{{ $header ?? 'Dashboard' }}</div>
                        </div>
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" class="flex items-center space-x-2 bg-white/10 hover:bg-white/20 px-2 py-1 rounded-md">
                                <span class="inline-flex items-center justify-center h-8 w-8 rounded-full bg-white/20">
                                    <svg class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A3 3 0 017 17h10a3 3 0 011.879.804M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                </span>
                                <div class="text-left leading-tight">
                                    <div class="text-xs sm:text-sm text-white/90">{{ Auth::user()->name ?? 'User' }}</div>
                                    <div class="hidden sm:block text-[10px] text-white/70">{{ optional(Auth::user()->role)->name }}</div>
                                </div>
                                <svg class="h-4 w-4 text-white/80" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </button>
                            <div x-show="open" @click.away="open = false" class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg ring-1 ring-black/5 py-1 z-30">
                                <a href="{{ route('profile.edit') }}" class="block px-3 py-2 text-sm text-gray-700 hover:bg-gray-50">Profile</a>
                                <a href="{{ route('profile.edit') }}#change-password" class="block px-3 py-2 text-sm text-gray-700 hover:bg-gray-50">Change Password</a>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="w-full text-left px-3 py-2 text-sm text-gray-700 hover:bg-gray-50">Logout</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </header>

                <!-- Page Content -->
                <main class="p-4 sm:p-6 lg:p-8">
                    {{ $slot }}
                </main>
            </div>
        </div>
    </body>
</html>
