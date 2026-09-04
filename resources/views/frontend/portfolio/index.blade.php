@extends('frontend.layouts.layout')

@section('content')

<header class="site-header parallax-bg">
    <div class="container">
        <div class="row d-flex align-items-center">
            <div class="col-sm-8">
                <h2 class="title">Our Portfolio</h2>
            </div>
            <div class="col-sm-4">
                <div class="breadcrumbs">
                    <ul>
                        <li><a href="{{url('/')}}">Home</a></li>
                        <li>Portfolio</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</header>

<section class="portfolio-area section-padding" id="portfolio-page">
    <div class="container">
        <div class="row">
            <div class="col-lg-6 offset-lg-3 text-center mb-5">
                <div class="section-title">
                    <h3 class="title">{{$portfolioTitle->title ?? 'Featured Case Studies & Launches'}}</h3>
                    <div class="desc">
                        <p>{!! $portfolioTitle->sub_title ?? 'Explore our delivered software applications, enterprise platforms, and mobile apps.' !!}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <ul class="filter-menu">
                    <li class="active" data-filter="*">All Projects</li>
                    @foreach ($portfolioCategories as $category)
                    <li data-filter=".{{$category->slug}}">{{$category->name}}</li>
                    @endforeach
                </ul>
            </div>
        </div>

        <div class="portfolio-wrapper">
            <div class="row portfolios">
                @forelse ($portfolioItems as $item)
                <div data-wow-delay="0.2s" class="col-md-6 col-lg-4 filter-item {{ $item->category->slug ?? '' }}">
                    <div class="single-portfolio">
                        <figure class="portfolio-image">
                            <img src="{{asset($item->image)}}" alt="{{$item->title}}" onerror="this.src='https://images.unsplash.com/photo-1460925895917-afdab827c52f?auto=format&fit=crop&w=800&q=80'">
                        </figure>
                        <div class="portfolio-content">
                            <a href="{{asset($item->image)}}" data-lity class="icon"><i class="fas fa-plus"></i></a>
                            <h4 class="title">
                                <a href="{{url('/portfolio/' . ($item->slug ?? $item->id))}}">{{$item->title}}</a>
                            </h4>
                            <div class="desc">
                                <p>{!! Str::limit(strip_tags($item->description), 90) !!}</p>
                            </div>
                            <a href="{{url('/portfolio/' . ($item->slug ?? $item->id))}}" class="text-white small fw-bold mt-2 d-inline-block">
                                View Case Study <i class="fal fa-arrow-right ms-1"></i>
                            </a>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-12 text-center py-5">
                    <p class="lead text-muted">No portfolio projects available yet.</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</section>

<!-- Call to Action Banner -->
<section class="section-padding-bottom">
    <div class="container">
        <div class="p-5 rounded-4 shadow text-center" style="background: linear-gradient(135deg, #190844 0%, #29175c 100%); color: #fff;">
            <h3 class="h2 fw-bold text-white mb-3">Inspired by Our Work?</h3>
            <p class="text-light mb-4" style="max-width: 600px; margin: 0 auto;">We can engineer a custom solution tailored to your operational workflows and business objectives.</p>
            <div class="d-flex justify-content-center gap-3">
                <a href="{{url('/quote-request')}}" class="button-primary mouse-dir px-4 py-3 text-white text-decoration-none">
                    <span class="text">Request a Project Quote</span>
                    <span class="dir-part"></span>
                </a>
                <a href="{{url('/book-consultation')}}" class="button-primary-trans mouse-dir px-4 py-3 text-white text-decoration-none border-white">
                    <span class="text">Book Discovery Call</span>
                    <span class="dir-part"></span>
                </a>
            </div>
        </div>
    </div>
</section>

@endsection
