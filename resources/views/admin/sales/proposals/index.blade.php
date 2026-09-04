@extends('admin.layouts.layout')

@section('content')
<section class="section">
    <div class="section-header">
        <h1>Proposals</h1>
        <h1>Proposals & Templates</h1>
        <div class="section-header-button">
            <button type="button" class="btn btn-primary mr-2" data-toggle="modal" data-target="#newProposalModal">
                <i class="fas fa-plus mr-1"></i> New Proposal
            </button>
            <button type="button" class="btn btn-outline-primary" data-toggle="modal" data-target="#newTemplateModal">
                <i class="fas fa-file-alt mr-1"></i> Create Template
            </button>
        </div>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item active"><a href="{{ route('dashboard') }}">Dashboard</a></div>
            <div class="breadcrumb-item"><a href="{{ route('admin.sales.index') }}">Sales</a></div>
            <div class="breadcrumb-item">Proposals</div>
        </div>
    </div>

    <div class="section-body">
        <div class="card">
            <div class="card-body">
                <p>Proposals will appear here.</p>
        <div class="row">
            <!-- Left Side: Proposals Table -->
            <div class="col-lg-7">
                <div class="card shadow-sm">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4><i class="fas fa-scroll mr-2 text-primary"></i> Active Proposals</h4>
                        <span class="badge badge-light border">{{ $proposals->total() }} total</span>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-striped table-md mb-0">
                                <thead>
                                    <tr>
                                        <th>Title</th>
                                        <th>Quote #</th>
                                        <th>Client</th>
                                        <th>Status</th>
                                        <th>Created</th>
                                        <th class="text-right">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($proposals as $prop)
                                    <tr>
                                        <td>
                                            <strong>{{ $prop->title }}</strong>
                                            @if($prop->template)
                                                <div class="text-muted small">Template: {{ $prop->template->name }}</div>
                                            @endif
                                        </td>
                                        <td>
                                            @if($prop->quote)
                                                <a href="{{ route('admin.quotes.show', $prop->quote) }}" class="font-weight-bold">
                                                    {{ $prop->quote->number }}
                                                </a>
                                            @else
                                                <span class="text-muted">N/A</span>
                                            @endif
                                        </td>
                                        <td>
                                            {{ $prop->quote->recipient_name ?? ($prop->customer->name ?? ($prop->lead->name ?? 'N/A')) }}
                                        </td>
                                        <td>
                                            <span class="badge badge-light border">{{ ucfirst($prop->status) }}</span>
                                        </td>
                                        <td class="small text-muted">
                                            {{ $prop->created_at->format('M d, Y') }}
                                        </td>
                                        <td class="text-right">
                                            @if($prop->quote)
                                            <a href="{{ route('admin.quotes.show', $prop->quote) }}" class="btn btn-sm btn-info" title="View Quote & Proposal">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @endif
                                            <form action="{{ route('admin.proposals.destroy', $prop) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete proposal?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4 text-muted">
                                            No proposals generated yet. Create your first proposal from a quote or template.
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @if($proposals->hasPages())
                    <div class="card-footer text-right">
                        {{ $proposals->links() }}
                    </div>
                    @endif
                </div>
            </div>

            <!-- Right Side: Proposal Templates -->
            <div class="col-lg-5">
                <div class="card shadow-sm">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4><i class="fas fa-file-invoice mr-2 text-info"></i> Proposal Templates</h4>
                        <button type="button" class="btn btn-sm btn-outline-info" data-toggle="modal" data-target="#newTemplateModal">
                            <i class="fas fa-plus mr-1"></i> Add
                        </button>
                    </div>
                    <div class="card-body p-0">
                        <ul class="list-group list-group-flush">
                            @forelse($templates as $tpl)
                            <li class="list-group-item">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-1 font-weight-bold">{{ $tpl->name }}</h6>
                                        <span class="badge badge-light border small mr-1">{{ $tpl->category }}</span>
                                        @if($tpl->subject)
                                            <span class="text-muted small">{{ Str::limit($tpl->subject, 30) }}</span>
                                        @endif
                                    </div>
                                    <div>
                                        <button type="button" class="btn btn-sm btn-light border" data-toggle="modal" data-target="#previewTemplateModal{{ $tpl->id }}" title="Preview">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                </div>
                            </li>

                            <!-- Modal: Preview Template -->
                            <div class="modal fade" id="previewTemplateModal{{ $tpl->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                                <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header bg-light">
                                            <h5 class="modal-title">Template Preview: {{ $tpl->name }}</h5>
                                            <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="p-3 border rounded bg-white" style="max-height: 400px; overflow-y: auto;">
                                                {!! nl2br(e($tpl->content)) !!}
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @empty
                            <li class="list-group-item text-center text-muted py-4">
                                No proposal templates available. Create one below to enable automated proposal generation!
                            </li>
                            @endforelse
                        </ul>
                    </div>
                </div>

                <!-- Template Placeholders Reference -->
                <div class="card shadow-sm bg-light">
                    <div class="card-header">
                        <h4><i class="fas fa-code mr-2 text-secondary"></i> Dynamic Placeholders</h4>
                    </div>
                    <div class="card-body small">
                        <p class="text-muted mb-2">Use these placeholders inside templates to automatically inject quote details:</p>
                        <div class="row">
                            <div class="col-6">
                                <code>@{{client_name}}</code><br>
                                <code>@{{client_company}}</code><br>
                                <code>@{{client_email}}</code><br>
                                <code>@{{quote_number}}</code><br>
                            </div>
                            <div class="col-6">
                                <code>@{{total_amount}}</code><br>
                                <code>@{{items_table}}</code><br>
                                <code>@{{valid_until}}</code><br>
                                <code>@{{terms}}</code>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Modal: New Proposal -->
<div class="modal fade" id="newProposalModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <form action="{{ route('admin.proposals.store') }}" method="POST" class="modal-content">
            @csrf
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title text-white"><i class="fas fa-plus-circle mr-2"></i> Create Proposal for Quote</h5>
                <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label class="font-weight-bold">Select Target Quote <span class="text-danger">*</span></label>
                    <select name="quote_id" class="form-control" required>
                        <option value="">-- Choose Quote --</option>
                        @foreach($quotes as $q)
                        <option value="{{ $q->id }}">
                            {{ $q->number }} - {{ $q->recipient_name }} ({{ $q->currency }} {{ number_format($q->total, 2) }})
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="font-weight-bold">Proposal Title <span class="text-danger">*</span></label>
                    <input type="text" name="title" class="form-control" placeholder="e.g. Solution Proposal & Architecture Overview" required>
                </div>
                <div class="form-group">
                    <label class="font-weight-bold">Template</label>
                    <select name="template_id" class="form-control">
                        <option value="">-- Blank / Custom --</option>
                        @foreach($templates as $t)
                        <option value="{{ $t->id }}">{{ $t->name }} ({{ $t->category }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group mb-0">
                    <label class="font-weight-bold">Custom Scope or Narrative (Optional)</label>
                    <textarea name="content" class="form-control" rows="4" placeholder="Add custom terms or scope details..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary font-weight-bold">Generate Proposal</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: New Template -->
<div class="modal fade" id="newTemplateModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <form action="{{ route('admin.proposal-templates.store') }}" method="POST" class="modal-content">
            @csrf
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title text-white"><i class="fas fa-file-alt mr-2"></i> Create Proposal Template</h5>
                <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-7 form-group">
                        <label class="font-weight-bold">Template Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" placeholder="e.g. Full-Stack Web Development Proposal" required>
                    </div>
                    <div class="col-md-5 form-group">
                        <label class="font-weight-bold">Category <span class="text-danger">*</span></label>
                        <select name="category" class="form-control" required>
                            <option value="Web Development">Web Development</option>
                            <option value="Mobile Applications">Mobile Applications</option>
                            <option value="Cloud & DevOps">Cloud & DevOps</option>
                            <option value="UI/UX Product Design">UI/UX Product Design</option>
                            <option value="Consulting & Strategy">Consulting & Strategy</option>
                            <option value="General">General</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="font-weight-bold">Email Subject Line</label>
                    <input type="text" name="subject" class="form-control" placeholder="e.g. Proposal for @{{client_company}} from SAA Digital">
                </div>
                <div class="form-group mb-0">
                    <label class="font-weight-bold">Template Content <span class="text-danger">*</span></label>
                    <textarea name="content" class="form-control" rows="8" placeholder="Enter proposal content with placeholders like @{{client_name}}, @{{quote_number}}, @{{items_table}}, and @{{total_amount}}..." required>## Executive Overview
Thank you for the opportunity to partner with @{{client_company}}. SAA Digital Solutions is pleased to present this engineering proposal and investment summary for quotation @{{quote_number}}.

## Scope of Work & Deliverables
Our dedicated software engineering squad will deliver the following milestones according to modern agile engineering principles:

@{{items_table}}

## Commercial Terms & Investment
- **Grand Total Investment:** @{{total_amount}}
- **Valid Until:** @{{valid_until}}

We look forward to successfully launching your project.
                    </textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-info font-weight-bold">Save Template</button>
            </div>
        </form>
    </div>
</div>
@endsection
