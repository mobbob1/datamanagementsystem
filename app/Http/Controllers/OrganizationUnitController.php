<?php

namespace App\Http\Controllers;

use App\Models\OrganizationUnit;
use App\Models\ActivityLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrganizationUnitController extends Controller
{
    public function index(Request $request): View
    {
        $units = OrganizationUnit::with('parent')
            ->orderBy('name')
            ->paginate(15);

        return view('organization_units.index', compact('units'));
    }

    public function create(): View
    {
        $this->authorize('admin');
        $parents = OrganizationUnit::orderBy('name')->get();
        return view('organization_units.create', compact('parents'));
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('admin');
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['nullable', 'string', 'max:50', 'unique:organization_units,code'],
            'type' => ['required', 'string', 'max:50'],
            'parent_id' => ['nullable', 'exists:organization_units,id'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $data['is_active'] = (bool) ($data['is_active'] ?? true);

        $unit = OrganizationUnit::create($data);

        ActivityLog::create([
            'user_id' => $request->user()->id,
            'action' => 'organization_unit.created',
            'subject_type' => OrganizationUnit::class,
            'subject_id' => $unit->id,
            'properties' => $data,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return redirect()->route('organization-units.index')
            ->with('status', 'Organization Unit created.');
    }

    public function edit(OrganizationUnit $organization_unit): View
    {
        $this->authorize('admin');
        $parents = OrganizationUnit::where('id', '!=', $organization_unit->id)
            ->orderBy('name')->get();
        return view('organization_units.edit', [
            'unit' => $organization_unit,
            'parents' => $parents,
        ]);
    }

    public function update(Request $request, OrganizationUnit $organization_unit): RedirectResponse
    {
        $this->authorize('admin');
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['nullable', 'string', 'max:50', 'unique:organization_units,code,' . $organization_unit->id],
            'type' => ['required', 'string', 'max:50'],
            'parent_id' => ['nullable', 'exists:organization_units,id'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        if (isset($data['parent_id']) && (int)$data['parent_id'] === (int)$organization_unit->id) {
            return back()->withErrors(['parent_id' => 'A unit cannot be its own parent.'])->withInput();
        }

        $data['is_active'] = (bool) ($data['is_active'] ?? true);

        $organization_unit->update($data);

        ActivityLog::create([
            'user_id' => $request->user()->id,
            'action' => 'organization_unit.updated',
            'subject_type' => OrganizationUnit::class,
            'subject_id' => $organization_unit->id,
            'properties' => $data,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return redirect()->route('organization-units.index')
            ->with('status', 'Organization Unit updated.');
    }

    public function destroy(OrganizationUnit $organization_unit): RedirectResponse
    {
        $this->authorize('admin');
        $id = $organization_unit->id;
        $organization_unit->delete();
        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'organization_unit.deleted',
            'subject_type' => OrganizationUnit::class,
            'subject_id' => $id,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
        return redirect()->route('organization-units.index')
            ->with('status', 'Organization Unit deleted.');
    }
}
