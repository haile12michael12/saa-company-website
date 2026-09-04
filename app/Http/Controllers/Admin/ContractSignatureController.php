<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\SignContractRequest;
use App\Models\Contract;
use App\Models\ContractSignature;
use App\Services\Contracts\SignatureService;
use Illuminate\Http\Request;

class ContractSignatureController extends Controller
{
    public function __construct(protected SignatureService $signatureService) {}

    public function sign(SignContractRequest $request, Contract $contract)
    {
        $signature = $this->signatureService->signContract(
            $contract,
            $request->validated(),
            auth()->user()
        );

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Contract digitally signed and executed successfully.',
                'signature' => $signature,
            ]);
        }

        toastr()->success('Contract digitally signed.');
        return redirect()->route('admin.contracts.show', $contract);
    }

    public function verify(ContractSignature $signature)
    {
        $isValid = $this->signatureService->verifySignature($signature);

        return response()->json([
            'valid' => $isValid,
            'signer_name' => $signature->signer_name,
            'signed_at' => $signature->signed_at,
            'ip_address' => $signature->ip_address,
        ]);
    }
}