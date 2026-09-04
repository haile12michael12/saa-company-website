@extends('frontend.layouts.layout')

@section('content')

<header class="site-header parallax-bg">
    <div class="container">
        <div class="row d-flex align-items-center">
            <div class="col-sm-8">
                <h2 class="title">Contact Us</h2>
            </div>
            <div class="col-sm-4">
                <div class="breadcrumbs">
                    <ul>
                        <li><a href="{{url('/')}}">Home</a></li>
                        <li>Contact</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</header>

<section class="contact-area section-padding">
    <div class="container">
        <!-- Contact Info Cards -->
        <div class="row g-4 mb-5">
            <div class="col-lg-3 col-md-6">
                <div class="card h-100 border-0 p-4 shadow-sm text-center" style="border-radius: 16px;">
                    <div class="text-primary mb-3" style="font-size: 32px;">
                        <i class="fal fa-map-marker-alt"></i>
                    </div>
                    <h5 class="fw-bold mb-2">Our Office</h5>
                    <p class="text-muted small mb-0">{{$footerContact->address ?? '123 Innovation Way, Tech District, Suite 500'}}</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="card h-100 border-0 p-4 shadow-sm text-center" style="border-radius: 16px;">
                    <div class="text-primary mb-3" style="font-size: 32px;">
                        <i class="fal fa-phone-alt"></i>
                    </div>
                    <h5 class="fw-bold mb-2">Phone Inquiries</h5>
                    <p class="text-muted small mb-0">
                        <a href="tel:{{$footerContact->phone ?? '+15552345678'}}" class="text-muted text-decoration-none">{{$footerContact->phone ?? '+1 (555) 234-5678'}}</a>
                    </p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="card h-100 border-0 p-4 shadow-sm text-center" style="border-radius: 16px;">
                    <div class="text-primary mb-3" style="font-size: 32px;">
                        <i class="fal fa-envelope"></i>
                    </div>
                    <h5 class="fw-bold mb-2">Email Us</h5>
                    <p class="text-muted small mb-0">
                        <a href="mailto:{{$footerContact->email ?? 'contact@saacompany.com'}}" class="text-muted text-decoration-none">{{$footerContact->email ?? 'contact@saacompany.com'}}</a>
                    </p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="card h-100 border-0 p-4 shadow-sm text-center" style="border-radius: 16px;">
                    <div class="text-primary mb-3" style="font-size: 32px;">
                        <i class="fal fa-clock"></i>
                    </div>
                    <h5 class="fw-bold mb-2">Business Hours</h5>
                    <p class="text-muted small mb-0">Mon – Fri: 9:00 AM – 6:00 PM<br>Weekend Support: On-Call</p>
                </div>
            </div>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card border-0 shadow-sm p-4 p-md-5" style="border-radius: 20px; background: #ffffff;">
                    <div class="section-title text-center mb-4">
                        <h3 class="title">{{$contactTitle->title ?? 'Send Us a Message'}}</h3>
                        <div class="desc">
                            <p>{{$contactTitle->sub_title ?? 'Fill out the form below and our technical advisors will get back to you shortly.'}}</p>
                        </div>
                    </div>

                    <!-- Contact-Form -->
                    <form class="contact-form" id="contact-page-form">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-4">
                                <div class="form-box">
                                    <input type="text" name="name" id="form-name" class="input-box" placeholder="Your Name *" required>
                                    <label for="form-name" class="icon lb-name"><i class="fal fa-user"></i></label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-box">
                                    <input type="email" name="email" id="form-email" class="input-box" placeholder="Your Email Address *" required>
                                    <label for="form-email" class="icon lb-email"><i class="fal fa-envelope"></i></label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-box">
                                    <input type="text" name="subject" id="form-subject" class="input-box" placeholder="Subject *" required>
                                    <label for="form-subject" class="icon lb-subject"><i class="fal fa-check-square"></i></label>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="form-box">
                                    <textarea class="input-box" id="form-message" placeholder="How can our team help you? *" cols="30" rows="5" name="message" required></textarea>
                                    <label for="form-message" class="icon lb-message"><i class="fal fa-edit"></i></label>
                                </div>
                            </div>
                            <div class="col-sm-12 text-center mt-4">
                                <button class="button-primary mouse-dir px-5 py-3 text-white" type="submit" id="submit_btn">
                                    <span class="text">Send Message Now</span>
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
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $(document).on('submit', '#contact-page-form', function(e){
            e.preventDefault();
            var $btn = $('#submit_btn');
            $.ajax({
                type: "POST",
                url: "{{route('contact')}}",
                data: $(this).serialize(),
                beforeSend: function(){
                    $btn.prop("disabled", true).find('.text').text('Sending Message...');
                },
                success: function(response){
                    if(response.status === 'success'){
                        toastr.success(response.message);
                        $btn.prop("disabled", false).find('.text').text('Send Message Now');
                        $('#contact-page-form').trigger('reset');
                    }
                },
                error: function(response){
                    $btn.prop("disabled", false).find('.text').text('Send Message Now');
                    if(response.status === 422){
                        let errorsMessage = $.parseJSON(response.responseText);
                        $.each(errorsMessage.errors, function(key, val){
                            toastr.error(val[0]);
                        });
                    } else {
                        toastr.error('An error occurred. Please try again.');
                    }
                }
            });
        });
    });
</script>
@endpush

@endsection
