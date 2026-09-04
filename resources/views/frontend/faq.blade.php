@extends('frontend.layouts.layout')

@section('content')

<header class="site-header parallax-bg">
    <div class="container">
        <div class="row d-flex align-items-center">
            <div class="col-sm-8">
                <h2 class="title">Frequently Asked Questions</h2>
            </div>
            <div class="col-sm-4">
                <div class="breadcrumbs">
                    <ul>
                        <li><a href="{{url('/')}}">Home</a></li>
                        <li>FAQ</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</header>

<section class="section-padding">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8 text-center mb-5">
                <div class="section-title">
                    <h3 class="title">Everything You Need to Know</h3>
                    <div class="desc">
                        <p>Got questions about our engineering processes, engagement models, timelines, or pricing? Find clear answers below.</p>
                    </div>
                </div>

                <!-- Instant Search Filter -->
                <div class="input-group input-group-lg shadow-sm rounded-pill overflow-hidden mt-4">
                    <span class="input-group-text bg-white border-0 ps-4"><i class="fal fa-search text-muted"></i></span>
                    <input type="text" id="faqSearch" class="form-control border-0 py-3" placeholder="Type a keyword to filter questions (e.g. quote, timeline, AI, support)...">
                </div>
            </div>
        </div>

        <!-- Category Pills -->
        @if(isset($categories) && $categories->count() > 0)
        <div class="row mb-4">
            <div class="col-12 text-center">
                <div class="d-flex flex-wrap justify-content-center gap-2">
                    <a href="{{url('/faq')}}" class="btn {{!request('category') ? 'btn-primary text-white' : 'btn-outline-secondary'}} rounded-pill px-3 py-1 small">All Topics</a>
                    @foreach($categories as $cat)
                    <a href="{{url('/faq?category=' . urlencode($cat))}}" class="btn {{request('category') == $cat ? 'btn-primary text-white' : 'btn-outline-secondary'}} rounded-pill px-3 py-1 small">{{$cat}}</a>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        <!-- Accordion List -->
        <div class="row justify-content-center">
            <div class="col-lg-9">
                <div class="accordion" id="faqAccordion">
                    @forelse($faqs as $index => $faq)
                    <div class="accordion-item mb-3 border rounded-3 overflow-hidden shadow-xs faq-item">
                        <h2 class="accordion-header" id="heading{{$faq->id}}">
                            <button class="accordion-button {{$index > 0 ? 'collapsed' : ''}} fw-bold py-3 text-dark bg-white" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{$faq->id}}" aria-expanded="{{$index == 0 ? 'true' : 'false'}}" aria-controls="collapse{{$faq->id}}">
                                <span class="badge bg-light text-primary border me-3 small">{{$faq->category}}</span>
                                <span class="faq-question-text">{{$faq->question}}</span>
                            </button>
                        </h2>
                        <div id="collapse{{$faq->id}}" class="accordion-collapse collapse {{$index == 0 ? 'show' : ''}}" aria-labelledby="heading{{$faq->id}}" data-bs-parent="#faqAccordion">
                            <div class="accordion-body text-muted py-4 px-4 bg-light" style="line-height: 1.8; font-size: 15px;">
                                {{$faq->answer}}
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-5">
                        <p class="lead text-muted">No frequently asked questions listed under this category.</p>
                        <a href="{{url('/faq')}}" class="btn btn-outline-primary">View All FAQs</a>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Help Banner -->
        <div class="row justify-content-center mt-5">
            <div class="col-lg-9">
                <div class="p-4 p-md-5 rounded-4 shadow-sm text-center" style="background: linear-gradient(135deg, #190844 0%, #2f1866 100%); color: #fff;">
                    <h4 class="h3 fw-bold text-white mb-2">Still Have Questions?</h4>
                    <p class="text-light mb-4" style="max-width: 550px; margin: 0 auto;">Our team is here to help clarify any architectural, budgetary, or strategic question you may have.</p>
                    <div class="d-flex flex-wrap justify-content-center gap-3">
                        <a href="{{url('/ai-assistant')}}" class="button-primary mouse-dir px-4 py-2 text-white text-decoration-none">
                            <span class="text"><i class="fal fa-robot me-1"></i> Ask AI Assistant</span>
                            <span class="dir-part"></span>
                        </a>
                        <a href="{{url('/contact')}}" class="button-primary-trans mouse-dir px-4 py-2 text-white border-white text-decoration-none">
                            <span class="text">Contact Us</span>
                            <span class="dir-part"></span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@push('scripts')
<script>
    $(document).ready(function(){
        $('#faqSearch').on('keyup', function(){
            var val = $(this).val().toLowerCase();
            $('.faq-item').each(function(){
                var text = $(this).text().toLowerCase();
                $(this).toggle(text.indexOf(val) > -1);
            });
        });
    });
</script>
@endpush

@endsection
