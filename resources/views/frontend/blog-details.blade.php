@extends('frontend.layouts.layout')

@section('content')

<header class="site-header parallax-bg">
    <div class="container">
        <div class="row d-flex align-items-center">
            <div class="col-sm-8">
                <h2 class="title">Blog Details</h2>
            </div>
            <div class="col-sm-4">
                <div class="breadcrumbs">
                    <ul>
                        <li><a href="{{url('/')}}">Home</a></li>
                        <li><a href="{{url('/blog')}}">Blog</a></li>
                        <li>Article</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</header>

<section class="blog-details section-padding">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <h1 class="head-title mb-4" style="font-size: 38px; line-height: 1.3;">{{$blog->title}}</h1>
                <div class="blog-meta mb-4">
                    <div class="single-meta">
                        <div class="meta-title">Published</div>
                        <h4 class="meta-value"><a href="javascript:void(0)">{{date('d M, Y', strtotime($blog->created_at))}}</a></h4>
                    </div>
                    @if($blog->getCategory)
                    <div class="single-meta">
                        <div class="meta-title">Category</div>
                        <h4 class="meta-value"><a href="{{url('/blog?category=' . $blog->getCategory->slug)}}">{{$blog->getCategory->name}}</a></h4>
                    </div>
                    @endif
                    <div class="single-meta">
                        <div class="meta-title">Author</div>
                        <h4 class="meta-value"><a href="javascript:void(0)">SAA Engineering</a></h4>
                    </div>
                </div>

                <figure class="image-block mb-4">
                    <img class="img-fix rounded" src="{{asset($blog->image)}}" alt="{{$blog->title}}" onerror="this.src='https://images.unsplash.com/photo-1498050108023-c5249f4df085?auto=format&fit=crop&w=1200&q=80'">
                </figure>

                <div class="description mb-5" style="font-size: 17px; line-height: 1.9; color: #2d3436;">
                   {!! $blog->description !!}
                </div>

                <!-- Navigation between posts -->
                <div class="single-navigation d-flex justify-content-between my-5">
                    @if ($previousPost)
                    <a href="{{url('/blog/' . ($previousPost->slug ?? $previousPost->id))}}" class="nav-link">
                        <span class="icon"><i class="fal fa-angle-left"></i></span>
                        <span class="text">Previous: {{$previousPost->title}}</span>
                    </a>
                    @else
                    <div></div>
                    @endif

                    @if ($nextPost)
                    <a href="{{url('/blog/' . ($nextPost->slug ?? $nextPost->id))}}" class="nav-link">
                        <span class="text">Next: {{$nextPost->title}}</span>
                        <span class="icon"><i class="fal fa-angle-right"></i></span>
                    </a>
                    @endif
                </div>

                <!-- Related Posts -->
                @if(isset($relatedPosts) && $relatedPosts->count() > 0)
                <div class="related-posts pt-5 border-top">
                    <h4 class="fw-bold mb-4">Related Articles</h4>
                    <div class="row g-4">
                        @foreach($relatedPosts as $related)
                        <div class="col-md-4">
                            <div class="card h-100 border-0 shadow-sm p-3 rounded-3">
                                <h6 class="fw-bold mb-2">
                                    <a href="{{url('/blog/' . ($related->slug ?? $related->id))}}" class="text-dark text-decoration-none">
                                        {{$related->title}}
                                    </a>
                                </h6>
                                <p class="small text-muted mb-0">{!! Str::limit(strip_tags($related->description), 80) !!}</p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</section>

@endsection
