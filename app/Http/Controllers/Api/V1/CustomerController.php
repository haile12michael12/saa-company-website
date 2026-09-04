<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Services\CRM\CustomerService;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $companyId = $request->user()?->company_id;
        $customers = Customer::when($companyId, fn($q) => $q->where('company_id', $companyId))
            ->latest()
            ->paginate($request->get('per_page', 15));

        return response()->json($customers);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string',
            'status' => 'nullable|string',
        ]);

        $validated['company_id'] = $request->user()?->company_id;

        $customer = Customer::create($validated);

        return response()->json($customer, 201);
    }

    public function show(Customer $customer)
    {
        return response()->json($customer->load(['quotes', 'contracts', 'appointments']));
    }

    public function update(Request $request, Customer $customer)
    {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|email|max:255',
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string',
            'status' => 'nullable|string',
        ]);

        $customer->update($validated);

        return response()->json($customer);
    }

    public function destroy(Customer $customer)
    {
        $customer->delete();
        return response()->json(['message' => 'Customer deleted.']);
    }
}