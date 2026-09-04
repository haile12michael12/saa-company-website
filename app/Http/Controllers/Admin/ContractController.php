<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreContractRequest;
use App\Http\Requests\UpdateContractRequest;
use App\Models\Contract;
use App\Models\Customer;
use App\Models\Quote;
use App\Services\Contracts\ContractService;
use Illuminate\Http\Request;

class ContractController extends Controller
{
    public function __construct(protected ContractService $contractService) {}

    public function index(Request $request)
    {
        $this->authorize('viewAny', Contract::class);

        $contracts = $this->contractService->getContractsForCompany(
            auth()->user()->company_id,
            $request->only(['status', 'customer_id', 'search', 'per_page'])
        );

        $customers = Customer::where('company_id', auth()->user()->company_id)->get();
        $quotes = Quote::where('company_id', auth()->user()->company_id)->where('status', 'accepted')->get();

        if (view()->exists('admin.contracts.index')) {
            return view('admin.contracts.index', compact('contracts', 'customers', 'quotes'));
        }

        return response()->json($contracts);
    }

    public function store(StoreContractRequest $request)
    {
        $this->authorize('create', Contract::class);

        $contract = $this->contractService->createContract(
            $request->validated(),
            auth()->user()->company_id,
            auth()->user()
        );

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Contract created successfully.', 'contract' => $contract], 201);
        }

        toastr()->success('Contract created successfully.');
        return redirect()->route('admin.contracts.show', $contract);
    }

    public function generateFromQuote(Request $request, Quote $quote)
    {
        $this->authorize('create', Contract::class);

        $contract = $this->contractService->generateFromQuote($quote, $request->all());

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Contract generated from quote.', 'contract' => $contract], 201);
        }

        toastr()->success("Contract {$contract->number} generated from quote.");
        return redirect()->route('admin.contracts.show', $contract);
    }

    public function show(Contract $contract)
    {
        $this->authorize('view', $contract);

        $contract->load(['customer', 'quote', 'signatures.user']);

        if (request()->wantsJson()) {
            return response()->json($contract);
        }

        return view('admin.contracts.show', compact('contract'));
    }

    public function update(UpdateContractRequest $request, Contract $contract)
    {
        $this->authorize('update', $contract);

        $updated = $this->contractService->updateContract($contract, $request->validated());

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Contract updated successfully.', 'contract' => $updated]);
        }

        toastr()->success('Contract updated successfully.');
        return redirect()->back();
    }

    public function send(Contract $contract)
    {
        $this->authorize('update', $contract);

        $this->contractService->sendContract($contract);

        if (request()->wantsJson()) {
            return response()->json(['message' => 'Contract sent for signing.']);
        }

        toastr()->success('Contract sent to client for signing.');
        return redirect()->back();
    }

    public function destroy(Contract $contract)
    {
        $this->authorize('delete', $contract);

        $this->contractService->voidContract($contract, 'Voided by administrator');

        if (request()->wantsJson()) {
            return response()->json(['message' => 'Contract voided successfully.']);
        }

        toastr()->success('Contract voided.');
        return redirect()->back();
    }
}