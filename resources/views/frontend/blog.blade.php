@extends('frontend.layouts.layout')

@section('content')
<header class="site-header parallax-bg">
    <div class="container">
        <div class="row d-flex align-items-center">
            <div class="col-sm-7">
                <h2 class="title">Blog & Insights</h2>
            </div>
            <div class="col-sm-5">
                <div class="breadcrumbs">
                    <ul>
                        <li><a href="{{url('/')}}">Home</a></li>
                        <li>Blog</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</header>

<section class="blog-area section-padding">
    <div class="container">
        <!-- Search and Filter Bar -->
        <div class="row mb-5 align-items-center justify-content-between">
            <div class="col-md-6 mb-3 mb-md-0">
                <form action="{{url('/blog')}}" method="GET" class="d-flex">
                    <div class="input-group">
                        <input type="text" name="search" class="form-control" placeholder="Search engineering articles..." value="{{request('search')}}">
                        <button class="btn btn-dark" type="submit"><i class="fal fa-search"></i> Search</button>
                    </div>
                </form>
            </div>
            @if(isset($categories) && $categories->count() > 0)
            <div class="col-md-6 text-md-end">
                <div class="d-flex flex-wrap justify-content-md-end gap-2">
                    <a href="{{url('/blog')}}" class="btn btn-sm {{!request('category') ? 'btn-primary' : 'btn-outline-secondary'}} rounded-pill">All Posts</a>
                    @foreach($categories as $cat)
                    <a href="{{url('/blog?category=' . $cat->slug)}}" class="btn btn-sm {{request('category') == $cat->slug ? 'btn-primary' : 'btn-outline-secondary'}} rounded-pill">{{$cat->name}}</a>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        <!-- Blog Posts Grid -->
        <div class="row g-4">
            @forelse ($blogs as $blog)
            <div class="col-xl-4 col-md-6">
                <div class="single-blog h-100 d-flex flex-column">
                    <figure class="blog-image">
                        <img src="{{asset($blog->image)}}" alt="{{$blog->title}}" onerror="this.src='https://images.unsplash.com/photo-1498050108023-c5249f4df085?auto=format&fit=crop&w=800&q=80'">
                    </figure>
                    <div class="blog-content d-flex flex-column flex-grow-1">
                        <div class="d-flex justify-content-between text-muted small mb-2">
                            <span><i class="fal fa-calendar-alt me-1"></i> {{date('M d, Y', strtotime($blog->created_at))}}</span>
                            @if($blog->getCategory)
                            <span class="badge bg-light text-dark border">{{$blog->getCategory->name}}</span>
                            @endif
                        </div>
                        <h3 class="title mb-3">
                            <a href="{{url('/blog/' . ($blog->slug ?? $blog->id))}}">{{$blog->title}}</a>
                        </h3>
                        <div class="desc mb-4 flex-grow-1">
                            <p>{!! Str::limit(strip_tags($blog->description), 140, '...') !!}</p>
                        </div>
                        <div class="mt-auto">
                            <a href="{{url('/blog/' . ($blog->slug ?? $blog->id))}}" class="button-primary-trans mouse-dir">Read More <span
                                    class="dir-part"></span> <i class="fal fa-arrow-right"></i></a>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12 text-center py-5">
                <p class="lead text-muted">No articles found matching your criteria.</p>
                <a href="{{url('/blog')}}" class="btn btn-outline-primary mt-2">View All Posts</a>
            </div>
            @endforelse
        </div>

        <div class="row mt-5">
            <div class="col-sm-12 text-center">
                <nav class="navigation pagination">
                    <div class="nav-links d-flex justify-content-center">
                        {{$blogs->links()}}
                    </div>
                </nav>
            </div>
        </div>
    </div>
</section>

@endsection
