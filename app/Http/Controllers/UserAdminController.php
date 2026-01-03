<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use App\Models\OrganizationUnit;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Hash;

class UserAdminController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('admin');
        $users = User::with(['role','organizationUnit'])->orderBy('name')->paginate(15);
        return view('users.index', compact('users'));
    }

    public function create(): View
    {
        $this->authorize('admin');
        return view('users.create', [
            'roles' => Role::orderBy('name')->get(),
            'units' => OrganizationUnit::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('admin');
        $data = $request->validate([
            'name' => ['required','string','max:255'],
            'email' => ['required','email','max:255','unique:users,email'],
            'password' => ['required','string','min:8','confirmed'],
            'role_id' => ['required','exists:roles,id'],
            'organization_unit_id' => ['nullable','exists:organization_units,id'],
            'status' => ['required','in:active,inactive,suspended'],
            'clearance_level' => ['required','integer','min:1','max:10'],
            'phone' => ['nullable','string','max:50'],
        ]);

        $user = new User();
        $user->name = $data['name'];
        $user->email = $data['email'];
        $user->password = Hash::make($data['password']);
        $user->role_id = $data['role_id'];
        $user->organization_unit_id = $data['organization_unit_id'] ?? null;
        $user->status = $data['status'];
        $user->clearance_level = $data['clearance_level'];
        $user->phone = $data['phone'] ?? null;
        $user->save();

        ActivityLog::create([
            'user_id' => $request->user()->id,
            'action' => 'user.created',
            'subject_type' => User::class,
            'subject_id' => $user->id,
            'properties' => [
                'role_id' => $user->role_id,
                'organization_unit_id' => $user->organization_unit_id,
                'status' => $user->status,
                'clearance_level' => $user->clearance_level,
            ],
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return redirect()->route('users.index')->with('status', 'User created.');
    }

    public function edit(User $user): View
    {
        $this->authorize('admin');
        return view('users.edit', [
            'user' => $user->load(['role','organizationUnit']),
            'roles' => Role::orderBy('name')->get(),
            'units' => OrganizationUnit::orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $this->authorize('admin');
        $data = $request->validate([
            'role_id' => ['required','exists:roles,id'],
            'organization_unit_id' => ['nullable','exists:organization_units,id'],
            'status' => ['required','in:active,inactive,suspended'],
            'clearance_level' => ['required','integer','min:1','max:10'],
            'phone' => ['nullable','string','max:50'],
        ]);

        $user->update($data);

        ActivityLog::create([
            'user_id' => $request->user()->id,
            'action' => 'user.updated',
            'subject_type' => User::class,
            'subject_id' => $user->id,
            'properties' => $data,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return redirect()->route('users.index')->with('status', 'User updated.');
    }
}
