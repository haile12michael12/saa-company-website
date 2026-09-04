<?php

namespace App\Services\System;

use Carbon\Carbon;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class BackupService
{
    protected string $backupDir;

    public function __construct()
    {
        $this->backupDir = storage_path('app/backups');
        if (!File::exists($this->backupDir)) {
            File::makeDirectory($this->backupDir, 0755, true);
        }
    }

    public function listBackups(): array
    {
        $files = File::files($this->backupDir);
        $backups = [];

        foreach ($files as $file) {
            $backups[] = [
                'filename' => $file->getFilename(),
                'size' => $this->formatBytes($file->getSize()),
                'size_bytes' => $file->getSize(),
                'created_at' => Carbon::createFromTimestamp($file->getMTime())->toDateTimeString(),
            ];
        }

        // Sort latest first
        usort($backups, fn($a, $b) => strcmp($b['created_at'], $a['created_at']));

        return $backups;
    }

    public function createBackup(string $type = 'database'): array
    {
        $filename = 'backup_' . $type . '_' . now()->format('Y_m_d_His') . '.json';
        $fullPath = $this->backupDir . DIRECTORY_SEPARATOR . $filename;

        // Snapshot database statistics & structure
        $data = [
            'type' => $type,
            'timestamp' => now()->toIso8601String(),
            'app_version' => '1.0.0',
            'environment' => config('app.env'),
            'tables_summary' => [
                'companies' => \App\Models\Company::count(),
                'users' => \App\Models\User::count(),
                'leads' => \App\Models\Lead::count(),
                'quotes' => \App\Models\Quote::count(),
                'contracts' => \App\Models\Contract::count(),
                'appointments' => \App\Models\Appointment::count(),
                'conversations' => \App\Models\Conversation::count(),
            ],
        ];

        File::put($fullPath, json_encode($data, JSON_PRETTY_PRINT));

        return [
            'filename' => $filename,
            'size' => $this->formatBytes(filesize($fullPath)),
            'path' => $fullPath,
            'created_at' => now()->toDateTimeString(),
        ];
    }

    public function deleteBackup(string $filename): bool
    {
        $path = $this->backupDir . DIRECTORY_SEPARATOR . basename($filename);
        if (File::exists($path)) {
            return File::delete($path);
        }
        return false;
    }

    public function getBackupPath(string $filename): ?string
    {
        $path = $this->backupDir . DIRECTORY_SEPARATOR . basename($filename);
        return File::exists($path) ? $path : null;
    }

    protected function formatBytes(int $bytes, int $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);

        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}
