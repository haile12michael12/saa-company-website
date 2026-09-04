<section class="service-area section-padding-top" id="services-section">
    <div class="container">
        <div class="row mb-4 align-items-end">
            <div class="col-md-8">
                <span class="text-uppercase fw-bold text-primary small" style="letter-spacing: 1.5px;">Capabilities</span>
                <h2 class="title" style="font-size: 36px;">Our Core Services</h2>
            </div>
            <div class="col-md-4 text-md-end">
                <a href="{{url('/services')}}" class="fw-bold text-dark text-decoration-none">
                    View All Services <i class="fal fa-arrow-right ms-1"></i>
                </a>
            </div>
        </div>
        <div class="row">
            @foreach ($services as $service)
            <div class="col-lg-4 {{$loop->index > 2 ? 'mt-4': ''}}">
                <div class="single-service">
                    <h3 class="title wow fadeInRight" data-wow-delay="0.3s">
                        <a href="{{url('/services/' . ($service->slug ?? $service->id))}}" class="text-dark text-decoration-none">
                            {{$service->name}}
                        </a>
                    </h3>
                    <div class="desc wow fadeInRight" data-wow-delay="0.4s">
                        <p>{{$service->description}}</p>
                    </div>
                    <div class="mt-3">
                        <a href="{{url('/services/' . ($service->slug ?? $service->id))}}" class="small fw-bold text-primary text-decoration-none">
                            Learn More <i class="fal fa-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>
