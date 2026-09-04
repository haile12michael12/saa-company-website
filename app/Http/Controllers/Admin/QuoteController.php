<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\QuoteMail;
use App\Models\Customer;
use App\Models\Lead;
use App\Models\Project;
use App\Models\ProposalTemplate;
use App\Models\Quote;
use App\Models\QuoteItem;
use App\Services\QuotePdfService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class QuoteController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', Quote::class);

        $status = $request->query('status', 'all');
        $search = $request->query('search');

        $companyId = auth()->user()->company_id;

        $baseQuery = Quote::query();
        if ($companyId) {
            $baseQuery->where('company_id', $companyId);
        }

        // Status counts for filter tabs
        $statusCounts = [
            'all' => (clone $baseQuery)->count(),
            'draft' => (clone $baseQuery)->where('status', 'draft')->count(),
            'pending_approval' => (clone $baseQuery)->where('status', 'pending_approval')->count(),
            'approved' => (clone $baseQuery)->where('status', 'approved')->count(),
            'sent' => (clone $baseQuery)->where('status', 'sent')->count(),
            'accepted' => (clone $baseQuery)->where('status', 'accepted')->count(),
            'rejected' => (clone $baseQuery)->where('status', 'rejected')->count(),
            'expired' => (clone $baseQuery)->where('valid_until', '<', Carbon::today())->whereNotIn('status', ['accepted'])->count(),
        ];

        $query = (clone $baseQuery)->with(['items', 'customer', 'lead']);

        if ($status !== 'all') {
            if ($status === 'expired') {
                $query->where('valid_until', '<', Carbon::today())->whereNotIn('status', ['accepted']);
            } else {
                $query->where('status', $status);
            }
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('number', 'like', "%{$search}%")
                    ->orWhere('title', 'like', "%{$search}%")
                    ->orWhereHas('customer', fn ($c) => $c->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%"))
                    ->orWhereHas('lead', fn ($l) => $l->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%"));
            });
        }

        $quotes = $query->latest()->paginate(15)->withQueryString();

        return view('admin.sales.quotes.index', compact('quotes', 'statusCounts', 'status', 'search'));
    }

    public function create(Request $request)
    {
        $this->authorize('create', Quote::class);

        $companyId = auth()->user()->company_id;

        $leadsQuery = Lead::query();
        $customersQuery = Customer::query();

        if ($companyId) {
            $leadsQuery->where('company_id', $companyId);
            $customersQuery->where('company_id', $companyId);
        }

        $leads = $leadsQuery->latest()->get();
        $customers = $customersQuery->latest()->get();

        $selectedLeadId = $request->query('lead_id');
        $selectedCustomerId = $request->query('customer_id');

        $selectedLead = $selectedLeadId ? Lead::find($selectedLeadId) : null;
        $selectedCustomer = $selectedCustomerId ? Customer::find($selectedCustomerId) : null;

        $nextNumber = 'QT-' . date('Y') . '-' . strtoupper(Str::random(6));

        return view('admin.sales.quotes.create', compact(
            'leads',
            'customers',
            'selectedLead',
            'selectedCustomer',
            'nextNumber'
        ));
    }

    public function store(Request $request)
    {
        $this->authorize('create', Quote::class);

        $validated = $request->validate([
            'number' => ['required', 'string', 'max:50', 'unique:quotes,number'],
            'title' => ['nullable', 'string', 'max:255'],
            'customer_id' => ['nullable', 'exists:customers,id'],
            'lead_id' => ['nullable', 'exists:leads,id'],
            'valid_until' => ['nullable', 'date'],
            'currency' => ['nullable', 'string', 'max:10'],
            'discount_type' => ['nullable', 'in:percentage,fixed'],
            'discount_rate' => ['nullable', 'numeric', 'min:0'],
            'tax_rate' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string'],
            'terms' => ['nullable', 'string'],
            'submit_action' => ['nullable', 'string'], // 'save_draft' or 'submit_approval'
            'items' => ['required', 'array', 'min:1'],
            'items.*.description' => ['required', 'string', 'max:500'],
            'items.*.quantity' => ['required', 'numeric', 'min:1'],
            'items.*.unit_price' => ['required', 'numeric', 'min:0'],
        ]);

        $status = ($request->input('submit_action') === 'submit_approval') ? 'pending_approval' : 'draft';

        $quote = DB::transaction(function () use ($validated, $status) {
            $quote = Quote::create([
                'company_id' => auth()->user()->company_id,
                'customer_id' => $validated['customer_id'] ?? null,
                'lead_id' => $validated['lead_id'] ?? null,
                'number' => $validated['number'],
                'title' => $validated['title'] ?? null,
                'status' => $status,
                'currency' => $validated['currency'] ?? 'USD',
                'valid_until' => $validated['valid_until'] ? Carbon::parse($validated['valid_until']) : Carbon::now()->addDays(30),
                'discount_type' => $validated['discount_type'] ?? 'fixed',
                'discount_rate' => (float) ($validated['discount_rate'] ?? 0),
                'tax_rate' => (float) ($validated['tax_rate'] ?? 0),
                'notes' => $validated['notes'] ?? null,
                'terms' => $validated['terms'] ?? null,
                'token' => Str::random(40),
            ]);

            foreach ($validated['items'] as $itemData) {
                $qty = (int) $itemData['quantity'];
                $unitPrice = (float) $itemData['unit_price'];
                $itemTotal = $qty * $unitPrice;

                QuoteItem::create([
                    'quote_id' => $quote->id,
                    'description' => $itemData['description'],
                    'quantity' => $qty,
                    'unit_price' => $unitPrice,
                    'total' => $itemTotal,
                ]);
            }

            $quote->recalculateTotals(true);

            return $quote;
        });

        toastr()->success('Quote created successfully!');

        return redirect()->route('admin.quotes.show', $quote);
    }

    public function show(Quote $quote)
    {
        $this->authorize('view', $quote);

        $quote->load(['items', 'customer', 'lead', 'approver', 'project', 'proposals.template']);

        $proposalTemplates = ProposalTemplate::where('is_active', true)->get();

        return view('admin.sales.quotes.show', compact('quote', 'proposalTemplates'));
    }

    public function edit(Quote $quote)
    {
        $this->authorize('update', $quote);

        $quote->load(['items', 'customer', 'lead']);

        $leads = Lead::latest()->get();
        $customers = Customer::latest()->get();

        return view('admin.sales.quotes.create', [
            'quote' => $quote,
            'leads' => $leads,
            'customers' => $customers,
            'selectedLead' => $quote->lead,
            'selectedCustomer' => $quote->customer,
            'nextNumber' => $quote->number,
            'isEdit' => true,
        ]);
    }

    public function update(Request $request, Quote $quote)
    {
        $this->authorize('update', $quote);

        $validated = $request->validate([
            'number' => ['required', 'string', 'max:50', 'unique:quotes,number,' . $quote->id],
            'title' => ['nullable', 'string', 'max:255'],
            'customer_id' => ['nullable', 'exists:customers,id'],
            'lead_id' => ['nullable', 'exists:leads,id'],
            'valid_until' => ['nullable', 'date'],
            'currency' => ['nullable', 'string', 'max:10'],
            'discount_type' => ['nullable', 'in:percentage,fixed'],
            'discount_rate' => ['nullable', 'numeric', 'min:0'],
            'tax_rate' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string'],
            'terms' => ['nullable', 'string'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.description' => ['required', 'string', 'max:500'],
            'items.*.quantity' => ['required', 'numeric', 'min:1'],
            'items.*.unit_price' => ['required', 'numeric', 'min:0'],
        ]);

        DB::transaction(function () use ($quote, $validated) {
            $quote->update([
                'customer_id' => $validated['customer_id'] ?? null,
                'lead_id' => $validated['lead_id'] ?? null,
                'number' => $validated['number'],
                'title' => $validated['title'] ?? null,
                'currency' => $validated['currency'] ?? 'USD',
                'valid_until' => !empty($validated['valid_until']) ? Carbon::parse($validated['valid_until']) : null,
                'discount_type' => $validated['discount_type'] ?? 'fixed',
                'discount_rate' => (float) ($validated['discount_rate'] ?? 0),
                'tax_rate' => (float) ($validated['tax_rate'] ?? 0),
                'notes' => $validated['notes'] ?? null,
                'terms' => $validated['terms'] ?? null,
            ]);

            // Re-sync items
            $quote->items()->delete();

            foreach ($validated['items'] as $itemData) {
                $qty = (int) $itemData['quantity'];
                $unitPrice = (float) $itemData['unit_price'];

                QuoteItem::create([
                    'quote_id' => $quote->id,
                    'description' => $itemData['description'],
                    'quantity' => $qty,
                    'unit_price' => $unitPrice,
                    'total' => $qty * $unitPrice,
                ]);
            }

            $quote->recalculateTotals(true);
        });

        toastr()->success('Quote updated successfully!');

        return redirect()->route('admin.quotes.show', $quote);
    }

    public function destroy(Quote $quote)
    {
        $this->authorize('delete', $quote);

        $quote->items()->delete();
        $quote->delete();

        toastr()->success('Quote deleted successfully.');

        return redirect()->route('admin.quotes.index');
    }

    public function approve(Request $request, Quote $quote)
    {
        $this->authorize('approve', $quote);

        $quote->update([
            'status' => 'approved',
            'approved_at' => Carbon::now(),
            'approved_by' => auth()->id(),
        ]);

        toastr()->success("Quote #{$quote->number} has been internally approved!");

        return redirect()->back();
    }

    public function sendEmail(Request $request, Quote $quote)
    {
        $this->authorize('sendEmail', $quote);

        $recipientEmail = $quote->recipient_email;

        if (!$recipientEmail) {
            toastr()->error('No recipient email address associated with this quote.');
            return redirect()->back();
        }

        $customMessage = $request->input('email_message');

        try {
            Mail::to($recipientEmail)->send(new QuoteMail($quote, $customMessage));

            $quote->update([
                'status' => 'sent',
                'sent_at' => Carbon::now(),
            ]);

            toastr()->success("Quotation #{$quote->number} successfully sent to {$recipientEmail}!");
        } catch (\Throwable $e) {
            toastr()->error('Failed to send email: ' . $e->getMessage());
        }

        return redirect()->back();
    }

    public function accept(Request $request, Quote $quote)
    {
        $this->authorize('accept', $quote);

        $quote->update([
            'status' => 'accepted',
            'accepted_at' => Carbon::now(),
        ]);

        toastr()->success("Quote #{$quote->number} marked as ACCEPTED!");

        return redirect()->back();
    }

    public function reject(Request $request, Quote $quote)
    {
        $this->authorize('reject', $quote);

        $reason = $request->input('reason', 'Client declined the proposed estimate.');

        $quote->update([
            'status' => 'rejected',
            'rejected_at' => Carbon::now(),
            'rejection_reason' => $reason,
        ]);

        toastr()->info("Quote #{$quote->number} marked as REJECTED.");

        return redirect()->back();
    }

    public function pdf(Quote $quote, QuotePdfService $pdfService)
    {
        $this->authorize('view', $quote);

        return $pdfService->downloadResponse($quote);
    }

    public function convertToCustomer(Quote $quote)
    {
        $this->authorize('convertToCustomer', $quote);

        $lead = $quote->lead;

        if (!$lead) {
            toastr()->error('This quote is not linked to a lead.');
            return redirect()->back();
        }

        $customer = Customer::create([
            'company_id' => $quote->company_id,
            'name' => $lead->name,
            'email' => $lead->email,
            'phone' => $lead->phone,
            'address' => null,
            'status' => 'active',
        ]);

        $quote->update(['customer_id' => $customer->id]);
        $lead->update(['customer_id' => $customer->id, 'status' => 'converted']);

        toastr()->success("Successfully converted lead into Customer '{$customer->name}'!");

        return redirect()->route('admin.quotes.show', $quote);
    }

    public function convertToProject(Quote $quote)
    {
        $this->authorize('convertToProject', $quote);

        // If no customer yet, auto-create one from the lead
        $customerId = $quote->customer_id;
        if (!$customerId && $quote->lead) {
            $customer = Customer::create([
                'company_id' => $quote->company_id,
                'name' => $quote->lead->name,
                'email' => $quote->lead->email,
                'phone' => $quote->lead->phone,
                'status' => 'active',
            ]);
            $customerId = $customer->id;
            $quote->update(['customer_id' => $customerId]);
        }

        $projectName = $quote->title ?: "Project for " . $quote->recipient_name;

        $project = Project::create([
            'company_id' => $quote->company_id,
            'customer_id' => $customerId,
            'name' => $projectName,
            'status' => 'planned',
            'description' => "Created from accepted Quote #{$quote->number}.\n\n" . ($quote->notes ?? ''),
            'starts_at' => Carbon::today(),
            'ends_at' => Carbon::today()->addDays(60),
            'budget' => $quote->total,
        ]);

        $quote->update(['project_id' => $project->id]);

        toastr()->success("Successfully launched Project '{$project->name}' with budget {$quote->currency} " . number_format($quote->total, 2) . "!");

        return redirect()->route('admin.quotes.show', $quote);
    }
}