<?php

namespace App\Http\Controllers;

use App\Models\DocumentType;
use App\Models\Classification;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class DocumentTypeController extends Controller
{
    public function index(Request $request): View
    {
        $types = DocumentType::orderBy('name')->paginate(15);
        return view('document_types.index', compact('types'));
    }

    public function create(): View
    {
        $this->authorize('admin');
        return view('document_types.create', [
            'classifications' => Classification::orderBy('clearance_level')->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('admin');
        $data = $request->validate([
            'key' => ['required','string','max:50','unique:document_types,key'],
            'name' => ['required','string','max:255'],
            'description' => ['nullable','string','max:1000'],
            'default_classification_id' => ['nullable','exists:classifications,id'],
            'default_retention_months' => ['nullable','integer','min:0','max:600'],
            'is_active' => ['sometimes','boolean'],
        ]);
        $data['is_active'] = (bool)($data['is_active'] ?? true);

        $type = DocumentType::create($data);
        ActivityLog::create([
            'user_id' => $request->user()->id,
            'action' => 'document_type.created',
            'subject_type' => DocumentType::class,
            'subject_id' => $type->id,
            'properties' => $data,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return redirect()->route('document-types.index')->with('status', 'Document type created.');
    }

    public function edit(DocumentType $document_type): View
    {
        $this->authorize('admin');
        return view('document_types.edit', [
            'type' => $document_type,
            'classifications' => Classification::orderBy('clearance_level')->orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, DocumentType $document_type): RedirectResponse
    {
        $this->authorize('admin');
        $data = $request->validate([
            'key' => ['required','string','max:50','unique:document_types,key,' . $document_type->id],
            'name' => ['required','string','max:255'],
            'description' => ['nullable','string','max:1000'],
            'default_classification_id' => ['nullable','exists:classifications,id'],
            'default_retention_months' => ['nullable','integer','min:0','max:600'],
            'is_active' => ['sometimes','boolean'],
        ]);
        $data['is_active'] = (bool)($data['is_active'] ?? true);

        $document_type->update($data);
        ActivityLog::create([
            'user_id' => $request->user()->id,
            'action' => 'document_type.updated',
            'subject_type' => DocumentType::class,
            'subject_id' => $document_type->id,
            'properties' => $data,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return redirect()->route('document-types.index')->with('status', 'Document type updated.');
    }

    public function destroy(DocumentType $document_type): RedirectResponse
    {
        $this->authorize('admin');
        $id = $document_type->id;
        $document_type->delete();
        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'document_type.deleted',
            'subject_type' => DocumentType::class,
            'subject_id' => $id,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
        return redirect()->route('document-types.index')->with('status', 'Document type deleted.');
    }
}
