@php
    $topbarContact = \App\Models\FooterContactInfo::first();
    $topbarSocialLinks = \App\Models\FooterSocialLink::all();
@endphp

<div class="topbar-area">
    <div class="container">
        <div class="topbar-content d-flex flex-wrap justify-content-between align-items-center">
            <ul class="topbar-contact list-unstyled d-flex flex-wrap align-items-center mb-0">
                <li>
                    <a href="mailto:{{ $topbarContact->email ?? 'contact@saacompany.com' }}">
                        <i class="fal fa-envelope me-2"></i>{{ $topbarContact->email ?? 'contact@saacompany.com' }}
                    </a>
                </li>
                <li>
                    <a href="tel:{{ $topbarContact->phone ?? '+15552345678' }}">
                        <i class="fal fa-phone-alt me-2"></i>{{ $topbarContact->phone ?? '+1 (555) 234-5678' }}
                    </a>
                </li>
            </ul>
            <ul class="topbar-social list-unstyled d-flex align-items-center mb-0">
                @forelse ($topbarSocialLinks as $socialLink)
                    <li><a href="{{ $socialLink->url }}" target="_blank" rel="noopener" aria-label="Social media"><i class="{{ $socialLink->icon }}"></i></a></li>
                @empty
                    <li><a href="{{ url('/contact') }}">Contact Us</a></li>
                @endforelse
            </ul>
        </div>
    </div>
</div>
