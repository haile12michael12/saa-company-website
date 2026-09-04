<section class="testimonial-area">
    <div class="container">
        <div class="row">
            <div class="col-lg-6 offset-lg-3 text-center">
                <div class="section-title">
                    <h3 class="title">{{$feedbackTitle->title}}</h3>
        <div class="row align-items-end mb-4">
            <div class="col-md-8">
                <div class="section-title mb-0">
                    <h3 class="title">{{@$feedbackTitle->title ?? 'Client Testimonials & Reviews'}}</h3>
                    <div class="desc">
                        <p>{{$feedbackTitle->sub_title}}</p>
                        <p>{{@$feedbackTitle->sub_title ?? 'What our clients say about partnering with our engineering team.'}}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 text-md-end">
                <a href="{{url('/reviews')}}" class="fw-bold text-dark text-decoration-none">
                    View All Reviews <i class="fal fa-arrow-right ms-1"></i>
                </a>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <div class="testimonial-slider">
                    @if(isset($feedbacks))
                    @foreach ($feedbacks as $feedback)
                    <div class="single-testimonial">
                        <div class="testimonial-header">
                            <div class="quote">
                                <i class="fas fa-quote-left"></i>
                            </div>
                            <h5 class="title">{{$feedback->name}}</h5>
                            <h6 class="position">{{$feedback->position}}</h6>
                        </div>
                        <div class="content">
                            {!!$feedback->description!!}
                        </div>
                    </div>
                    @endforeach

                    @endif
                </div>
            </div>
        </div>
    </div>
</section>
