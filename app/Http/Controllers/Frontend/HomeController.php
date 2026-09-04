<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Mail\ContactMail;
use App\Models\About;
use App\Models\Appointment;
use App\Models\Blog;
use App\Models\BlogCategory;
use App\Models\BlogSectionSetting;
use App\Models\Category;
use App\Models\ContactSectionSetting;
use App\Models\Experienace;
use App\Models\Faq;
use App\Models\Feedback;
use App\Models\FeedbackSectionSetting;
use App\Models\FooterContactInfo;
use App\Models\Hero;
use App\Models\Lead;
use App\Models\PortfolioItem;
use App\Models\PortfolioSectionSetting;
use App\Models\Quote;
use App\Models\Service;
use App\Models\SkillItem;
use App\Models\SkillSectionSetting;
use App\Models\TyperTitle;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class HomeController extends Controller
{
    /**
     * Home Page (/)
     */
    public function index()
    {
        $hero = Hero::first();
        $typerTitles = TyperTitle::all();
        $services = Service::all();
        $about = About::first();
        $portfolioTitle = PortfolioSectionSetting::first();
        $portfolioCategories = Category::all();
        $portfolioItems = PortfolioItem::all();
        $skill = SkillSectionSetting::first();
        $skillItems = SkillItem::all();
        $experience = Experienace::first();
        $feedbacks = Feedback::all();
        $feedbackTitle = FeedbackSectionSetting::first();
        $blogs = Blog::latest()->take(6)->get();
        $blogTitle = BlogSectionSetting::first();
        $contactTitle = ContactSectionSetting::first();

        return view('frontend.home', compact(
            'hero',
            'typerTitles',
            'services',
            'about',
            'portfolioTitle',
            'portfolioCategories',
            'portfolioItems',
            'skill',
            'skillItems',
            'experience',
            'feedbacks',
            'feedbackTitle',
            'blogs',
            'blogTitle',
            'contactTitle'
        ));
    }

    /**
     * About Page (/about)
     */
    public function about()
    {
        $about = About::first();
        $experience = Experienace::first();
        $skill = SkillSectionSetting::first();
        $skillItems = SkillItem::all();
        $feedbacks = Feedback::latest()->take(6)->get();
        $feedbackTitle = FeedbackSectionSetting::first();
        $services = Service::all();

        return view('frontend.about', compact(
            'about',
            'experience',
            'skill',
            'skillItems',
            'feedbacks',
            'feedbackTitle',
            'services'
        ));
    }

    /**
     * Services Catalog (/services)
     */
    public function services()
    {
        $services = Service::all();
        return view('frontend.services.index', compact('services'));
    }

    /**
     * Service Details (/services/{service})
     */
    public function showService($service)
    {
        $item = is_numeric($service)
            ? Service::findOrFail($service)
            : Service::where('slug', $service)->orWhere('id', $service)->firstOrFail();

        $allServices = Service::where('id', '!=', $item->id)->get();
        $relatedPortfolios = PortfolioItem::latest()->take(3)->get();

        return view('frontend.services.show', compact('item', 'allServices', 'relatedPortfolios'));
    }

    /**
     * Portfolio Gallery (/portfolio)
     */
    public function portfolio()
    {
        $portfolioTitle = PortfolioSectionSetting::first();
        $portfolioCategories = Category::all();
        $portfolioItems = PortfolioItem::latest()->get();

        return view('frontend.portfolio.index', compact(
            'portfolioTitle',
            'portfolioCategories',
            'portfolioItems'
        ));
    }

    /**
     * Portfolio Details (/portfolio/{portfolio})
     */
    public function showPortfolio($portfolio)
    {
        $item = is_numeric($portfolio)
            ? PortfolioItem::findOrFail($portfolio)
            : PortfolioItem::where('slug', $portfolio)->orWhere('id', $portfolio)->firstOrFail();

        $previousProject = PortfolioItem::where('id', '<', $item->id)->orderBy('id', 'desc')->first();
        $nextProject = PortfolioItem::where('id', '>', $item->id)->orderBy('id', 'asc')->first();
        $relatedProjects = PortfolioItem::where('category_id', $item->category_id)
            ->where('id', '!=', $item->id)
            ->take(3)
            ->get();

        return view('frontend.portfolio-details', [
            'portfolio' => $item,
            'previousProject' => $previousProject,
            'nextProject' => $nextProject,
            'relatedProjects' => $relatedProjects,
        ]);
    }

    /**
     * Blog Index (/blog)
     */
    public function blog(Request $request)
    {
        $query = Blog::query();

        if ($request->filled('category')) {
            $category = BlogCategory::where('slug', $request->category)->first();
            if ($category) {
                $query->where('category', $category->id);
            }
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $blogs = $query->latest()->paginate(9)->withQueryString();
        $categories = BlogCategory::all();
        $recentBlogs = Blog::latest()->take(5)->get();

        return view('frontend.blog', compact('blogs', 'categories', 'recentBlogs'));
    }

    /**
     * Blog Details (/blog/{slug})
     */
    public function showBlog($slug)
    {
        $blog = is_numeric($slug)
            ? Blog::findOrFail($slug)
            : Blog::where('slug', $slug)->orWhere('id', $slug)->firstOrFail();

        $previousPost = Blog::where('id', '<', $blog->id)->orderBy('id', 'desc')->first();
        $nextPost = Blog::where('id', '>', $blog->id)->orderBy('id', 'asc')->first();
        $relatedPosts = Blog::where('category', $blog->category)
            ->where('id', '!=', $blog->id)
            ->latest()
            ->take(3)
            ->get();

        return view('frontend.blog-details', compact('blog', 'previousPost', 'nextPost', 'relatedPosts'));
    }

    /**
     * Contact Page (/contact)
     */
    public function contactPage()
    {
        $contactTitle = ContactSectionSetting::first();
        $footerContact = FooterContactInfo::first();

        return view('frontend.contact', compact('contactTitle', 'footerContact'));
    }

    /**
     * Submit Contact Form (POST /contact)
     */
    public function contact(Request $request)
    {
        $request->validate([
            'name' => ['required', 'max:200'],
            'subject' => ['required', 'max:300'],
            'email' => ['required', 'email'],
            'message' => ['required', 'max:2000'],
        ]);

        // Record as lead
        Lead::create([
            'name' => $request->name,
            'email' => $request->email,
            'source' => 'Contact Form',
            'status' => 'new',
            'notes' => "Subject: {$request->subject}\nMessage: {$request->message}",
        ]);

        // Attempt sending email
        try {
            Mail::send(new ContactMail($request->all()));
        } catch (\Throwable $th) {
            // Mail sending gracefully falls back
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Thank you! Your message has been sent successfully.',
            ]);
        }

        return redirect()->back()->with('success', 'Thank you! Your message has been sent successfully.');
    }

    /**
     * Quote Request Page (/quote-request)
     */
    public function quoteRequest(Request $request)
    {
        $services = Service::all();
        $selectedService = $request->query('service');

        return view('frontend.quote-request', compact('services', 'selectedService'));
    }

    /**
     * Submit Quote Request (POST /quote-request)
     */
    public function submitQuoteRequest(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:200'],
            'email' => ['required', 'email', 'max:200'],
            'phone' => ['nullable', 'string', 'max:50'],
            'company' => ['nullable', 'string', 'max:200'],
            'service_id' => ['nullable', 'string'],
            'project_type' => ['required', 'string', 'max:100'],
            'budget_range' => ['required', 'string', 'max:100'],
            'timeline' => ['required', 'string', 'max:100'],
            'description' => ['required', 'string', 'max:3000'],
        ]);

        // Create Lead
        $lead = Lead::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'source' => 'Quote Request',
            'status' => 'qualified',
            'notes' => "Company: " . ($validated['company'] ?? 'N/A') . "\nProject Type: {$validated['project_type']}\nBudget: {$validated['budget_range']}\nTimeline: {$validated['timeline']}\nDescription: {$validated['description']}",
        ]);

        // Generate Quote Reference Number
        $quoteNumber = 'QT-' . date('Y') . '-' . strtoupper(Str::random(6));
        $title = ($validated['project_type'] ?? 'Project') . ' - ' . ($validated['service_id'] ?? 'Custom Digital Solution');

        // Estimate initial unit price from budget range if applicable
        $estimatedPrice = 0.00;
        if (preg_match('/\$?([0-9]+(?:,[0-9]{3})*)/', $validated['budget_range'], $matches)) {
            $estimatedPrice = (float) str_replace(',', '', $matches[1]);
        }

        // Create Quote Record
        $quote = Quote::create([
            'lead_id' => $lead->id,
            'number' => $quoteNumber,
            'title' => $title,
            'status' => 'draft',
            'subtotal' => 0.00,
            'subtotal' => $estimatedPrice,
            'tax' => 0.00,
            'total' => 0.00,
            'total' => $estimatedPrice,
            'valid_until' => Carbon::now()->addDays(30),
            'notes' => "Selected Service: " . ($validated['service_id'] ?? 'Custom') . "\nRequirements: {$validated['description']}",
            'notes' => "Service: " . ($validated['service_id'] ?? 'Custom') . "\nProject Type: {$validated['project_type']}\nBudget Range: {$validated['budget_range']}\nTimeline: {$validated['timeline']}\n\nRequirements:\n{$validated['description']}",
            'terms' => "1. 50% upfront upon project commencement, 50% on milestone completion.\n2. Standard 30 days warranty included on all deliverables.\n3. Detailed sprint delivery schedule will be attached to formal contract.",
            'token' => Str::random(40),
        ]);

        // Create initial QuoteItem
        \App\Models\QuoteItem::create([
            'quote_id' => $quote->id,
            'description' => $title,
            'quantity' => 1,
            'unit_price' => $estimatedPrice,
            'total' => $estimatedPrice,
        ]);

        $quote->recalculateTotals(true);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'status' => 'success',
                'quote_number' => $quoteNumber,
                'message' => "Quotation request #{$quoteNumber} submitted successfully! Our team will contact you with a detailed proposal within 24 hours.",
            ]);
        }

        return redirect()->route('quote.request')->with([
            'success' => "Quotation request #{$quoteNumber} submitted successfully! Our team will contact you with a detailed proposal within 24 hours.",
            'quote_number' => $quoteNumber,
        ]);
    }

    /**
     * Book Consultation Page (/book-consultation)
     */
    public function bookConsultation(Request $request)
    {
        $services = Service::all();
        $selectedService = $request->query('service');

        return view('frontend.book-consultation', compact('services', 'selectedService'));
    }

    /**
     * Submit Consultation Booking (POST /book-consultation)
     */
    public function submitBookConsultation(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:200'],
            'email' => ['required', 'email', 'max:200'],
            'phone' => ['required', 'string', 'max:50'],
            'organization' => ['nullable', 'string', 'max:200'],
            'topic' => ['required', 'string', 'max:200'],
            'meeting_format' => ['required', 'string', 'in:Google Meet,Zoom,Phone Call,In-Person Office'],
            'date' => ['required', 'date', 'after_or_equal:today'],
            'time_slot' => ['required', 'string'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        // Combine date and time slot into timestamp
        $startTime = Carbon::parse($validated['date'] . ' ' . explode(' - ', $validated['time_slot'])[0]);
        $endTime = (clone $startTime)->addMinutes(45);

        // Record Lead
        $lead = Lead::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'source' => 'Consultation Booking',
            'status' => 'qualified',
            'notes' => "Meeting Format: {$validated['meeting_format']}\nTopic: {$validated['topic']}\nNotes: " . ($validated['notes'] ?? 'None'),
        ]);

        // Create Appointment
        $appointment = Appointment::create([
            'title' => "Discovery Session: {$validated['topic']} with {$validated['name']}",
            'description' => "Client: {$validated['name']} ({$validated['email']}, {$validated['phone']})\nOrganization: " . ($validated['organization'] ?? 'Independent') . "\nFormat: {$validated['meeting_format']}\nGoals: " . ($validated['notes'] ?? 'General Consultation'),
            'starts_at' => $startTime,
            'ends_at' => $endTime,
            'status' => 'scheduled',
            'location' => $validated['meeting_format'],
        ]);

        $confirmationMsg = "Consultation booked successfully for {$startTime->format('M d, Y \a\t h:i A')} ({$validated['meeting_format']}). A calendar invitation will be sent to {$validated['email']}.";

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'status' => 'success',
                'appointment_id' => $appointment->id,
                'message' => $confirmationMsg,
            ]);
        }

        return redirect()->route('consultation.book')->with('success', $confirmationMsg);
    }

    /**
     * Frequently Asked Questions (/faq)
     */
    public function faq(Request $request)
    {
        $query = Faq::where('is_active', true)->orderBy('order');

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        $faqs = $query->get();
        $categories = Faq::where('is_active', true)->distinct()->pluck('category');

        return view('frontend.faq', compact('faqs', 'categories'));
    }

    /**
     * Reviews / Testimonials (/reviews)
     */
    public function reviews()
    {
        $feedbacks = Feedback::latest()->paginate(9);
        $totalReviews = Feedback::count();
        $avgRating = $totalReviews > 0 ? round(Feedback::avg('rating'), 1) : 5.0;

        $ratingBreakdown = [
            5 => Feedback::where('rating', 5)->count(),
            4 => Feedback::where('rating', 4)->count(),
            3 => Feedback::where('rating', 3)->count(),
            2 => Feedback::where('rating', 2)->count(),
            1 => Feedback::where('rating', 1)->count(),
        ];

        return view('frontend.reviews', compact(
            'feedbacks',
            'totalReviews',
            'avgRating',
            'ratingBreakdown'
        ));
    }

    /**
     * Submit Review (POST /reviews)
     */
    public function submitReview(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'position' => ['required', 'string', 'max:150'],
            'rating' => ['required', 'integer', 'between:1,5'],
            'description' => ['required', 'string', 'max:1500'],
        ]);

        Feedback::create([
            'name' => $validated['name'],
            'position' => $validated['position'],
            'rating' => $validated['rating'],
            'description' => $validated['description'],
            'is_featured' => true,
        ]);

        $message = 'Thank you for your valuable feedback! Your review has been submitted.';

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => $message,
            ]);
        }

        return redirect()->route('reviews.index')->with('success', $message);
    }

    /**
     * Public AI Assistant (/ai-assistant)
     */
    public function aiAssistant()
    {
        $aiEnabled = config('services.ai_assistant.enabled', true);
        $services = Service::all();
        $faqs = Faq::where('is_active', true)->take(4)->get();

        return view('frontend.ai-assistant', compact('aiEnabled', 'services', 'faqs'));
    }

    /**
     * AI Assistant Chat Endpoint (POST /ai-assistant/chat)
     */
    public function aiAssistantChat(Request $request)
    {
        $aiEnabled = config('services.ai_assistant.enabled', true);

        if (!$aiEnabled) {
            return response()->json([
                'status' => 'disabled',
                'response' => 'The Public AI Assistant is currently undergoing scheduled maintenance. Please feel free to contact our team directly at contact@saacompany.com or book a consultation.',
            ]);
        }

        $request->validate([
            'message' => ['required', 'string', 'max:1000'],
        ]);

        $message = strtolower(trim($request->message));
        $services = Service::all();
        $portfolioItems = PortfolioItem::all();
        $contact = FooterContactInfo::first();

        // Intelligent contextual response matching
        if (Str::contains($message, ['service', 'offer', 'what do you do', 'capabilities', 'stack', 'development'])) {
            $serviceList = $services->map(fn($s) => "• **{$s->name}**: {$s->description}")->implode("\n\n");
            $response = "### Our Engineering & Digital Services\n\nAt SAA Digital Solutions, we specialize in end-to-end digital transformation:\n\n{$serviceList}\n\nWould you like a tailored quotation for any of these services? You can [Request a Quote](/quote-request) or [Book a Free Discovery Call](/book-consultation).";
        } elseif (Str::contains($message, ['portfolio', 'project', 'work', 'case stud', 'client', 'example'])) {
            $projectList = $portfolioItems->take(4)->map(fn($p) => "• **{$p->title}** (Client: {$p->client}): " . Str::limit(strip_tags($p->description), 120))->implode("\n\n");
            $response = "### Featured Projects & Client Success Stories\n\nHere are some of our recent product launches:\n\n{$projectList}\n\nYou can explore our complete gallery on our [Portfolio Page](/portfolio).";
        } elseif (Str::contains($message, ['quote', 'pricing', 'cost', 'estimate', 'rate', 'budget'])) {
            $response = "### Quotations & Pricing Overview\n\nEvery project is tailored to your unique requirements. Standard guidelines:\n\n• **UI/UX & Product Design**: Starting from $1,500\n• **Custom Web Applications**: Starting from $2,500\n• **Mobile App Development (iOS & Android)**: Starting from $3,000\n• **AI Integrations & Workflow Automation**: Starting from $3,500\n\nTo get a binding, detailed estimate within 24–48 hours, please submit a brief on our [Quote Request page](/quote-request).";
        } elseif (Str::contains($message, ['consult', 'book', 'schedule', 'call', 'meeting', 'appointment', 'talk'])) {
            $response = "### Book a Free Discovery Consultation\n\nWe offer free 30-minute technical and strategic consultations! We can discuss your project architecture, requirements, and timeline.\n\n• **Formats**: Google Meet, Zoom, Phone Call, or In-Person\n• **Availability**: Monday through Friday, 09:00 AM – 06:00 PM\n\n👉 [Click here to schedule your session on /book-consultation](/book-consultation).";
        } elseif (Str::contains($message, ['contact', 'email', 'phone', 'address', 'office', 'reach', 'location'])) {
            $addr = $contact->address ?? '123 Innovation Way, Tech District, Suite 500';
            $ph = $contact->phone ?? '+1 (555) 234-5678';
            $em = $contact->email ?? 'contact@saacompany.com';
            $response = "### Get in Touch\n\nYou can reach our direct team through any of the following channels:\n\n• **Address**: {$addr}\n• **Phone**: {$ph}\n• **Email**: [{$em}](mailto:{$em})\n• **Working Hours**: Monday – Friday, 9:00 AM – 6:00 PM\n\nYou can also submit an inquiry on our [Contact page](/contact).";
        } elseif (Str::contains($message, ['review', 'testimonial', 'rating', 'feedback', 'satisfaction'])) {
            $avg = Feedback::avg('rating') ? round(Feedback::avg('rating'), 1) : '5.0';
            $count = Feedback::count();
            $response = "### Client Satisfaction & Reviews\n\nWe maintain an average rating of **{$avg} / 5.0** across {$count}+ client reviews.\n\nOur clients value our strict timeline adherence, technical depth, and transparent communication. Read our testimonials or submit your own on the [Reviews Page](/reviews).";
        } elseif (Str::contains($message, ['hello', 'hi', 'hey', 'who are you', 'help'])) {
            $response = "Hello! 👋 I am the **SAA Digital Assistant**.\n\nI can help answer your questions about:\n• Our services & technical capabilities\n• Portfolio case studies\n• Pricing & quotation requests\n• Booking a free discovery consultation\n• Office location & contact info\n\nHow can I assist you today?";
        } else {
            $response = "Thank you for reaching out! At SAA Digital Solutions, we engineer scalable web platforms, mobile apps, and custom AI automations.\n\nIf you have a specific project inquiry, feel free to [Request a Free Quote](/quote-request), [Schedule a Consultation](/book-consultation), or visit our [FAQ page](/faq) for common questions.";
        }

        return response()->json([
            'status' => 'success',
            'response' => $response,
        ]);
    }
}
