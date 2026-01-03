<?php

namespace App\Http\Controllers;

use App\Models\Template;
use App\Models\DocumentType;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class TemplateController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('admin');
        $templates = Template::with('documentType')->orderBy('name')->paginate(15);
        return view('templates.index', compact('templates'));
    }

    public function create(): View
    {
        $this->authorize('admin');
        return view('templates.create', [
            'types' => DocumentType::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('admin');
        $data = $request->validate([
            'key' => ['required','string','max:50','unique:templates,key'],
            'name' => ['required','string','max:255'],
            'document_type_id' => ['nullable','exists:document_types,id'],
            'description' => ['nullable','string','max:2000'],
            'is_active' => ['sometimes','boolean'],
        ]);
        $data['is_active'] = (bool)($data['is_active'] ?? true);

        $template = Template::create($data);
        ActivityLog::create([
            'user_id' => $request->user()->id,
            'action' => 'template.created',
            'subject_type' => Template::class,
            'subject_id' => $template->id,
            'properties' => $data,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return redirect()->route('templates.index')->with('status', 'Template created.');
    }

    public function edit(Template $template): View
    {
        $this->authorize('admin');
        return view('templates.edit', [
            'template' => $template,
            'types' => DocumentType::orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Template $template): RedirectResponse
    {
        $this->authorize('admin');
        $data = $request->validate([
            'key' => ['required','string','max:50','unique:templates,key,' . $template->id],
            'name' => ['required','string','max:255'],
            'document_type_id' => ['nullable','exists:document_types,id'],
            'description' => ['nullable','string','max:2000'],
            'is_active' => ['sometimes','boolean'],
        ]);
        $data['is_active'] = (bool)($data['is_active'] ?? true);

        $template->update($data);
        ActivityLog::create([
            'user_id' => $request->user()->id,
            'action' => 'template.updated',
            'subject_type' => Template::class,
            'subject_id' => $template->id,
            'properties' => $data,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return redirect()->route('templates.index')->with('status', 'Template updated.');
    }

    public function destroy(Template $template): RedirectResponse
    {
        $this->authorize('admin');
        $id = $template->id;
        $template->delete();
        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'template.deleted',
            'subject_type' => Template::class,
            'subject_id' => $id,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
        return redirect()->route('templates.index')->with('status', 'Template deleted.');
    }
}
