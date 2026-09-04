@extends('frontend.layouts.layout')

@section('content')

<header class="site-header parallax-bg">
    <div class="container">
        <div class="row d-flex align-items-center">
            <div class="col-sm-8">
                <h2 class="title">Request a Quotation</h2>
            </div>
            <div class="col-sm-4">
                <div class="breadcrumbs">
                    <ul>
                        <li><a href="{{url('/')}}">Home</a></li>
                        <li>Quote Request</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</header>

<section class="section-padding">
    <div class="container">
        <div class="row g-5">
            <!-- Form Area -->
            <div class="col-lg-8">
                @if(session('success'))
                <div class="alert alert-success border-0 shadow-sm p-4 mb-4 rounded-3 text-center">
                    <div class="mb-2 text-success" style="font-size: 32px;"><i class="fal fa-check-circle"></i></div>
                    <h4 class="h5 fw-bold mb-2">Quote Request Received!</h4>
                    <p class="mb-2">{{ session('success') }}</p>
                    @if(session('quote_number'))
                    <span class="badge bg-primary px-3 py-2" style="font-size: 15px;">Reference: {{ session('quote_number') }}</span>
                    @endif
                </div>
                @endif

                <div class="card border-0 shadow-sm p-4 p-md-5" style="border-radius: 20px; background: #ffffff;">
                    <div class="mb-4">
                        <span class="text-uppercase fw-bold text-primary" style="letter-spacing: 1px; font-size: 13px;">Tailored Project Estimate</span>
                        <h3 class="h3 fw-bold text-dark mt-1">Tell Us About Your Project</h3>
                        <p class="text-muted">Provide your specifications below and our engineering leads will prepare a comprehensive quotation within 24 to 48 hours.</p>
                    </div>

                    <form action="{{route('quote.submit')}}" method="POST" id="quote-request-form">
                        @csrf

                        <!-- Client Details -->
                        <h5 class="fw-bold mb-3 text-dark border-bottom pb-2"><i class="fal fa-user me-2 text-primary"></i> 1. Contact Information</h5>
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-medium text-dark small">Full Name *</label>
                                <input type="text" name="name" class="form-control form-control-lg fs-6" placeholder="e.g. Alexander Hayes" value="{{old('name')}}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-medium text-dark small">Business Email *</label>
                                <input type="email" name="email" class="form-control form-control-lg fs-6" placeholder="e.g. alex@company.com" value="{{old('email')}}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-medium text-dark small">Phone Number</label>
                                <input type="text" name="phone" class="form-control form-control-lg fs-6" placeholder="e.g. +1 (555) 019-2834" value="{{old('phone')}}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-medium text-dark small">Company / Organization</label>
                                <input type="text" name="company" class="form-control form-control-lg fs-6" placeholder="e.g. Apex Innovations Ltd" value="{{old('company')}}">
                            </div>
                        </div>

                        <!-- Project Scope -->
                        <h5 class="fw-bold mb-3 text-dark border-bottom pb-2"><i class="fal fa-sliders-h me-2 text-primary"></i> 2. Project Parameters</h5>
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-medium text-dark small">Primary Service</label>
                                <select name="service_id" class="form-select form-select-lg fs-6">
                                    <option value="">Select relevant service</option>
                                    @foreach($services as $srv)
                                    <option value="{{$srv->name}}" {{($selectedService == $srv->slug || $selectedService == $srv->name) ? 'selected' : ''}}>
                                        {{$srv->name}}
                                    </option>
                                    @endforeach
                                    <option value="Custom Enterprise Solution">Other / Custom Enterprise Solution</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-medium text-dark small">Project Type *</label>
                                <select name="project_type" class="form-select form-select-lg fs-6" required>
                                    <option value="New Product / MVP Build">New Product / MVP Build</option>
                                    <option value="Major Redesign & Modernization">Major Redesign & Modernization</option>
                                    <option value="Feature Expansion & Scaling">Feature Expansion & Scaling</option>
                                    <option value="AI Integration & Automation">AI Integration & Automation</option>
                                    <option value="Enterprise Systems Migration">Enterprise Systems Migration</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-medium text-dark small">Estimated Budget Range *</label>
                                <select name="budget_range" class="form-select form-select-lg fs-6" required>
                                    <option value="Under $2,500">Under $2,500</option>
                                    <option value="$2,500 - $5,000">$2,500 – $5,000</option>
                                    <option value="$5,000 - $10,000" selected>$5,000 – $10,000</option>
                                    <option value="$10,000 - $25,000">$10,000 – $25,000</option>
                                    <option value="$25,000+">$25,000+</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-medium text-dark small">Desired Timeline *</label>
                                <select name="timeline" class="form-select form-select-lg fs-6" required>
                                    <option value="Urgent (< 1 Month)">Urgent (< 1 Month)</option>
                                    <option value="1 - 2 Months" selected>1 – 2 Months</option>
                                    <option value="2 - 4 Months">2 – 4 Months</option>
                                    <option value="Flexible / Long-term">Flexible / Long-term</option>
                                </select>
                            </div>
                        </div>

                        <!-- Requirements -->
                        <h5 class="fw-bold mb-3 text-dark border-bottom pb-2"><i class="fal fa-file-alt me-2 text-primary"></i> 3. Project Requirements</h5>
                        <div class="mb-4">
                            <label class="form-label fw-medium text-dark small">Scope & Deliverables *</label>
                            <textarea name="description" class="form-control fs-6 p-3" rows="5" placeholder="Describe the problem you are solving, core features needed, target audience, and any technical preferences or integrations..." required>{{old('description')}}</textarea>
                        </div>

                        <div class="text-center pt-2">
                            <button type="submit" id="quote_submit_btn" class="button-primary mouse-dir px-5 py-3 text-white">
                                <span class="text">Submit Quotation Request</span>
                                <span class="dir-part"></span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Sidebar Info -->
            <div class="col-lg-4">
                <div class="sticky-top" style="top: 100px;">
                    <div class="card border-0 p-4 shadow-sm mb-4" style="border-radius: 16px; background: #fafafa;">
                        <h5 class="fw-bold mb-3 text-dark">What Happens Next?</h5>
                        <div class="d-flex mb-3">
                            <div class="badge bg-primary text-white me-3 rounded-circle" style="width: 28px; height: 28px; line-height: 20px;">1</div>
                            <div>
                                <h6 class="fw-bold mb-1">Architecture Review</h6>
                                <p class="small text-muted mb-0">Our solution architect reviews your scope, dependencies, and timeline.</p>
                            </div>
                        </div>
                        <div class="d-flex mb-3">
                            <div class="badge bg-primary text-white me-3 rounded-circle" style="width: 28px; height: 28px; line-height: 20px;">2</div>
                            <div>
                                <h6 class="fw-bold mb-1">Tailored Proposal</h6>
                                <p class="small text-muted mb-0">You receive a breakdown of sprint milestones, deliverables, and fixed costs.</p>
                            </div>
                        </div>
                        <div class="d-flex">
                            <div class="badge bg-primary text-white me-3 rounded-circle" style="width: 28px; height: 28px; line-height: 20px;">3</div>
                            <div>
                                <h6 class="fw-bold mb-1">Kickoff Session</h6>
                                <p class="small text-muted mb-0">We align on development sprints, code repository setup, and start building.</p>
                            </div>
                        </div>
                    </div>

                    <div class="card border-0 p-4 shadow-sm" style="border-radius: 16px; background: linear-gradient(135deg, #190844 0%, #2a165b 100%); color: #fff;">
                        <h5 class="fw-bold text-white mb-2">Prefer to Talk Live?</h5>
                        <p class="small text-light mb-3">Skip the form and schedule a 30-minute discovery consultation directly with our engineering lead.</p>
                        <a href="{{url('/book-consultation')}}" class="button-primary-trans mouse-dir text-center py-2 text-white border-white text-decoration-none">
                            <span class="text">Book Free Consultation</span>
                            <span class="dir-part"></span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection
