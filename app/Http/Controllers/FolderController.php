<?php

namespace App\Http\Controllers;

use App\Models\Folder;
use App\Models\FolderPermission;
use App\Models\OrganizationUnit;
use App\Models\ActivityLog;
use App\Models\Document;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class FolderController extends Controller
{
    /**
     * Browse folders accessible to the current user and view folder documents.
     */
    public function browse(Request $request, Folder $folder = null): View
    {
        $user = $request->user();
        $q = trim((string) $request->query('q', ''));
        // Load root-level folders when none selected
        if (!$folder) {
            $all = Folder::withCount('children')->whereNull('parent_id')->orderBy('name')->get();
            $folders = $all->filter(function(Folder $f) use ($user) { return $this->folderAccessible($user, $f); });
            if ($q !== '') {
                $folders = $folders->filter(function(Folder $f) use ($q) {
                    return stripos($f->name, $q) !== false;
                });
            }
            return view('folders.browse', [
                'current' => null,
                'breadcrumbs' => collect(),
                'folders' => $folders,
                'documents' => collect(),
                'q' => $q,
                'canUpload' => false,
            ]);
        }

        // Ensure the user can access this folder path
        if (!$this->folderAccessible($user, $folder) && !(method_exists($user,'isAdmin') && $user->isAdmin()) && optional($user->role)->key !== 'registrar') {
            abort(403);
        }

        // Build breadcrumbs
        $breadcrumbs = collect();
        $ptr = $folder;
        while ($ptr) {
            $breadcrumbs->prepend($ptr);
            $ptr = $ptr->parent; // relies on model relation
        }

        // Child folders that are accessible
        $children = $folder->children()->orderBy('name')->get();
        $folders = $children->filter(function(Folder $f) use ($user) { return $this->folderAccessible($user, $f); });
        if ($q !== '') {
            $folders = $folders->filter(function(Folder $f) use ($q) {
                return stripos($f->name, $q) !== false;
            });
        }

        // Documents within this folder filtered by policy (view)
        $docs = Document::with(['type','classification','originUnit','files' => function($q){ $q->where('is_current', true); }])
            ->where('folder_id', $folder->id)
            ->latest()
            ->when($q !== '', function($b) use ($q) {
                $b->where(function($w) use ($q) {
                    $w->where('title', 'like', "%$q%")
                      ->orWhere('doc_number', 'like', "%$q%");
                });
            })
            ->get()
            ->filter(function(Document $d) use ($user) { return $user->can('view', $d); });

        $canUpload = $this->folderEditable($user, $folder) && $user->can('create', Document::class);

        return view('folders.browse', [
            'current' => $folder,
            'breadcrumbs' => $breadcrumbs,
            'folders' => $folders,
            'documents' => $docs,
            'q' => $q,
            'canUpload' => $canUpload,
        ]);
    }

    /** Determine if user can access folder via unit or explicit permissions on any ancestor. */
    protected function folderAccessible(User $user, Folder $folder): bool
    {
        // Admins/registrars see everything
        if ((method_exists($user,'isAdmin') && $user->isAdmin()) || optional($user->role)->key === 'registrar') {
            return true;
        }
        $ptr = $folder;
        while ($ptr) {
            if ($ptr->organization_unit_id && $user->organization_unit_id && $ptr->organization_unit_id === $user->organization_unit_id) {
                return true;
            }
            $perm = $ptr->permissions()->where('user_id', $user->id)->where('can_view', true)->first();
            if ($perm) { return true; }
            $ptr = $ptr->parent;
        }
        return false;
    }

    /** Determine if user can upload (edit) into folder via unit or explicit edit permissions on any ancestor. */
    protected function folderEditable(User $user, Folder $folder): bool
    {
        if ((method_exists($user,'isAdmin') && $user->isAdmin()) || optional($user->role)->key === 'registrar') {
            return true;
        }
        $ptr = $folder;
        while ($ptr) {
            if ($ptr->organization_unit_id && $user->organization_unit_id && $ptr->organization_unit_id === $user->organization_unit_id) {
                return true; // unit can upload
            }
            $perm = $ptr->permissions()->where('user_id', $user->id)->first();
            if ($perm && ($perm->can_edit || $perm->can_view)) { // allow upload if at least explicit access (adjust if needed)
                return true;
            }
            $ptr = $ptr->parent;
        }
        return false;
    }

    public function index(Request $request): View
    {
        $this->authorize('admin');
        $folders = Folder::with(['parent','unit'])->orderBy('name')->paginate(20);
        return view('folders.index', compact('folders'));
    }

    public function create(): View
    {
        $this->authorize('admin');
        return view('folders.create', [
            'parents' => Folder::orderBy('name')->get(),
            'units' => OrganizationUnit::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('admin');
        $data = $request->validate([
            'name' => ['required','string','max:255'],
            'parent_id' => ['nullable','exists:folders,id'],
            'slug' => ['nullable','string','max:255'],
            'description' => ['nullable','string','max:2000'],
            'organization_unit_id' => ['nullable','exists:organization_units,id'],
        ]);
        $folder = Folder::create(array_merge($data, [
            'created_by' => $request->user()->id,
        ]));
        ActivityLog::create([
            'user_id' => $request->user()->id,
            'action' => 'folder.created',
            'subject_type' => Folder::class,
            'subject_id' => $folder->id,
            'properties' => $data,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);
        return redirect()->route('folders.index')->with('status', 'Folder created.');
    }

    public function edit(Folder $folder): View
    {
        $this->authorize('admin');
        $folder->load(['permissions.user','parent','children','unit']);
        return view('folders.edit', [
            'folder' => $folder,
            'parents' => Folder::where('id','<>',$folder->id)->orderBy('name')->get(),
            'units' => OrganizationUnit::orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Folder $folder): RedirectResponse
    {
        $this->authorize('admin');
        $data = $request->validate([
            'name' => ['required','string','max:255'],
            'parent_id' => ['nullable','exists:folders,id'],
            'slug' => ['nullable','string','max:255'],
            'description' => ['nullable','string','max:2000'],
            'organization_unit_id' => ['nullable','exists:organization_units,id'],
        ]);
        $folder->update($data);
        ActivityLog::create([
            'user_id' => $request->user()->id,
            'action' => 'folder.updated',
            'subject_type' => Folder::class,
            'subject_id' => $folder->id,
            'properties' => $data,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);
        return back()->with('status', 'Folder updated.');
    }

    public function destroy(Folder $folder): RedirectResponse
    {
        $this->authorize('admin');
        $id = $folder->id;
        $folder->delete();
        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'folder.deleted',
            'subject_type' => Folder::class,
            'subject_id' => $id,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
        return redirect()->route('folders.index')->with('status', 'Folder deleted.');
    }

    public function addPermission(Folder $folder, Request $request): RedirectResponse
    {
        $this->authorize('admin');
        $data = $request->validate([
            'user_id' => ['required','exists:users,id'],
            'can_view' => ['sometimes','boolean'],
            'can_edit' => ['sometimes','boolean'],
        ]);
        $perm = $folder->permissions()->updateOrCreate(
            ['user_id' => $data['user_id']],
            [
                'can_view' => (bool)($data['can_view'] ?? true),
                'can_edit' => (bool)($data['can_edit'] ?? false),
            ]
        );
        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'folder.permission_added',
            'subject_type' => Folder::class,
            'subject_id' => $folder->id,
            'properties' => ['permission_id' => $perm->id, 'user_id' => $perm->user_id, 'can_view' => $perm->can_view, 'can_edit' => $perm->can_edit],
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
        return back()->with('status', 'Permission updated.');
    }

    public function removePermission(Folder $folder, FolderPermission $permission): RedirectResponse
    {
        $this->authorize('admin');
        if ($permission->folder_id !== $folder->id) abort(404);
        $permission->delete();
        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'folder.permission_removed',
            'subject_type' => Folder::class,
            'subject_id' => $folder->id,
            'properties' => ['permission_id' => $permission->id],
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
        return back()->with('status', 'Permission removed.');
    }
}
