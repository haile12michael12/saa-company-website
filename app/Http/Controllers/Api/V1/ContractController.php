<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\SignContractRequest;
use App\Http\Requests\StoreContractRequest;
use App\Models\Contract;
use App\Services\Contracts\ContractService;
use App\Services\Contracts\SignatureService;
use Illuminate\Http\Request;

class ContractController extends Controller
{
    public function __construct(
        protected ContractService $contractService,
        protected SignatureService $signatureService
    ) {}

    public function index(Request $request)
    {
        $contracts = $this->contractService->getContractsForCompany(
            $request->user()?->company_id,
            $request->only(['status', 'customer_id', 'search', 'per_page'])
        );

        return response()->json($contracts);
    }

    public function store(StoreContractRequest $request)
    {
        $contract = $this->contractService->createContract(
            $request->validated(),
            $request->user()?->company_id,
            $request->user()
        );

        return response()->json($contract, 201);
    }

    public function show(Contract $contract)
    {
        return response()->json($contract->load(['customer', 'signatures']));
    }

    public function sign(SignContractRequest $request, Contract $contract)
    {
        $signature = $this->signatureService->signContract(
            $contract,
            $request->validated(),
            $request->user()
        );

        return response()->json([
            'message' => 'Contract signed successfully.',
            'signature' => $signature,
        ]);
    }
}
