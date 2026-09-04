<section class="skills-area section-padding-top" id="skills-page">
    <div class="container">
        <div class="row">
            <div class="col-lg-6">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="section-title">
                            <h3 class="title">{{@$skill->title ?? 'Technical Mastery & Capabilities'}}</h3>
                            <div class="desc">
                                <p>{{@$skill->sub_title ?? 'Engineering disciplines deployed across our project lifecycles.'}}</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row skills">
                    @if(isset($skillItems))
                    @foreach ($skillItems as $item)
                    <div class="col-sm-6">
                        <div class="bar_group wow fadeInUp" data-wow-delay="0.3s" data-max="100">
                            <div class="title">{{$item->name}}</div>
                            <div class="bar_group__bar thick elastic" data-value="{{$item->percent}}" data-color="{{function_exists('getColor') ? getColor($loop->index) : '#ff885e'}}"
                                data-tooltip="true" data-show-values="false" data-unit="%"></div>
                        </div>
                    </div>
                    @endforeach
                    @endif
                </div>
            </div>
            <div class="col-lg-6 d-none d-lg-block">
                <figure class="single-image text-right wow fadeInRight">
                    <img src="{{asset(@$skill->image ?? 'frontend/assets/images/skill-image.jpg')}}" alt="Skills" onerror="this.src='https://images.unsplash.com/photo-1581291518857-4e27b48ff24e?auto=format&fit=crop&w=800&q=80'">
                </figure>
            </div>
        </div>
    </div>
</section>
