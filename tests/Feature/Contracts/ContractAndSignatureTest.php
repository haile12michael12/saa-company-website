<?php

namespace Tests\Feature\Contracts;

use App\Models\Company;
use App\Models\Contract;
use App\Models\Customer;
use App\Models\Quote;
use App\Models\User;
use App\Services\Contracts\ContractService;
use App\Services\Contracts\SignatureService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContractAndSignatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_generate_contract_from_quote()
    {
        $company = Company::create(['name' => 'Acme Corp']);
        $user = User::factory()->create(['company_id' => $company->id]);
        $customer = Customer::create(['company_id' => $company->id, 'name' => 'Acme Client', 'email' => 'client@acme.com']);
        $quote = Quote::create([
            'company_id' => $company->id,
            'customer_id' => $customer->id,
            'quote_number' => 'Q-9901',
            'status' => 'accepted',
            'total' => 5000.00,
        ]);

        $contractService = app(ContractService::class);
        $contract = $contractService->generateFromQuote($quote, ['duration_months' => 6]);

        $this->assertDatabaseHas('contracts', [
            'id' => $contract->id,
            'quote_id' => $quote->id,
            'company_id' => $company->id,
        ]);
        $this->assertStringContainsString('$5,000.00', $contract->content);
    }

    public function test_can_digitally_sign_contract()
    {
        $company = Company::create(['name' => 'Acme Corp']);
        $user = User::factory()->create(['company_id' => $company->id]);
        $customer = Customer::create(['company_id' => $company->id, 'name' => 'Signer User', 'email' => 'signer@example.com']);
        $contract = Contract::create([
            'company_id' => $company->id,
            'customer_id' => $customer->id,
            'number' => 'CTR-1001',
            'status' => 'sent',
            'content' => 'Sample Agreement terms',
        ]);

        $signatureService = app(SignatureService::class);
        $sig = $signatureService->signContract($contract, [
            'signer_name' => 'John Signer',
            'signer_email' => 'signer@example.com',
            'signature' => 'BASE64_SIGNATURE_DATA',
        ], $user);

        $this->assertDatabaseHas('contract_signatures', [
            'contract_id' => $contract->id,
            'signer_name' => 'John Signer',
        ]);
        $this->assertEquals('signed', $contract->fresh()->status);
        $this->assertTrue($signatureService->verifySignature($sig));
    }

    public function test_public_contract_signing_endpoint()
    {
        $company = Company::create(['name' => 'Acme Corp']);
        $customer = Customer::create(['company_id' => $company->id, 'name' => 'Public Signer', 'email' => 'public@example.com']);
        $contract = Contract::create([
            'company_id' => $company->id,
            'customer_id' => $customer->id,
            'number' => 'CTR-PUBLIC-77',
            'status' => 'sent',
            'content' => 'Public signing agreement terms',
        ]);

        $response = $this->postJson(route('contracts.public.sign', ['number' => $contract->number]), [
            'signer_name' => 'Public Signer',
            'signer_email' => 'public@example.com',
            'signature' => 'SIGN_DATA',
            'agree_terms' => true,
        ]);

        $response->assertStatus(200);
        $this->assertEquals('signed', $contract->fresh()->status);
    }
}
