<?php

namespace Tests\Feature\Security;

use App\Models\AuditLog;
use App\Models\Company;
use App\Models\Customer;
use App\Models\SecurityLog;
use App\Models\User;
use App\Services\Security\AuditLogService;
use App\Services\Security\SecurityLogService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuditAndSecurityLogsTest extends TestCase
{
    use RefreshDatabase;

    public function test_auditable_trait_auto_records_model_changes()
    {
        $company = Company::create(['name' => 'Audited Org']);
        $user = User::factory()->create(['company_id' => $company->id]);
        $this->actingAs($user);

        $customer = Customer::create([
            'company_id' => $company->id,
            'name' => 'Tracked Client',
            'email' => 'client@track.com',
        ]);

        $this->assertDatabaseHas('audit_logs', [
            'auditable_type' => Customer::class,
            'auditable_id' => $customer->id,
            'action' => 'created',
        ]);

        $customer->update(['name' => 'Updated Client Name']);

        $this->assertDatabaseHas('audit_logs', [
            'auditable_type' => Customer::class,
            'auditable_id' => $customer->id,
            'action' => 'updated',
        ]);
    }

    public function test_security_log_service_tracks_events()
    {
        $company = Company::create(['name' => 'Secure Org']);
        $user = User::factory()->create(['company_id' => $company->id]);

        $service = app(SecurityLogService::class);
        $service->logLoginAttempt($user->email, true);

        $this->assertDatabaseHas('security_logs', [
            'user_id' => $user->id,
            'event_type' => 'login_success',
            'severity' => 'info',
        ]);

        $service->logLoginAttempt('intruder@bad.com', false, 'Invalid password');

        $this->assertDatabaseHas('security_logs', [
            'event_type' => 'login_failed',
            'severity' => 'warning',
        ]);
    }
}
