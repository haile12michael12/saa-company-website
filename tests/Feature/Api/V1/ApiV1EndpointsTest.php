<?php

namespace Tests\Feature\Api\V1;

use App\Models\Company;
use App\Models\Customer;
use App\Models\Lead;
use App\Models\Quote;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ApiV1EndpointsTest extends TestCase
{
    use RefreshDatabase;

    public function test_api_login_and_authenticated_profile()
    {
        $company = Company::create(['name' => 'API Corp']);
        $user = User::factory()->create([
            'company_id' => $company->id,
            'email' => 'api_user@example.com',
            'password' => Hash::make('secret123'),
        ]);

        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'api_user@example.com',
            'password' => 'secret123',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure(['token', 'token_type', 'user']);

        $token = $response->json('token');

        $profileResponse = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/v1/auth/me');

        $profileResponse->assertStatus(200)
            ->assertJsonPath('email', 'api_user@example.com');
    }

    public function test_api_v1_customers_crud()
    {
        $company = Company::create(['name' => 'API Corp']);
        $user = User::factory()->create(['company_id' => $company->id]);
        Sanctum::actingAs($user);

        // Create
        $response = $this->postJson('/api/v1/customers', [
            'name' => 'API Customer',
            'email' => 'customer@api.com',
            'phone' => '+1234567890',
        ]);
        $response->assertStatus(201);
        $customerId = $response->json('id');

        // List
        $listResponse = $this->getJson('/api/v1/customers');
        $listResponse->assertStatus(200)
            ->assertJsonCount(1, 'data');

        // Show
        $showResponse = $this->getJson("/api/v1/customers/{$customerId}");
        $showResponse->assertStatus(200)
            ->assertJsonPath('name', 'API Customer');
    }

    public function test_api_v1_leads_and_scoring()
    {
        $company = Company::create(['name' => 'API Corp']);
        $user = User::factory()->create(['company_id' => $company->id]);
        Sanctum::actingAs($user);

        $response = $this->postJson('/api/v1/leads', [
            'name' => 'API Lead',
            'email' => 'lead@corporate-domain.com',
            'phone' => '+1234567890',
            'notes' => 'Enterprise inquiries looking for full stack overhaul.',
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('name', 'API Lead');
        $this->assertNotNull($response->json('score'));
    }
}
