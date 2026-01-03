<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-white">Create User</h2>
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

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <form method="POST" action="{{ route('users.store') }}" class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @csrf

            <div>
                <x-input-label value="Name" />
                <x-text-input name="name" value="{{ old('name') }}" class="w-full" required />
            </div>
            <div>
                <x-input-label value="Email" />
                <x-text-input name="email" type="email" value="{{ old('email') }}" class="w-full" required />
            </div>
            <div>
                <x-input-label value="Password" />
                <x-text-input name="password" type="password" class="w-full" required />
            </div>
            <div>
                <x-input-label value="Confirm Password" />
                <x-text-input name="password_confirmation" type="password" class="w-full" required />
            </div>

            <div>
                <x-input-label value="Role" />
                <select name="role_id" class="w-full border-orc-navy/30 focus:border-orc-teal focus:ring-orc-teal rounded-md" required>
                    @foreach($roles as $r)
                        <option value="{{ $r->id }}" @selected(old('role_id') == $r->id)>{{ $r->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <x-input-label value="Organization Unit" />
                <select name="organization_unit_id" class="w-full border-orc-navy/30 focus:border-orc-teal focus:ring-orc-teal rounded-md">
                    <option value="">-- None --</option>
                    @foreach($units as $u)
                        <option value="{{ $u->id }}" @selected(old('organization_unit_id') == $u->id)>{{ $u->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <x-input-label value="Status" />
                <select name="status" class="w-full border-orc-navy/30 focus:border-orc-teal focus:ring-orc-teal rounded-md" required>
                    @foreach(['active','inactive','suspended'] as $s)
                        <option value="{{ $s }}" @selected(old('status','active') == $s)>{{ ucfirst($s) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <x-input-label value="Clearance Level" />
                <x-text-input type="number" name="clearance_level" min="1" max="10" value="{{ old('clearance_level', 1) }}" class="w-full" required />
            </div>
            <div class="md:col-span-2">
                <x-input-label value="Phone" />
                <x-text-input name="phone" value="{{ old('phone') }}" class="w-full" />
            </div>

            <div class="md:col-span-2 flex justify-end gap-2">
                <a href="{{ route('users.index') }}" class="inline-flex items-center px-4 py-2 rounded-md bg-gray-200 text-gray-800 text-xs uppercase tracking-widest">Cancel</a>
                <x-primary-button>Create User</x-primary-button>
            </div>
        </form>
    </div>
</x-app-layout>
