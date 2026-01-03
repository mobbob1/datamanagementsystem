<?php

namespace App\Http\Controllers;

use App\Models\Classification;
use App\Models\ActivityLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ClassificationController extends Controller
{
    public function index(Request $request): View
    {
        $classifications = Classification::orderBy('clearance_level')->orderBy('name')->paginate(15);
        return view('classifications.index', compact('classifications'));
    }

    public function create(): View
    {
        $this->authorize('admin');
        return view('classifications.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('admin');
        $data = $request->validate([
            'key' => ['required', 'string', 'max:50', 'unique:classifications,key'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'clearance_level' => ['required', 'integer', 'min:1', 'max:10'],
            'is_active' => ['sometimes', 'boolean'],
        ]);
        $data['is_active'] = (bool) ($data['is_active'] ?? true);

        $classification = Classification::create($data);

        ActivityLog::create([
            'user_id' => $request->user()->id,
            'action' => 'classification.created',
            'subject_type' => Classification::class,
            'subject_id' => $classification->id,
            'properties' => $data,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return redirect()->route('classifications.index')->with('status', 'Classification created.');
    }

    public function edit(Classification $classification): View
    {
        $this->authorize('admin');
        return view('classifications.edit', compact('classification'));
    }

    public function update(Request $request, Classification $classification): RedirectResponse
    {
        $this->authorize('admin');
        $data = $request->validate([
            'key' => ['required', 'string', 'max:50', 'unique:classifications,key,' . $classification->id],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'clearance_level' => ['required', 'integer', 'min:1', 'max:10'],
            'is_active' => ['sometimes', 'boolean'],
        ]);
        $data['is_active'] = (bool) ($data['is_active'] ?? true);

        $classification->update($data);

        ActivityLog::create([
            'user_id' => $request->user()->id,
            'action' => 'classification.updated',
            'subject_type' => Classification::class,
            'subject_id' => $classification->id,
            'properties' => $data,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return redirect()->route('classifications.index')->with('status', 'Classification updated.');
    }

    public function destroy(Classification $classification): RedirectResponse
    {
        $this->authorize('admin');
        $id = $classification->id;
        $classification->delete();
        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'classification.deleted',
            'subject_type' => Classification::class,
            'subject_id' => $id,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
        return redirect()->route('classifications.index')->with('status', 'Classification deleted.');
    }
}
