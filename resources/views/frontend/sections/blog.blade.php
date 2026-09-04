<section class="blog-area section-padding-top" id="blog-page">
    <div class="container">
        <div class="row align-items-end mb-4">
            <div class="col-md-8">
                <div class="section-title mb-0">
                    <h3 class="title">{{$blogTitle->title ?? 'Latest Insights'}}</h3>
                    <h3 class="title">{{@$blogTitle->title ?? 'Latest Insights'}}</h3>
                    <div class="desc">
                        {{$blogTitle->sub_title ?? 'Perspectives on engineering, design, and product strategy.'}}
                        {{@$blogTitle->sub_title ?? 'Perspectives on engineering, design, and product strategy.'}}
                    </div>
                </div>
            </div>
            <div class="col-md-4 text-md-end">
                <a href="{{url('/blog')}}" class="fw-bold text-dark text-decoration-none">
                    Read All Articles <i class="fal fa-arrow-right ms-1"></i>
                </a>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <div class="blog-slider">
                    @foreach ($blogs as $blog)
                    <div class="single-blog">
                        <figure class="blog-image">
                            <img src="{{asset($blog->image)}}" alt="{{$blog->title}}" onerror="this.src='https://images.unsplash.com/photo-1498050108023-c5249f4df085?auto=format&fit=crop&w=800&q=80'">
                        </figure>
                        <div class="blog-content">
                            <h3 class="title"><a href="{{url('/blog/' . ($blog->slug ?? $blog->id))}}">{{$blog->title}}</a></h3>
                            <div class="desc">
                                <p>{!! Str::limit(strip_tags($blog->description), 140, '...') !!}</p>
                            </div>
                            <a href="{{url('/blog/' . ($blog->slug ?? $blog->id))}}" class="button-primary-trans mouse-dir">Read More <span
                                    class="dir-part"></span> <i class="fal fa-arrow-right"></i></a>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>
