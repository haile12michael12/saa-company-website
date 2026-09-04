@extends('frontend.layouts.layout')

@section('content')

<header class="site-header parallax-bg">
    <div class="container">
        <div class="row d-flex align-items-center">
            <div class="col-sm-8">
                <h2 class="title">Public AI Assistant</h2>
            </div>
            <div class="col-sm-4">
                <div class="breadcrumbs">
                    <ul>
                        <li><a href="{{url('/')}}">Home</a></li>
                        <li>AI Assistant</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</header>

<section class="section-padding bg-light">
    <div class="container">
        @if(!$aiEnabled)
        <!-- Offline State -->
        <div class="row justify-content-center">
            <div class="col-lg-8 text-center">
                <div class="card border-0 p-5 shadow-sm rounded-4 bg-white">
                    <div class="text-warning mb-3" style="font-size: 50px;">
                        <i class="fal fa-robot"></i>
                    </div>
                    <h3 class="fw-bold mb-3">AI Assistant Currently Offline</h3>
                    <p class="text-muted lead mb-4">
                        Our conversational AI assistant is undergoing scheduled maintenance and performance optimization. In the meantime, our human team is actively available!
                    </p>
                    <div class="d-flex justify-content-center gap-3">
                        <a href="{{url('/contact')}}" class="button-primary mouse-dir px-4 py-3 text-white text-decoration-none">
                            <span class="text">Contact Our Team</span>
                            <span class="dir-part"></span>
                        </a>
                        <a href="{{url('/book-consultation')}}" class="button-primary-trans mouse-dir px-4 py-3 text-decoration-none">
                            <span class="text">Book Free Call</span>
                            <span class="dir-part"></span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        @else
        <!-- Active Chat Interface -->
        <div class="row justify-content-center">
            <div class="col-lg-10 col-xl-9">
                <div class="card border-0 shadow-lg rounded-4 overflow-hidden bg-white">
                    <!-- Chat Header -->
                    <div class="p-3 px-4 d-flex align-items-center justify-content-between text-white" style="background: linear-gradient(135deg, #190844 0%, #2e1762 100%);">
                        <div class="d-flex align-items-center">
                            <div class="bg-white text-primary rounded-circle text-center me-3" style="width: 44px; height: 44px; line-height: 44px; font-size: 20px;">
                                <i class="fal fa-robot"></i>
                            </div>
                            <div>
                                <h5 class="fw-bold text-white mb-0">SAA Intelligence Guide</h5>
                                <span class="small text-success d-flex align-items-center">
                                    <span class="d-inline-block rounded-circle bg-success me-1" style="width: 8px; height: 8px;"></span> Online & Ready to Help
                                </span>
                            </div>
                        </div>
                        <button id="clearChatBtn" class="btn btn-sm btn-outline-light rounded-pill px-3" style="font-size: 12px;">
                            <i class="fal fa-trash-alt me-1"></i> Clear
                        </button>
                    </div>

                    <!-- Prompt Suggestion Chips -->
                    <div class="p-3 bg-light border-bottom">
                        <div class="d-flex flex-wrap gap-2 align-items-center">
                            <span class="small fw-bold text-muted me-1">Suggested:</span>
                            <button class="btn btn-sm btn-white bg-white border rounded-pill shadow-xs prompt-chip" data-prompt="What engineering services do you offer?">
                                💼 Our Services
                            </button>
                            <button class="btn btn-sm btn-white bg-white border rounded-pill shadow-xs prompt-chip" data-prompt="How much does a custom web application cost?">
                                💰 Pricing & Quotes
                            </button>
                            <button class="btn btn-sm btn-white bg-white border rounded-pill shadow-xs prompt-chip" data-prompt="How do I book a consultation call?">
                                📅 Book Consultation
                            </button>
                            <button class="btn btn-sm btn-white bg-white border rounded-pill shadow-xs prompt-chip" data-prompt="Tell me about your portfolio projects">
                                🚀 Case Studies
                            </button>
                            <button class="btn btn-sm btn-white bg-white border rounded-pill shadow-xs prompt-chip" data-prompt="Where is your office located and what are your hours?">
                                📍 Office & Contact
                            </button>
                        </div>
                    </div>

                    <!-- Chat Messages Container -->
                    <div id="chatMessages" class="p-4 overflow-auto" style="height: 460px; background: #fdfdfd;">
                        <!-- Welcome message -->
                        <div class="d-flex mb-3 align-items-start">
                            <div class="avatar-circle me-3 bg-primary text-white text-center rounded-circle flex-shrink-0" style="width: 38px; height: 38px; line-height: 38px; font-size: 16px;">
                                <i class="fal fa-robot"></i>
                            </div>
                            <div class="p-3 rounded-3 shadow-xs" style="background: #f1f2f6; max-width: 80%; line-height: 1.7; color: #2d3436;">
                                <p class="mb-2">Hello! 👋 I'm your <strong>SAA Digital Assistant</strong>.</p>
                                <p class="mb-0">Ask me anything about our software engineering capabilities, past portfolio launches, quotation estimates, or how to schedule a consultation with our team.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Typing Indicator -->
                    <div id="typingIndicator" class="px-4 py-2 d-none">
                        <div class="d-flex align-items-center text-muted small">
                            <span class="spinner-grow spinner-grow-sm me-2 text-primary" role="status"></span>
                            <span>Assistant is typing...</span>
                        </div>
                    </div>

                    <!-- Chat Input Bar -->
                    <div class="p-3 bg-white border-top">
                        <form id="aiChatForm" class="d-flex gap-2">
                            @csrf
                            <input type="text" id="userMessage" class="form-control form-control-lg fs-6" placeholder="Ask a question about our services, pricing, or past work..." autocomplete="off" required>
                            <button type="submit" id="sendBtn" class="button-primary mouse-dir px-4 py-2 text-white border-0 flex-shrink-0" style="border-radius: 12px; height: auto; line-height: normal;">
                                <span class="text"><i class="fal fa-paper-plane me-1"></i> Send</span>
                                <span class="dir-part"></span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        @endif
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

        function appendMessage(sender, text) {
            var isUser = sender === 'user';
            var formattedText = formatMarkdown(text);

            var html = '';
            if(isUser) {
                html = `
                <div class="d-flex mb-3 justify-content-end align-items-start">
                    <div class="p-3 rounded-3 text-white shadow-xs" style="background: #190844; max-width: 80%; line-height: 1.7;">
                        <p class="mb-0">${escapeHtml(text)}</p>
                    </div>
                    <div class="avatar-circle ms-3 bg-dark text-white text-center rounded-circle flex-shrink-0" style="width: 38px; height: 38px; line-height: 38px; font-size: 14px;">
                        <i class="fal fa-user"></i>
                    </div>
                </div>`;
            } else {
                html = `
                <div class="d-flex mb-3 align-items-start">
                    <div class="avatar-circle me-3 bg-primary text-white text-center rounded-circle flex-shrink-0" style="width: 38px; height: 38px; line-height: 38px; font-size: 16px;">
                        <i class="fal fa-robot"></i>
                    </div>
                    <div class="p-3 rounded-3 shadow-xs" style="background: #f1f2f6; max-width: 80%; line-height: 1.7; color: #2d3436;">
                        ${formattedText}
                    </div>
                </div>`;
            }

            $('#chatMessages').append(html);
            $('#chatMessages').scrollTop($('#chatMessages')[0].scrollHeight);
        }

        function escapeHtml(string) {
            return String(string).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
        }

        function formatMarkdown(text) {
            var out = text;
            // Headers
            out = out.replace(/^### (.*$)/gim, '<h6 class="fw-bold mt-2 mb-2 text-dark">$1</h6>');
            // Bold
            out = out.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>');
            // Bullet points
            out = out.replace(/• (.*$)/gim, '<div class="mb-1 d-flex"><span class="text-primary me-2">•</span><span>$1</span></div>');
            // Markdown links [text](url)
            out = out.replace(/\[(.*?)\]\((.*?)\)/g, '<a href="$2" class="fw-bold text-primary text-decoration-underline">$1</a>');
            // Paragraph breaks
            out = out.replace(/\n\n/g, '<div class="mb-2"></div>');
            return out;
        }

        function sendMessage(msg) {
            if(!msg) return;
            appendMessage('user', msg);
            $('#userMessage').val('');
            $('#typingIndicator').removeClass('d-none');
            $('#chatMessages').scrollTop($('#chatMessages')[0].scrollHeight);

            $.ajax({
                type: "POST",
                url: "{{route('ai.assistant.chat')}}",
                data: { message: msg },
                success: function(res) {
                    $('#typingIndicator').addClass('d-none');
                    if(res.status === 'success' || res.status === 'disabled') {
                        appendMessage('assistant', res.response);
                    } else {
                        appendMessage('assistant', 'Sorry, I encountered an unexpected error. Please try again or visit our Contact page.');
                    }
                },
                error: function() {
                    $('#typingIndicator').addClass('d-none');
                    appendMessage('assistant', 'Could not connect to the assistant. Please check your internet connection or reach out via our [Contact page](/contact).');
                }
            });
        }

        $('#aiChatForm').on('submit', function(e){
            e.preventDefault();
            var msg = $.trim($('#userMessage').val());
            sendMessage(msg);
        });

        $('.prompt-chip').on('click', function(){
            var prompt = $(this).data('prompt');
            sendMessage(prompt);
        });

        $('#clearChatBtn').on('click', function(){
            $('#chatMessages').html(`
                <div class="d-flex mb-3 align-items-start">
                    <div class="avatar-circle me-3 bg-primary text-white text-center rounded-circle flex-shrink-0" style="width: 38px; height: 38px; line-height: 38px; font-size: 16px;">
                        <i class="fal fa-robot"></i>
                    </div>
                    <div class="p-3 rounded-3 shadow-xs" style="background: #f1f2f6; max-width: 80%; line-height: 1.7; color: #2d3436;">
                        <p class="mb-0">Chat history cleared. How can I help you today?</p>
                    </div>
                </div>
            `);
        });
    });
</script>
@endpush

@endsection
