<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Requests\SignContractRequest;
use App\Models\Contract;
use App\Services\Contracts\SignatureService;
use Illuminate\Http\Request;

class ContractSignController extends Controller
{
    public function __construct(protected SignatureService $signatureService) {}

    public function show(string $number)
    {
        $contract = Contract::where('number', $number)->with(['customer', 'signatures'])->firstOrFail();

        if (view()->exists('frontend.contract.show')) {
            return view('frontend.contract.show', compact('contract'));
        }

        return view('admin.contracts.show', compact('contract'));
    }

    public function sign(SignContractRequest $request, string $number)
    {
        $contract = Contract::where('number', $number)->firstOrFail();

        $signature = $this->signatureService->signContract($contract, $request->validated());

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Contract executed successfully.', 'signature' => $signature]);
        }

        toastr()->success('Contract signed successfully!');
        return redirect()->back();
    }
}
