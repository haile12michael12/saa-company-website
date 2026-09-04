@extends('frontend.layouts.layout')

@section('content')

<header class="site-header parallax-bg">
    <div class="container">
        <div class="row d-flex align-items-center">
            <div class="col-sm-8">
                <h2 class="title">Client Reviews</h2>
            </div>
            <div class="col-sm-4">
                <div class="breadcrumbs">
                    <ul>
                        <li><a href="{{url('/')}}">Home</a></li>
                        <li>Reviews</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</header>

<section class="section-padding">
    <div class="container">
        <!-- Rating Metrics Overview -->
        <div class="row g-4 mb-5 align-items-center">
            <div class="col-lg-4 text-center">
                <div class="p-4 p-md-5 rounded-4 shadow-sm bg-light border">
                    <div class="display-3 fw-bold text-dark mb-1">{{$avgRating}}</div>
                    <div class="text-warning mb-2" style="font-size: 22px;">
                        @for($i = 1; $i <= 5; $i++)
                            @if($i <= round($avgRating))
                                <i class="fas fa-star"></i>
                            @else
                                <i class="fal fa-star text-muted"></i>
                            @endif
                        @endfor
                    </div>
                    <p class="text-muted mb-0">Based on {{$totalReviews}} Verified Client Reviews</p>
                </div>
            </div>

            <div class="col-lg-8">
                <div class="p-4 p-md-5 rounded-4 shadow-sm bg-white border">
                    <h5 class="fw-bold mb-3 text-dark">Satisfaction Breakdown</h5>
                    @foreach([5, 4, 3, 2, 1] as $stars)
                    @php
                        $count = $ratingBreakdown[$stars] ?? 0;
                        $pct = $totalReviews > 0 ? round(($count / $totalReviews) * 100) : 0;
                    @endphp
                    <div class="d-flex align-items-center mb-2">
                        <span class="small fw-bold text-dark me-2" style="width: 50px;">{{$stars}} <i class="fas fa-star text-warning" style="font-size: 11px;"></i></span>
                        <div class="progress flex-grow-1 me-3" style="height: 10px; border-radius: 5px;">
                            <div class="progress-bar bg-warning" role="progressbar" style="width: {{$pct}}%;"></div>
                        </div>
                        <span class="small text-muted" style="width: 40px;">{{$count}}</span>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Reviews List -->
        <div class="row g-4 mb-5">
            <div class="col-12">
                <h3 class="h4 fw-bold mb-4 text-dark"><i class="fal fa-comments-alt text-primary me-2"></i> Client Experiences</h3>
            </div>
            @forelse($feedbacks as $fb)
            <div class="col-lg-4 col-md-6">
                <div class="card h-100 border-0 p-4 shadow-sm" style="border-radius: 16px; background: #ffffff;">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="text-warning">
                            @for($i = 1; $i <= 5; $i++)
                                @if($i <= ($fb->rating ?? 5))
                                    <i class="fas fa-star" style="font-size: 14px;"></i>
                                @else
                                    <i class="fal fa-star text-muted" style="font-size: 14px;"></i>
                                @endif
                            @endfor
                        </div>
                        <span class="small text-muted">{{date('M Y', strtotime($fb->created_at))}}</span>
                    </div>
                    <p class="text-muted mb-4 flex-grow-1" style="line-height: 1.7; font-size: 15px;">
                        "{!! strip_tags($fb->description) !!}"
                    </p>
                    <div class="d-flex align-items-center mt-auto pt-3 border-top">
                        <div class="avatar-circle me-3 bg-primary text-white fw-bold rounded-circle text-center" style="width: 44px; height: 44px; line-height: 44px; font-size: 16px;">
                            {{ strtoupper(substr($fb->name, 0, 1)) }}
                        </div>
                        <div>
                            <h6 class="fw-bold mb-0 text-dark" style="font-size: 15px;">{{$fb->name}}</h6>
                            <span class="small text-muted">{{$fb->position}}</span>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12 text-center py-4">
                <p class="text-muted">No reviews posted yet. Be the first to share your experience!</p>
            </div>
            @endforelse
        </div>

        <!-- Submit Review Form -->
        <div class="row justify-content-center pt-4">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm p-4 p-md-5" style="border-radius: 20px; background: #fafafa;">
                    <div class="text-center mb-4">
                        <span class="text-uppercase fw-bold text-primary small" style="letter-spacing: 1px;">Your Opinion Matters</span>
                        <h3 class="h3 fw-bold text-dark mt-1">Submit Your Review</h3>
                        <p class="text-muted small">Collaborated with SAA? We'd love to hear about your experience.</p>
                    </div>

                    <form action="{{route('reviews.submit')}}" method="POST" id="submit-review-form">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-medium text-dark">Your Name *</label>
                                <input type="text" name="name" class="form-control" placeholder="e.g. Jordan Reed" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-medium text-dark">Role & Company *</label>
                                <input type="text" name="position" class="form-control" placeholder="e.g. Co-Founder, NextEra Labs" required>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label small fw-medium text-dark d-block">Overall Rating *</label>
                                <div class="rating-picker d-flex gap-3 align-items-center py-1">
                                    @for($r = 5; $r >= 1; $r--)
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="rating" id="rating{{$r}}" value="{{$r}}" {{$r == 5 ? 'checked' : ''}}>
                                        <label class="form-check-label fw-bold text-warning" for="rating{{$r}}">
                                            {{$r}} <i class="fas fa-star"></i>
                                        </label>
                                    </div>
                                    @endfor
                                </div>
                            </div>
                            <div class="col-12">
                                <label class="form-label small fw-medium text-dark">Review Comments *</label>
                                <textarea name="description" class="form-control" rows="4" placeholder="Describe your experience working with our engineering and design team..." required></textarea>
                            </div>
                            <div class="col-12 text-center mt-4">
                                <button type="submit" id="review_submit_btn" class="button-primary mouse-dir px-5 py-2 text-white">
                                    <span class="text">Submit Review</span>
                                    <span class="dir-part"></span>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

@push('scripts')
<script>
    $(document).ready(function(){
        $('#submit-review-form').on('submit', function(e){
            e.preventDefault();
            var $btn = $('#review_submit_btn');
            $.ajax({
                type: "POST",
                url: "{{route('reviews.submit')}}",
                data: $(this).serialize(),
                beforeSend: function(){
                    $btn.prop("disabled", true).find('.text').text('Submitting...');
                },
                success: function(response){
                    $btn.prop("disabled", false).find('.text').text('Submit Review');
                    if(response.status === 'success'){
                        toastr.success(response.message);
                        $('#submit-review-form').trigger('reset');
                        setTimeout(function(){
                            location.reload();
                        }, 1200);
                    }
                },
                error: function(response){
                    $btn.prop("disabled", false).find('.text').text('Submit Review');
                    if(response.status === 422){
                        let errorsMessage = $.parseJSON(response.responseText);
                        $.each(errorsMessage.errors, function(key, val){
                            toastr.error(val[0]);
                        });
                    } else {
                        toastr.error('Unable to submit review. Please try again.');
                    }
                }
            });
        });
    });
</script>
@endpush

@endsection
