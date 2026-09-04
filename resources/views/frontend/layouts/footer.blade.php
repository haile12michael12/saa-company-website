@php
    $generalSetting = $generalSetting ?? \App\Models\GeneralSetting::first();
    $footerInfo = \App\Models\FooterInfo::first();
    $footerIcons = \App\Models\FooterSocialLink::all();
    $footerUsefulLinks = \App\Models\FooterUsefulLink::all();
    $footerContact = \App\Models\FooterContactInfo::first();
    $footerHelpLinks = \App\Models\FooterHelpLink::all();
@endphp

<footer class="footer-area">
    <div class="container">
        <div class="row footer-widgets">
            <div class="col-md-12 col-lg-3 widget">
                <div class="text-box">
                    <figure class="footer-logo">
                        <img src="{{asset($generalSetting->footer_logo ?? 'frontend/assets/images/logo.png')}}" alt="SAA Logo">
                    </figure>
                    <p>{{$footerInfo->info ?? 'We craft modern digital products, enterprise applications, and intelligent automated systems that empower ambitious businesses to scale.'}}</p>
                    <ul class="d-flex flex-wrap list-unstyled gap-2 mt-3">
                        @forelse ($footerIcons as $icon)
                            <li><a href="{{$icon->url}}" target="_blank" rel="noopener"><i class="{{$icon->icon}}"></i></a></li>
                        @empty
                            <li><a href="#"><i class="fab fa-twitter"></i></a></li>
                            <li><a href="#"><i class="fab fa-github"></i></a></li>
                            <li><a href="#"><i class="fab fa-linkedin"></i></a></li>
                        @endforelse
                    </ul>
                </div>
            </div>
            <div class="col-md-4 col-lg-2 offset-lg-1 widget">
                <h3 class="widget-title">Explore</h3>
                <ul class="nav-menu list-unstyled">
                    @forelse ($footerUsefulLinks as $usefulLink)
                        <li><a href="{{$usefulLink->url}}">{{$usefulLink->name}}</a></li>
                    @empty
                        <li><a href="{{url('/about')}}">About Us</a></li>
                        <li><a href="{{url('/services')}}">Services</a></li>
                        <li><a href="{{url('/portfolio')}}">Portfolio</a></li>
                        <li><a href="{{url('/blog')}}">Blog</a></li>
                    @endforelse
                </ul>
            </div>
            <div class="col-md-4 col-lg-3 widget">
                <h3 class="widget-title">Contact & Support</h3>
                <ul class="list-unstyled">
                    <li class="mb-2"><i class="fal fa-map-marker-alt text-warning me-2"></i> {{$footerContact->address ?? '123 Innovation Way, Tech District, Suite 500'}}</li>
                    <li class="mb-2"><i class="fal fa-phone-alt text-info me-2"></i> <a href="tel:{{$footerContact->phone ?? '+15552345678'}}">{{$footerContact->phone ?? '+1 (555) 234-5678'}}</a></li>
                    <li class="mb-2"><i class="fal fa-envelope text-success me-2"></i> <a href="mailto:{{$footerContact->email ?? 'contact@saacompany.com'}}">{{$footerContact->email ?? 'contact@saacompany.com'}}</a></li>
                </ul>
            </div>
            <div class="col-md-4 col-lg-3 widget">
                <h3 class="widget-title">Visitor Center</h3>
                <ul class="nav-menu list-unstyled">
                    <li><a href="{{url('/quote-request')}}"><i class="fal fa-file-invoice-dollar me-1"></i> Request a Quote</a></li>
                    <li><a href="{{url('/book-consultation')}}"><i class="fal fa-calendar-alt me-1"></i> Book Consultation</a></li>
                    <li><a href="{{url('/faq')}}"><i class="fal fa-question-circle me-1"></i> Frequently Asked Questions</a></li>
                    <li><a href="{{url('/reviews')}}"><i class="fal fa-star me-1"></i> Client Reviews & Ratings</a></li>
                    <li><a href="{{url('/ai-assistant')}}"><i class="fal fa-robot me-1"></i> Public AI Assistant</a></li>
                </ul>
            </div>
        </div>
    </div>
    <div class="footer-bottom">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="copyright d-flex flex-wrap justify-content-between align-items-center">
                        <p>{{$footerInfo->copy_right ?? '© 2026 SAA Digital Solutions. All Rights Reserved.'}}</p>
                        <p>{{$footerInfo->powered_by ?? 'Engineered with Precision'}}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</footer>
