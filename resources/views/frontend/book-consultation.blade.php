@extends('frontend.layouts.layout')

@section('content')

<header class="site-header parallax-bg">
    <div class="container">
        <div class="row d-flex align-items-center">
            <div class="col-sm-8">
                <h2 class="title">Book Consultation</h2>
            </div>
            <div class="col-sm-4">
                <div class="breadcrumbs">
                    <ul>
                        <li><a href="{{url('/')}}">Home</a></li>
                        <li>Consultation</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</header>

<section class="section-padding">
    <div class="container">
        <div class="row g-5">
            <!-- Form Column -->
            <div class="col-lg-8">
                @if(session('success'))
                <div class="alert alert-success border-0 shadow-sm p-4 mb-4 rounded-3 text-center">
                    <div class="mb-2 text-success" style="font-size: 32px;"><i class="fal fa-calendar-check"></i></div>
                    <h4 class="h5 fw-bold mb-2">Session Scheduled!</h4>
                    <p class="mb-0">{{ session('success') }}</p>
                </div>
                @endif

                <div class="card border-0 shadow-sm p-4 p-md-5" style="border-radius: 20px; background: #ffffff;">
                    <div class="mb-4">
                        <span class="text-uppercase fw-bold text-primary" style="letter-spacing: 1px; font-size: 13px;">Free 30-Minute Discovery Session</span>
                        <h3 class="h3 fw-bold text-dark mt-1">Schedule Your Strategy Call</h3>
                        <p class="text-muted">Speak directly with a senior solutions architect to explore technical feasibility, architecture options, and project roadmaps.</p>
                    </div>

                    <form action="{{route('consultation.submit')}}" method="POST" id="consultation-form">
                        @csrf

                        <!-- Personal / Contact Details -->
                        <h5 class="fw-bold mb-3 text-dark border-bottom pb-2"><i class="fal fa-user-circle me-2 text-primary"></i> 1. Your Information</h5>
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-medium text-dark small">Full Name *</label>
                                <input type="text" name="name" class="form-control form-control-lg fs-6" placeholder="e.g. Maya Lin" value="{{old('name')}}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-medium text-dark small">Email Address *</label>
                                <input type="email" name="email" class="form-control form-control-lg fs-6" placeholder="e.g. maya@enterprise.com" value="{{old('email')}}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-medium text-dark small">Phone Number *</label>
                                <input type="text" name="phone" class="form-control form-control-lg fs-6" placeholder="e.g. +1 (555) 987-6543" value="{{old('phone')}}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-medium text-dark small">Company / Project Name</label>
                                <input type="text" name="organization" class="form-control form-control-lg fs-6" placeholder="e.g. CloudScale Corp" value="{{old('organization')}}">
                            </div>
                        </div>

                        <!-- Meeting Logistics -->
                        <h5 class="fw-bold mb-3 text-dark border-bottom pb-2"><i class="fal fa-calendar-alt me-2 text-primary"></i> 2. Meeting Preferences</h5>
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-medium text-dark small">Consultation Topic *</label>
                                <select name="topic" class="form-select form-select-lg fs-6" required>
                                    <option value="New Product Architecture & Scoping">New Product Architecture & Scoping</option>
                                    <option value="Web & SaaS Platform Development">Web & SaaS Platform Development</option>
                                    <option value="Mobile App (iOS/Android) Strategy">Mobile App (iOS/Android) Strategy</option>
                                    <option value="AI Assistant & Workflow Automation">AI Assistant & Workflow Automation</option>
                                    <option value="Cloud Infrastructure & DevOps Scaling">Cloud Infrastructure & DevOps Scaling</option>
                                    <option value="Codebase Audit & Modernization">Codebase Audit & Modernization</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-medium text-dark small">Preferred Meeting Format *</label>
                                <select name="meeting_format" class="form-select form-select-lg fs-6" required>
                                    <option value="Google Meet" selected>Google Meet (Video Conference)</option>
                                    <option value="Zoom">Zoom Meeting</option>
                                    <option value="Phone Call">Direct Phone Call</option>
                                    <option value="In-Person Office">In-Person at Our Office</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-medium text-dark small">Preferred Date *</label>
                                <input type="date" name="date" class="form-control form-control-lg fs-6" min="{{date('Y-m-d')}}" value="{{old('date', date('Y-m-d', strtotime('+1 day')))}}" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-medium text-dark small">Preferred Time Window *</label>
                                <select name="time_slot" class="form-select form-select-lg fs-6" required>
                                    <option value="09:00 AM - 10:00 AM">Morning: 09:00 AM – 10:00 AM</option>
                                    <option value="11:00 AM - 12:00 PM">Late Morning: 11:00 AM – 12:00 PM</option>
                                    <option value="02:00 PM - 03:00 PM" selected>Afternoon: 02:00 PM – 03:00 PM</option>
                                    <option value="04:00 PM - 05:00 PM">Late Afternoon: 04:00 PM – 05:00 PM</option>
                                </select>
                            </div>
                        </div>

                        <!-- Agenda Notes -->
                        <h5 class="fw-bold mb-3 text-dark border-bottom pb-2"><i class="fal fa-comment-alt-lines me-2 text-primary"></i> 3. What would you like to achieve?</h5>
                        <div class="mb-4">
                            <label class="form-label fw-medium text-dark small">Goals & Questions</label>
                            <textarea name="notes" class="form-control fs-6 p-3" rows="4" placeholder="Briefly describe what you'd like to review during this call (e.g. technology trade-offs, budget expectations, timeline viability)...">{{old('notes')}}</textarea>
                        </div>

                        <div class="text-center pt-2">
                            <button type="submit" id="book_submit_btn" class="button-primary mouse-dir px-5 py-3 text-white">
                                <span class="text">Confirm Consultation Booking</span>
                                <span class="dir-part"></span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <div class="sticky-top" style="top: 100px;">
                    <div class="card border-0 p-4 shadow-sm mb-4" style="border-radius: 16px; background: #fafafa;">
                        <h5 class="fw-bold mb-3 text-dark">What's Covered in 30 Min:</h5>
                        <ul class="list-unstyled mb-0">
                            <li class="mb-3 d-flex align-items-start">
                                <i class="fal fa-check-circle text-success mt-1 me-2"></i>
                                <span class="small text-muted"><strong>Feasibility Review:</strong> We analyze your concept against technical, security, and timeline constraints.</span>
                            </li>
                            <li class="mb-3 d-flex align-items-start">
                                <i class="fal fa-check-circle text-success mt-1 me-2"></i>
                                <span class="small text-muted"><strong>Stack Recommendations:</strong> Learn the optimal frameworks, cloud providers, and databases for your scale.</span>
                            </li>
                            <li class="mb-3 d-flex align-items-start">
                                <i class="fal fa-check-circle text-success mt-1 me-2"></i>
                                <span class="small text-muted"><strong>Realistic Budgets:</strong> Clear ballparks on engineering time, MVP scope, and production costs.</span>
                            </li>
                            <li class="d-flex align-items-start">
                                <i class="fal fa-check-circle text-success mt-1 me-2"></i>
                                <span class="small text-muted"><strong>Zero Obligation:</strong> Actionable engineering insights whether or not you choose to partner with us.</span>
                            </li>
                        </ul>
                    </div>

                    <div class="card border-0 p-4 shadow-sm text-center" style="border-radius: 16px; background: #ffffff;">
                        <div class="text-warning mb-2" style="font-size: 20px;">
                            <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
                        </div>
                        <p class="small text-muted fst-italic mb-2">"The initial 30-minute call saved us months of architecture missteps. Incredible technical clarity."</p>
                        <span class="small fw-bold text-dark">Sarah Jenkins — CTO, Fintech Corp</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection
