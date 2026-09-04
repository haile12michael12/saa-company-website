@extends('frontend.layouts.layout')

@section('content')

<header class="site-header parallax-bg">
    <div class="container">
        <div class="row d-flex align-items-center">
            <div class="col-sm-8">
                <h2 class="title">{{$item->name}}</h2>
            </div>
            <div class="col-sm-4">
                <div class="breadcrumbs">
                    <ul>
                        <li><a href="{{url('/')}}">Home</a></li>
                        <li><a href="{{url('/services')}}">Services</a></li>
                        <li>Details</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</header>

<section class="section-padding">
    <div class="container">
        <div class="row g-5">
            <!-- Main Content Area -->
            <div class="col-lg-8">
                <div class="service-details-content">
                    <div class="d-flex align-items-center mb-4">
                        <div class="icon-badge text-center me-3" style="width: 70px; height: 70px; line-height: 70px; background: rgba(255, 136, 94, 0.12); border-radius: 16px; color: #ff885e; font-size: 30px;">
                            <i class="{{$item->icon ?? 'fal fa-layer-group'}}"></i>
                        </div>
                        <div>
                            <span class="text-uppercase fw-bold text-primary" style="letter-spacing: 1px; font-size: 13px;">Specialized Service</span>
                            <h1 class="h3 fw-bold mb-0 text-dark">{{$item->name}}</h1>
                        </div>
                    </div>

                    <div class="lead text-muted mb-4" style="line-height: 1.8; font-size: 18px;">
                        {{$item->description}}
                    </div>

                    <div class="service-long-desc mb-5" style="color: #4a4a4a; line-height: 1.8; font-size: 16px;">
                        {!! $item->long_description ?? '<p>Our engineering team delivers resilient, user-centric software architectures tailored to scale with your enterprise.</p>' !!}
                    </div>

                    @php
                        $features = is_array($item->features) ? $item->features : json_decode($item->features, true);
                    @endphp

                    @if(!empty($features))
                    <div class="mb-5 p-4 rounded-3 bg-light border">
                        <h4 class="h5 fw-bold mb-4 text-dark"><i class="fal fa-clipboard-check text-success me-2"></i> What's Included & Key Deliverables</h4>
                        <div class="row g-3">
                            @foreach($features as $feature)
                            <div class="col-md-6">
                                <div class="d-flex align-items-start p-2 bg-white rounded border-sm shadow-xs">
                                    <i class="fal fa-check text-primary mt-1 me-2" style="font-weight: 900;"></i>
                                    <span class="text-dark fw-medium">{{$feature}}</span>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <!-- Engineering & Delivery Workflow -->
                    <div class="delivery-workflow mb-5">
                        <h4 class="h5 fw-bold mb-4 text-dark"><i class="fal fa-route text-warning me-2"></i> Our Delivery Process</h4>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="p-3 border rounded-3 h-100 bg-white shadow-sm">
                                    <div class="fw-bold text-primary mb-1">01. Discovery & Technical Scoping</div>
                                    <p class="small text-muted mb-0">We analyze functional requirements, user personas, API dependencies, and performance benchmarks.</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="p-3 border rounded-3 h-100 bg-white shadow-sm">
                                    <div class="fw-bold text-primary mb-1">02. Architecture & Wireframing</div>
                                    <p class="small text-muted mb-0">We craft data schemas, system blueprints, and interactive prototypes for early validation.</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="p-3 border rounded-3 h-100 bg-white shadow-sm">
                                    <div class="fw-bold text-primary mb-1">03. Agile Engineering Sprints</div>
                                    <p class="small text-muted mb-0">Test-driven development, weekly milestone demos, and continuous integration pipelines.</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="p-3 border rounded-3 h-100 bg-white shadow-sm">
                                    <div class="fw-bold text-primary mb-1">04. Deployment & SLA Support</div>
                                    <p class="small text-muted mb-0">Zero-downtime production rollouts, monitoring instrumentation, and ongoing maintenance.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Direct Action Banner -->
                    <div class="p-4 rounded-3 text-white d-flex flex-wrap align-items-center justify-content-between" style="background: linear-gradient(135deg, #190844 0%, #30176d 100%);">
                        <div class="mb-3 mb-md-0">
                            <h5 class="fw-bold text-white mb-1">Ready to kickstart this service?</h5>
                            <p class="small text-light mb-0">Receive a detailed scope and fixed quotation in 24 hours.</p>
                        </div>
                        <div class="d-flex gap-2">
                            <a href="{{url('/quote-request?service=' . ($item->slug ?? $item->id))}}" class="button-primary mouse-dir px-3 py-2 text-white text-decoration-none">
                                <span class="text">Get Quote</span>
                                <span class="dir-part"></span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <div class="service-sidebar sticky-top" style="top: 100px;">
                    <!-- Action Box -->
                    <div class="card border-0 p-4 shadow-sm mb-4" style="border-radius: 16px; background: #fafafa;">
                        <h4 class="h5 fw-bold mb-3 text-dark">Get Started</h4>
                        @if($item->price)
                        <div class="mb-3">
                            <span class="text-muted small">Estimated Investment</span>
                            <div class="h4 fw-bold text-primary">{{$item->price}}</div>
                        </div>
                        @endif

                        <div class="d-grid gap-2 mb-3">
                            <a href="{{url('/quote-request?service=' . ($item->slug ?? $item->id))}}" class="button-primary mouse-dir text-center py-3 text-white text-decoration-none">
                                <span class="text">Request Quotation</span>
                                <span class="dir-part"></span>
                            </a>
                            <a href="{{url('/book-consultation?service=' . ($item->slug ?? $item->id))}}" class="button-primary-trans mouse-dir text-center py-3 text-decoration-none">
                                <span class="text">Book Consultation</span>
                                <span class="dir-part"></span>
                            </a>
                        </div>
                        <div class="text-center">
                            <a href="{{url('/ai-assistant')}}" class="small text-muted text-decoration-none">
                                <i class="fal fa-robot text-success me-1"></i> Ask AI about this service
                            </a>
                        </div>
                    </div>

                    <!-- Other Services Navigation -->
                    @if(isset($allServices) && $allServices->count() > 0)
                    <div class="card border-0 p-4 shadow-sm" style="border-radius: 16px;">
                        <h5 class="fw-bold mb-3 text-dark">Other Services</h5>
                        <ul class="list-unstyled mb-0">
                            @foreach($allServices as $other)
                            <li class="mb-2 pb-2 border-bottom">
                                <a href="{{url('/services/' . ($other->slug ?? $other->id))}}" class="d-flex justify-content-between align-items-center text-decoration-none text-dark hover-primary py-1">
                                    <span>{{$other->name}}</span>
                                    <i class="fal fa-angle-right text-muted"></i>
                                </a>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>

@endsection
