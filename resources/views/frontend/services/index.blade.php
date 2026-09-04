@extends('frontend.layouts.layout')

@section('content')

<header class="site-header parallax-bg">
    <div class="container">
        <div class="row d-flex align-items-center">
            <div class="col-sm-8">
                <h2 class="title">Our Services</h2>
            </div>
            <div class="col-sm-4">
                <div class="breadcrumbs">
                    <ul>
                        <li><a href="{{url('/')}}">Home</a></li>
                        <li>Services</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</header>

<!-- Services Catalog -->
<section class="service-area section-padding">
    <div class="container">
        <div class="row">
            <div class="col-lg-6 offset-lg-3 text-center mb-5">
                <div class="section-title">
                    <h3 class="title">Enterprise Digital Solutions</h3>
                    <div class="desc">
                        <p>End-to-end technology services engineered to accelerate product velocity, automate operations, and scale revenue.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            @forelse($services as $service)
            <div class="col-lg-4 col-md-6">
                <div class="card h-100 border-0 p-4 shadow-sm single-service-card" style="border-radius: 16px; transition: transform .3s ease, box-shadow .3s ease; background: #ffffff;">
                    <div class="d-flex align-items-center justify-content-between mb-4">
                        <div class="service-icon-box text-center" style="width: 60px; height: 60px; line-height: 60px; background: rgba(255, 136, 94, 0.1); border-radius: 12px; color: #ff885e; font-size: 26px;">
                            <i class="{{$service->icon ?? 'fal fa-layer-group'}}"></i>
                        </div>
                        @if($service->price)
                        <span class="badge bg-light text-dark border px-3 py-2" style="font-size: 13px; font-weight: 600;">{{$service->price}}</span>
                        @endif
                    </div>

                    <h4 class="h5 fw-bold mb-3">
                        <a href="{{url('/services/' . ($service->slug ?? $service->id))}}" class="text-dark text-decoration-none">
                            {{$service->name}}
                        </a>
                    </h4>

                    <p class="text-muted mb-4" style="font-size: 15px; line-height: 1.7;">
                        {{$service->description}}
                    </p>

                    @php
                        $features = is_array($service->features) ? $service->features : json_decode($service->features, true);
                    @endphp

                    @if(!empty($features))
                    <ul class="list-unstyled mb-4 text-muted" style="font-size: 14px;">
                        @foreach(array_slice($features, 0, 3) as $feature)
                        <li class="mb-2 d-flex align-items-center">
                            <i class="fal fa-check-circle text-success me-2"></i> {{ $feature }}
                        </li>
                        @endforeach
                    </ul>
                    @endif

                    <div class="mt-auto pt-3 border-top d-flex justify-content-between align-items-center">
                        <a href="{{url('/services/' . ($service->slug ?? $service->id))}}" class="fw-bold text-primary text-decoration-none" style="font-size: 14px;">
                            Explore Details <i class="fal fa-arrow-right ms-1"></i>
                        </a>
                        <a href="{{url('/quote-request?service=' . ($service->slug ?? $service->id))}}" class="button-primary-trans mouse-dir px-3 py-1 text-decoration-none" style="font-size: 13px; border-radius: 15px;">
                            <span class="text">Get Quote</span>
                            <span class="dir-part"></span>
                        </a>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12 text-center py-5">
                <p class="lead text-muted">No services listed yet.</p>
            </div>
            @endforelse
        </div>
    </div>
</section>

<!-- Custom Consultation Banner -->
<section class="section-padding-bottom">
    <div class="container">
        <div class="p-5 rounded-4 shadow" style="background: linear-gradient(135deg, #190844 0%, #2b1863 100%); color: #fff;">
            <div class="row align-items-center">
                <div class="col-lg-8 mb-4 mb-lg-0">
                    <span class="text-uppercase fw-bold text-warning mb-2 d-inline-block" style="letter-spacing: 1.5px;">Custom Requirements?</span>
                    <h3 class="h2 fw-bold text-white mb-3">Need a Tailored Architectural Solution?</h3>
                    <p class="text-light mb-0" style="font-size: 16px;">We frequently collaborate on hybrid architectures, custom integrations, and complex enterprise requirements. Book a consultation with our senior engineering team to scope your roadmap.</p>
                </div>
                <div class="col-lg-4 text-lg-end">
                    <a href="{{url('/book-consultation')}}" class="button-primary mouse-dir px-4 py-3 text-white text-decoration-none">
                        <span class="text">Schedule Discovery Call</span>
                        <span class="dir-part"></span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection
