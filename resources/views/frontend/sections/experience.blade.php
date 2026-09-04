<section class="experience-area section-padding">
    <div class="container">
        <div class="row d-flex align-items-center">
            <div class="col-lg-6 d-none d-lg-block">
                <figure class="single-image wow fadeInLeft">
                    <img src="{{asset($experience->image)}}" alt="">
                    <img src="{{asset(@$experience->image ?? 'frontend/assets/images/experience-image.jpg')}}" alt="Experience" onerror="this.src='https://images.unsplash.com/photo-1522071820081-009f0129c71c?auto=format&fit=crop&w=800&q=80'">
                </figure>
            </div>
            <div class="col-lg-6">
                <div class="experience-text">
                    <h3 class="title wow fadeInUp" data-wow-delay="0.3s">{{$experience->title}}</h3>
                    <h3 class="title wow fadeInUp" data-wow-delay="0.3s">{{@$experience->title ?? 'Over 8 Years of Delivering Mission-Critical Software Solutions'}}</h3>
                    <div class="desc wow fadeInUp" data-wow-delay="0.4s">
                        {!!$experience->description!!}
                        {!! @$experience->description ?? '<p>We take pride in delivering on time, on budget, and beyond expectations.</p>' !!}
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="icon-info wow fadeInUp" data-wow-delay="0.3s">
                                <div class="icon"><i class="fas fa-mobile-android-alt"></i></div>
                                <h6><a href="javascript:void(0)" class="text">{{$experience->phone}}</a></h6>
                                <h6><a href="tel:{{@$experience->phone ?? '+15552345678'}}" class="text">{{@$experience->phone ?? '+1 (555) 234-5678'}}</a></h6>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="icon-info wow fadeInUp" data-wow-delay="0.4s">
                                <div class="icon"><i class="fas fa-envelope"></i></div>
                                <h6><a href="javascript:void(0)" class="text">{{$experience->email}}</a></h6>
                                <h6><a href="mailto:{{@$experience->email ?? 'contact@saacompany.com'}}" class="text">{{@$experience->email ?? 'contact@saacompany.com'}}</a></h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
