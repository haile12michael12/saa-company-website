<section class="portfolio-area section-padding-top" id="portfolio-page">
    <div class="container">
        <div class="row align-items-end mb-4">
            <div class="col-md-8">
                <div class="section-title mb-0">
                    <h3 class="title">{{@$portfolioTitle->title ?? 'Featured Portfolio'}}</h3>
                    <div class="desc">
                        <p>{!! @$portfolioTitle->sub_title ?? 'Explore our recent success stories' !!}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 text-md-end">
                <a href="{{url('/portfolio')}}" class="fw-bold text-dark text-decoration-none">
                    View Full Portfolio <i class="fal fa-arrow-right ms-1"></i>
                </a>
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
                @foreach ($portfolioItems as $item)
                <div data-wow-delay="0.3s" class="col-md-6 col-lg-4 filter-item {{ $item->category->slug ?? '' }}">
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
                                <p>{!! Str::limit(strip_tags($item->description), 100) !!}</p>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</section>
