<header class="header-area parallax-bg" id="home-page" style="background: url('{{asset(@$hero->image ?? 'frontend/assets/images/hero-bg.jpg')}}') no-repeat scroll top center/cover">
    <div class="container">
        <div class="row">
            <div class="col-lg-8">
                <div class="header-text">
                    <h3 class="typer-title wow fadeInUp" data-wow-delay="0.2s">Digital Product Engineering</h3>
                    <h1 class="title wow fadeInUp" data-wow-delay="0.3s">{{@$hero->title ?? 'Building Next-Generation Digital Experiences'}}</h1>
                    <div class="desc wow fadeInUp" data-wow-delay="0.4s">
                        <p>{{@$hero->sub_title ?? 'We design, engineer, and deploy high-performance software solutions.'}}</p>
                    </div>
                    @if (@$hero->btn_text)
                    <a href="{{@$hero->btn_url ?? url('/quote-request')}}" class="button-dark mouse-dir wow fadeInUp text-white text-decoration-none" data-wow-delay="0.5s">{{@$hero->btn_text}} <span
                            class="dir-part"></span></a>
                    @else
                    <a href="{{url('/quote-request')}}" class="button-dark mouse-dir wow fadeInUp text-white text-decoration-none" data-wow-delay="0.5s">Get a Quote <span
                            class="dir-part"></span></a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</header>

@push('scripts')
    <script>
        @php
            $titles = [];
            if(isset($typerTitles)){
                foreach($typerTitles as $title){
                    $titles[] = $title->title;
                }
            }
            if(empty($titles)){
                $titles = ["Web Engineering", "Mobile App Development", "UI/UX & Product Design", "AI Automation"];
            }
            $titles = json_encode($titles);
        @endphp
        $('.header-area .typer-title').typer({!! $titles !!});
    </script>
@endpush
