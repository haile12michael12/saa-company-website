@extends('frontend.layouts.layout')

@section('content')

<header class="site-header parallax-bg">
    <div class="container">
        <div class="row d-flex align-items-center">
            <div class="col-sm-8">
                <h2 class="title">Portfolio Details</h2>
            </div>
            <div class="col-sm-4">
                <div class="breadcrumbs">
                    <ul>
                        <li><a href="{{url('/')}}">Home</a></li>
                        <li><a href="{{url('/portfolio')}}">Portfolio</a></li>
                        <li>Case Study</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</header>

<!-- Portfolio-Area-Start -->
<section class="portfolio-details section-padding" id="portfolio-page">
    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <h2 class="head-title">{{$portfolio->title}}</h2>
                <figure class="image-block mb-4">
                    <img src="{{asset($portfolio->image)}}" alt="{{$portfolio->title}}" class="img-fix rounded" onerror="this.src='https://images.unsplash.com/photo-1460925895917-afdab827c52f?auto=format&fit=crop&w=1200&q=80'">
                </figure>
                <div class="portflio-info mb-4">
                    <div class="single-info">
                        <h4 class="title">Client</h4>
                        <p>{{$portfolio->client ?? 'Confidential Enterprise Client'}}</p>
                    </div>
                    <div class="single-info">
                        <h4 class="title">Date</h4>
                        <p>{{date('d M, Y', strtotime($portfolio->created_at))}}</p>
                    </div>
                    <div class="single-info">
                        <h4 class="title">Website</h4>
                        <p>
                            @if($portfolio->website)
                            <a href="{{$portfolio->website}}" target="_blank" rel="noopener">{{$portfolio->website}} <i class="fal fa-external-link ms-1"></i></a>
                            @else
                            <span>Private Internal Platform</span>
                            @endif
                        </p>
                    </div>
                    <div class="single-info">
                        <h4 class="title">Category</h4>
                        <p>{{$portfolio->category->name ?? 'Digital Engineering'}}</p>
                    </div>
                </div>

                <div class="description mb-5">
                   {!! $portfolio->description !!}
                </div>

                <!-- Action CTA Banner -->
                <div class="p-4 rounded-3 text-white d-flex flex-wrap align-items-center justify-content-between mb-5" style="background: linear-gradient(135deg, #190844 0%, #2f1866 100%);">
                    <div class="mb-3 mb-md-0">
                        <h4 class="fw-bold text-white mb-1">Interested in Building a Similar Solution?</h4>
                        <p class="small text-light mb-0">Our team can design and build a tailored platform matching your exact technical specifications.</p>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{url('/quote-request')}}" class="button-primary mouse-dir px-3 py-2 text-white text-decoration-none">
                            <span class="text">Get Quote</span>
                            <span class="dir-part"></span>
                        </a>
                        <a href="{{url('/book-consultation')}}" class="button-primary-trans mouse-dir px-3 py-2 text-white text-decoration-none border-white">
                            <span class="text">Book Call</span>
                            <span class="dir-part"></span>
                        </a>
                    </div>
                </div>

                <!-- Navigation between projects -->
                <div class="single-navigation d-flex justify-content-between">
                    @if (isset($previousProject) && $previousProject)
                    <a href="{{url('/portfolio/' . ($previousProject->slug ?? $previousProject->id))}}" class="nav-link">
                        <span class="icon"><i class="fal fa-angle-left"></i></span>
                        <span class="text">Previous: {{$previousProject->title}}</span>
                    </a>
                    @else
                    <div></div>
                    @endif

                    @if (isset($nextProject) && $nextProject)
                    <a href="{{url('/portfolio/' . ($nextProject->slug ?? $nextProject->id))}}" class="nav-link">
                        <span class="text">Next: {{$nextProject->title}}</span>
                        <span class="icon"><i class="fal fa-angle-right"></i></span>
                    </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>
<!-- Portfolio-Area-End -->

@endsection
