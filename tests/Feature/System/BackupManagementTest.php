<?php

namespace Tests\Feature\System;

use App\Models\Company;
use App\Models\User;
use App\Services\System\BackupService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BackupManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_generate_and_list_backups()
    {
        $service = app(BackupService::class);
        $backup = $service->createBackup('database');

        $this->assertFileExists($backup['path']);
        $this->assertStringContainsString('backup_database_', $backup['filename']);

        $list = $service->listBackups();
        $this->assertNotEmpty($list);

        // Cleanup
        $service->deleteBackup($backup['filename']);
        $this->assertFileDoesNotExist($backup['path']);
    }
}
