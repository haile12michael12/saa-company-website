<section class="about-area section-padding-top" id="about-section">
    <div class="container">
        <div class="row d-flex align-items-center">
            <div class="col-lg-6">
                <figure class="about-image">
                    <img src="{{asset(@$about->image ?? 'frontend/assets/images/about-image.jpg')}}" alt="About" class="wow fadeInUp rounded" data-wow-delay="0.3s" onerror="this.src='https://images.unsplash.com/photo-1522071820081-009f0129c71c?auto=format&fit=crop&w=800&q=80'">
                </figure>
            </div>
            <div class="col-lg-6">
                <div class="about-text">
                    <h3 class="title wow fadeInUp" data-wow-delay="0.3s">{{@$about->title ?? 'Engineering Digital Excellence'}}</h3>
                    <div class="desc wow fadeInUp" data-wow-delay="0.4s">
                        {!! @$about->description ?? '<p>We craft modern software solutions for ambitious organizations.</p>' !!}
                    </div>
                    <div class="d-flex flex-wrap gap-3 mt-4">
                        <a href="{{url('/about')}}" class="button-dark mouse-dir wow fadeInUp text-white text-decoration-none" data-wow-delay="0.45s">
                            <span class="text">About Us</span>
                            <span class="dir-part"></span>
                        </a>
                        <a href="{{route('resume.download')}}" class="button-primary-trans mouse-dir wow fadeInUp text-decoration-none" data-wow-delay="0.5s">
                            <span class="icon"><i class="fal fa-download"></i></span>
                            <span class="text">Company Profile</span>
                            <span class="dir-part"></span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
