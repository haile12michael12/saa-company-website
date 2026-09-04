@extends('frontend.layouts.layout')

@section('content')

<header class="site-header parallax-bg">
    <div class="container">
        <div class="row d-flex align-items-center">
            <div class="col-sm-8">
                <h2 class="title">About Our Company</h2>
            </div>
            <div class="col-sm-4">
                <div class="breadcrumbs">
                    <ul>
                        <li><a href="{{url('/')}}">Home</a></li>
                        <li>About Us</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</header>

<!-- Company Overview Section -->
<section class="about-area section-padding">
    <div class="container">
        <div class="row d-flex align-items-center">
            <div class="col-lg-6 mb-5 mb-lg-0">
                <figure class="about-image">
                    <img src="{{asset($about->image ?? 'frontend/assets/images/about-image.jpg')}}" alt="About SAA" class="img-fluid rounded shadow-lg" onerror="this.src='https://images.unsplash.com/photo-1522071820081-009f0129c71c?auto=format&fit=crop&w=800&q=80'">
                </figure>
            </div>
            <div class="col-lg-6">
                <div class="about-text ps-lg-4">
                    <span class="text-uppercase fw-bold text-primary mb-2 d-inline-block" style="letter-spacing: 2px;">Who We Are</span>
                    <h2 class="title mb-4" style="font-size: 36px; line-height: 1.3;">{{$about->title ?? 'Engineering High-Performance Digital Solutions for Modern Businesses'}}</h2>
                    <div class="desc mb-4" style="color: #636e72; font-size: 16px; line-height: 1.8;">
                        {!! $about->description ?? '<p>We are a forward-thinking digital agency delivering custom software, mobile applications, and AI integrations.</p>' !!}
                    </div>

                    <div class="d-flex flex-wrap gap-3 mt-4">
                        <a href="{{route('resume.download')}}" class="button-primary-trans mouse-dir px-4 py-3">
                            <span class="icon me-2"><i class="fal fa-file-pdf"></i></span>
                            <span class="text">Download Profile</span>
                            <span class="dir-part"></span>
                        </a>
                        <a href="{{url('/quote-request')}}" class="button-dark mouse-dir px-4 py-3 text-white text-decoration-none">
                            <span class="text">Get a Quote</span>
                            <span class="dir-part"></span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Company Values & Principles -->
<section class="section-padding bg-light">
    <div class="container">
        <div class="row">
            <div class="col-lg-6 offset-lg-3 text-center mb-5">
                <div class="section-title">
                    <h3 class="title">Our Guiding Principles</h3>
                    <div class="desc">
                        <p>How we build, collaborate, and ensure lasting value for our partners.</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="card h-100 border-0 p-4 shadow-sm" style="border-radius: 12px;">
                    <div class="mb-3 text-primary" style="font-size: 32px;">
                        <i class="fal fa-gem"></i>
                    </div>
                    <h4 class="h5 fw-bold mb-3">Architectural Integrity</h4>
                    <p class="text-muted mb-0">We build clean, tested, and maintainable software designed to scale gracefully without accumulating technical debt.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100 border-0 p-4 shadow-sm" style="border-radius: 12px;">
                    <div class="mb-3 text-primary" style="font-size: 32px;">
                        <i class="fal fa-user-shield"></i>
                    </div>
                    <h4 class="h5 fw-bold mb-3">Radical Transparency</h4>
                    <p class="text-muted mb-0">Direct access to project roadmaps, clear sprint deliverables, and open communication throughout the engineering cycle.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100 border-0 p-4 shadow-sm" style="border-radius: 12px;">
                    <div class="mb-3 text-primary" style="font-size: 32px;">
                        <i class="fal fa-rocket"></i>
                    </div>
                    <h4 class="h5 fw-bold mb-3">Business-First Impact</h4>
                    <p class="text-muted mb-0">Every line of code and user flow is measured against real conversion, operational velocity, and business ROI.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Skills & Capabilities Section -->
@if(isset($skillItems) && $skillItems->count() > 0)
<section class="skills-area section-padding">
    <div class="container">
        <div class="row">
            <div class="col-lg-6 offset-lg-3 text-center mb-5">
                <div class="section-title">
                    <h3 class="title">{{$skill->title ?? 'Technical Mastery & Capabilities'}}</h3>
                    <div class="desc">
                        <p>{{$skill->sub_title ?? 'Expertise across the entire digital product lifecycle'}}</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="row g-4">
                    @foreach($skillItems as $item)
                    <div class="col-md-6">
                        <div class="skill-box p-3 bg-white border rounded shadow-sm">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="fw-bold">{{$item->name}}</span>
                                <span class="badge bg-primary text-white">{{$item->percent}}%</span>
                            </div>
                            <div class="progress" style="height: 10px; border-radius: 5px;">
                                <div class="progress-bar bg-warning" role="progressbar" style="width: {{$item->percent}}%;" aria-valuenow="{{$item->percent}}" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>
@endif

<!-- Call to Action Banner -->
<section class="cta-area section-padding bg-dark text-white text-center" style="background: linear-gradient(135deg, #190844 0%, #2f1b6a 100%);">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <h2 class="display-6 fw-bold mb-3 text-white">Have a Project in Mind?</h2>
                <p class="lead mb-4 text-light">Let's discuss how our digital engineering and design team can bring your vision to life.</p>
                <div class="d-flex flex-wrap justify-content-center gap-3">
                    <a href="{{url('/quote-request')}}" class="button-primary mouse-dir px-4 py-3 text-white text-decoration-none">
                        <span class="text">Request a Free Quote</span>
                        <span class="dir-part"></span>
                    </a>
                    <a href="{{url('/book-consultation')}}" class="button-primary-trans mouse-dir px-4 py-3 text-white text-decoration-none border-white">
                        <span class="text">Book Free Consultation</span>
                        <span class="dir-part"></span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection
