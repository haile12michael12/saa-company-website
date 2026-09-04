<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Proposal;
use App\Models\ProposalTemplate;
use App\Models\Quote;
use Illuminate\Http\Request;

class ProposalController extends Controller
{
    public function index() {}
    public function create() {}
    public function store(Request $request) {}
    public function show(string $id) {}
    public function edit(string $id) {}
    public function update(Request $request, string $id) {}
    public function destroy(string $id) {}
    public function index(Request $request)
    {
        $this->authorize('viewAny', Proposal::class);

        $companyId = auth()->user()->company_id;

        $proposalsQuery = Proposal::with(['quote', 'customer', 'lead', 'template', 'creator']);
        $templatesQuery = ProposalTemplate::query();

        if ($companyId) {
            $proposalsQuery->where('company_id', $companyId);
            $templatesQuery->where('company_id', $companyId);
        }

        $proposals = $proposalsQuery->latest()->paginate(10);
        $templates = $templatesQuery->latest()->get();
        $quotes = Quote::latest()->take(20)->get();

        return view('admin.sales.proposals.index', compact('proposals', 'templates', 'quotes'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', Proposal::class);

        $validated = $request->validate([
            'quote_id' => ['required', 'exists:quotes,id'],
            'title' => ['required', 'string', 'max:255'],
            'template_id' => ['nullable', 'exists:proposal_templates,id'],
            'content' => ['nullable', 'string'],
        ]);

        $quote = Quote::findOrFail($validated['quote_id']);
        $content = $validated['content'] ?? '';

        if (!empty($validated['template_id'])) {
            $template = ProposalTemplate::findOrFail($validated['template_id']);
            $content = $template->render($quote);
        }

        $proposal = Proposal::create([
            'company_id' => auth()->user()->company_id,
            'quote_id' => $quote->id,
            'lead_id' => $quote->lead_id,
            'customer_id' => $quote->customer_id,
            'template_id' => $validated['template_id'] ?? null,
            'title' => $validated['title'],
            'content' => $content,
            'status' => 'draft',
            'created_by' => auth()->id(),
        ]);

        toastr()->success("Proposal '{$proposal->title}' generated successfully!");

        return redirect()->route('admin.quotes.show', $quote);
    }

    public function storeTemplate(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'subject' => ['nullable', 'string', 'max:255'],
            'category' => ['required', 'string', 'max:100'],
            'content' => ['required', 'string'],
        ]);

        $template = ProposalTemplate::create([
            'company_id' => auth()->user()->company_id,
            'name' => $validated['name'],
            'subject' => $validated['subject'] ?? null,
            'category' => $validated['category'],
            'content' => $validated['content'],
            'is_active' => true,
        ]);

        toastr()->success("Proposal Template '{$template->name}' created successfully!");

        return redirect()->route('admin.proposals.index');
    }

    public function updateTemplate(Request $request, ProposalTemplate $template)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'subject' => ['nullable', 'string', 'max:255'],
            'category' => ['required', 'string', 'max:100'],
            'content' => ['required', 'string'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $template->update([
            'name' => $validated['name'],
            'subject' => $validated['subject'] ?? null,
            'category' => $validated['category'],
            'content' => $validated['content'],
            'is_active' => $request->has('is_active') ? (bool) $request->is_active : true,
        ]);

        toastr()->success("Proposal Template updated successfully!");

        return redirect()->route('admin.proposals.index');
    }

    public function destroy(Proposal $proposal)
    {
        $this->authorize('delete', $proposal);

        $proposal->delete();

        toastr()->success('Proposal deleted.');

        return redirect()->back();
    }
}