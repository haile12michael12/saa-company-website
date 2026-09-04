<nav class="navbar navbar-expand-lg main_menu" id="main_menu_area">
    <div class="container">
        <a class="navbar-brand" href="{{url('/')}}">
            <img src="{{asset($generalSetting->logo ?? 'frontend/assets/images/logo.png')}}" alt="SAA Logo">
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <i class="far fa-bars"></i>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto align-items-center">
                <li class="nav-item">
                    <a class="nav-link {{request()->is('/') ? 'active' : ''}}" href="{{url('/')}}">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{request()->is('about') ? 'active' : ''}}" href="{{url('/about')}}">About</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{request()->is('services*') ? 'active' : ''}}" href="{{url('/services')}}">Services</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{request()->is('portfolio*') ? 'active' : ''}}" href="{{url('/portfolio')}}">Portfolio</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{request()->is('blog*') ? 'active' : ''}}" href="{{url('/blog')}}">Blog</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{request()->is('reviews') ? 'active' : ''}}" href="{{url('/reviews')}}">Reviews</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{request()->is('faq') ? 'active' : ''}}" href="{{url('/faq')}}">FAQ</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{request()->is('contact') ? 'active' : ''}}" href="{{url('/contact')}}">Contact</a>
                </li>
                <li class="nav-item dropdown position-relative">
                    <a class="nav-link {{request()->is('quote-request') || request()->is('book-consultation') || request()->is('ai-assistant') ? 'active' : ''}}" href="javascript:void(0)">
                        Engage <i class="fal fa-chevron-down ms-1" style="font-size: 11px;"></i>
                    </a>
                    <ul class="sub_menu list-unstyled">
                        <li class="mb-2">
                            <a href="{{url('/quote-request')}}" class="d-flex align-items-center {{request()->is('quote-request') ? 'text-primary' : ''}}">
                                <i class="fal fa-file-invoice-dollar me-2 text-warning"></i> Request a Quote
                            </a>
                        </li>
                        <li class="mb-2">
                            <a href="{{url('/book-consultation')}}" class="d-flex align-items-center {{request()->is('book-consultation') ? 'text-primary' : ''}}">
                                <i class="fal fa-calendar-check me-2 text-info"></i> Book Consultation
                            </a>
                        </li>
                        <li>
                            <a href="{{url('/ai-assistant')}}" class="d-flex align-items-center {{request()->is('ai-assistant') ? 'text-primary' : ''}}">
                                <i class="fal fa-robot me-2 text-success"></i> AI Assistant
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="nav-item ms-lg-2">
                    <a href="{{url('/quote-request')}}" class="button-primary-trans mouse-dir px-3 py-2 text-white" style="line-height: normal; font-size: 14px; border-radius: 20px;">
                        <span class="text">Get Quote</span>
                        <span class="dir-part"></span>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>
