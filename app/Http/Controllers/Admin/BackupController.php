<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\System\BackupService;
use Illuminate\Http\Request;

class BackupController extends Controller
{
    public function __construct(protected BackupService $backupService) {}

    public function index(Request $request)
    {
        $backups = $this->backupService->listBackups();

        if ($request->wantsJson()) {
            return response()->json($backups);
        }

        if (view()->exists('admin.backups.index')) {
            return view('admin.backups.index', compact('backups'));
        }

        return response()->json($backups);
    }

    public function store(Request $request)
    {
        $type = $request->get('type', 'database');
        $backup = $this->backupService->createBackup($type);

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Backup created successfully.', 'backup' => $backup], 201);
        }

        toastr()->success("Backup '{$backup['filename']}' generated successfully.");
        return redirect()->back();
    }

    public function download(string $filename)
    {
        $path = $this->backupService->getBackupPath($filename);

        if (!$path || !file_exists($path)) {
            abort(404, 'Backup file not found.');
        }

        return response()->download($path);
    }

    public function destroy(string $filename)
    {
        $deleted = $this->backupService->deleteBackup($filename);

        if (request()->wantsJson()) {
            return response()->json(['deleted' => $deleted, 'message' => $deleted ? 'Backup deleted.' : 'File not found.']);
        }

        if ($deleted) {
            toastr()->success('Backup deleted.');
        } else {
            toastr()->error('Backup could not be deleted.');
        }

        return redirect()->back();
    }
}