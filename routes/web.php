<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
});

Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Master Data
    Route::resource('organization-units', \App\Http\Controllers\OrganizationUnitController::class)->except(['show']);
    Route::resource('classifications', \App\Http\Controllers\ClassificationController::class)->except(['show']);
    Route::resource('document-types', \App\Http\Controllers\DocumentTypeController::class)->except(['show']);

    // Documents
    Route::resource('documents', \App\Http\Controllers\DocumentController::class);
    Route::get('documents-pending', [\App\Http\Controllers\DocumentController::class, 'pending'])->name('documents.pending');
    Route::get('documents-bulk', [\App\Http\Controllers\DocumentController::class, 'bulkCreate'])->name('documents.bulk.create');
    Route::post('documents-bulk', [\App\Http\Controllers\DocumentController::class, 'bulkStore'])->name('documents.bulk.store');
    Route::post('documents/{document}/version', [\App\Http\Controllers\DocumentController::class, 'uploadVersion'])->name('documents.version.upload');
    Route::post('documents/{document}/lock', [\App\Http\Controllers\DocumentController::class, 'lock'])->name('documents.lock');
    Route::post('documents/{document}/unlock', [\App\Http\Controllers\DocumentController::class, 'unlock'])->name('documents.unlock');
    Route::post('documents/{document}/legal-hold', [\App\Http\Controllers\DocumentController::class, 'toggleLegalHold'])->name('documents.legal-hold.toggle');
    Route::post('documents/search/save', [\App\Http\Controllers\DocumentController::class, 'saveSearch'])->name('documents.search.save');
    Route::post('documents/{document}/permissions', [\App\Http\Controllers\DocumentController::class, 'addPermission'])->name('documents.permissions.add');
    Route::delete('documents/{document}/permissions/{permission}', [\App\Http\Controllers\DocumentController::class, 'removePermission'])->name('documents.permissions.remove');
    Route::get('files/{file}/preview', [\App\Http\Controllers\DocumentController::class, 'preview'])->name('files.preview');
    Route::get('files/{file}/download', [\App\Http\Controllers\DocumentController::class, 'download'])->name('files.download');
    Route::post('documents/{document}/copy', [\App\Http\Controllers\DocumentController::class, 'copy'])->name('documents.copy');
    Route::post('documents/{document}/move', [\App\Http\Controllers\DocumentController::class, 'move'])->name('documents.move');
    Route::post('documents/{document}/rename', [\App\Http\Controllers\DocumentController::class, 'rename'])->name('documents.rename');
    Route::post('documents/{document}/workflow/approve', [\App\Http\Controllers\WorkflowRuntimeController::class, 'approve'])->name('documents.workflow.approve');
    Route::post('documents/{document}/workflow/reject', [\App\Http\Controllers\WorkflowRuntimeController::class, 'reject'])->name('documents.workflow.reject');

    // Archive & Config
    Route::get('archive', [\App\Http\Controllers\DocumentController::class, 'archive'])->name('archive.index');
    Route::resource('templates', \App\Http\Controllers\TemplateController::class)->except(['show']);
    // Folders: admin management
    Route::resource('folders', \App\Http\Controllers\FolderController::class)->except(['show']);
    // Folders: user browsing (accessible to all authenticated users)
    Route::get('folders/browse/{folder?}', [\App\Http\Controllers\FolderController::class, 'browse'])->name('folders.browse');
    Route::post('folders/{folder}/permissions', [\App\Http\Controllers\FolderController::class, 'addPermission'])->name('folders.permissions.add');
    Route::delete('folders/{folder}/permissions/{permission}', [\App\Http\Controllers\FolderController::class, 'removePermission'])->name('folders.permissions.remove');
    Route::resource('workflows', \App\Http\Controllers\WorkflowController::class)->except(['show']);
    Route::post('workflows/{workflow}/steps', [\App\Http\Controllers\WorkflowController::class, 'storeStep'])->name('workflows.steps.store');
    Route::delete('workflows/{workflow}/steps/{step}', [\App\Http\Controllers\WorkflowController::class, 'destroyStep'])->name('workflows.steps.destroy');
    Route::post('workflows/{workflow}/steps/{step}/up', [\App\Http\Controllers\WorkflowController::class, 'moveStepUp'])->name('workflows.steps.up');
    Route::post('workflows/{workflow}/steps/{step}/down', [\App\Http\Controllers\WorkflowController::class, 'moveStepDown'])->name('workflows.steps.down');
    Route::get('activity-logs', [\App\Http\Controllers\ActivityLogController::class, 'index'])->name('activity-logs.index');
    Route::get('activity-logs/export', [\App\Http\Controllers\ActivityLogController::class, 'export'])->name('activity-logs.export');
    Route::resource('users', \App\Http\Controllers\UserAdminController::class)->only(['index','create','store','edit','update']);

    // Reports & Analytics
    Route::get('reports', [\App\Http\Controllers\ReportsController::class, 'index'])->name('reports.index');
    Route::get('reports/export', [\App\Http\Controllers\ReportsController::class, 'export'])->name('reports.export');

    // Training & Guides
    Route::get('training', [\App\Http\Controllers\TrainingController::class, 'index'])->name('training.index');

    // QR/Barcode scan & Suggestions
    Route::get('scan', [\App\Http\Controllers\DocumentController::class, 'scan'])->name('scan');
    Route::get('documents/suggest', [\App\Http\Controllers\DocumentController::class, 'suggest'])->name('documents.suggest');
});

require __DIR__.'/auth.php';
