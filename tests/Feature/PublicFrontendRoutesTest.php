<?php

namespace Tests\Feature;

use App\Models\Blog;
use App\Models\PortfolioItem;
use App\Models\Service;
use Tests\TestCase;

class PublicFrontendRoutesTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        if (Service::count() === 0) {
            $this->artisan('db:seed');
        }
    }

    /**
     * Test all 14 public routes are accessible without authentication.
     */
    public function test_all_public_frontend_routes_are_accessible_without_auth(): void
    {
        // 1. Home
        $response = $this->get('/');
        $response->assertStatus(200);

        // 2. About
        $response = $this->get('/about');
        $response->assertStatus(200);
        $response->assertSee('About Our Company');

        // 3. Services index
        $response = $this->get('/services');
        $response->assertStatus(200);
        $response->assertSee('Our Services');

        // 4. Services detail
        $service = Service::first();
        $this->assertNotNull($service);
        $response = $this->get('/services/' . ($service->slug ?? $service->id));
        $response->assertStatus(200);
        $response->assertSee($service->name);

        // 5. Portfolio index
        $response = $this->get('/portfolio');
        $response->assertStatus(200);
        $response->assertSee('Our Portfolio');

        // 6. Portfolio detail
        $portfolio = PortfolioItem::first();
        $this->assertNotNull($portfolio);
        $response = $this->get('/portfolio/' . ($portfolio->slug ?? $portfolio->id));
        $response->assertStatus(200);
        $response->assertSee($portfolio->title);

        // 7. Blog index
        $response = $this->get('/blog');
        $response->assertStatus(200);
        $response->assertSee('Blog');

        // 8. Blog detail
        $blog = Blog::first();
        $this->assertNotNull($blog);
        $response = $this->get('/blog/' . ($blog->slug ?? $blog->id));
        $response->assertStatus(200);
        $response->assertSee($blog->title);

        // 9. Contact page
        $response = $this->get('/contact');
        $response->assertStatus(200);
        $response->assertSee('Contact Us');

        // 10. Quote request page
        $response = $this->get('/quote-request');
        $response->assertStatus(200);
        $response->assertSee('Request a Quotation');

        // 11. Book consultation page
        $response = $this->get('/book-consultation');
        $response->assertStatus(200);
        $response->assertSee('Book Consultation');

        // 12. FAQ page
        $response = $this->get('/faq');
        $response->assertStatus(200);
        $response->assertSee('Frequently Asked Questions');

        // 13. Reviews page
        $response = $this->get('/reviews');
        $response->assertStatus(200);
        $response->assertSee('Client Reviews');

        // 14. AI Assistant page
        $response = $this->get('/ai-assistant');
        $response->assertStatus(200);
        $response->assertSee('Public AI Assistant');
    }

    /**
     * Test visitors can submit contact form.
     */
    public function test_visitor_can_submit_contact_form(): void
    {
        $payload = [
            'name' => 'Jane Visitor',
            'email' => 'jane@example.com',
            'subject' => 'Inquiry regarding mobile application',
            'message' => 'Hello team, I would like to explore developing a custom mobile application for logistics.',
        ];

        $response = $this->postJson('/contact', $payload);
        $response->assertStatus(200)
            ->assertJson(['status' => 'success']);

        $this->assertDatabaseHas('leads', [
            'email' => 'jane@example.com',
            'source' => 'Contact Form',
        ]);
    }

    /**
     * Test visitors can submit quote request.
     */
    public function test_visitor_can_submit_quote_request(): void
    {
        $payload = [
            'name' => 'Michael Scott',
            'email' => 'michael@dundermifflin.com',
            'phone' => '+1 555-0100',
            'company' => 'Dunder Mifflin Paper',
            'service_id' => 'Custom Web Application Development',
            'project_type' => 'New Product / MVP Build',
            'budget_range' => '$5,000 – $10,000',
            'timeline' => '1 – 2 Months',
            'description' => 'Need an internal customer portal with invoicing and real-time inventory management.',
        ];

        $response = $this->postJson('/quote-request', $payload);
        $response->assertStatus(200)
            ->assertJson(['status' => 'success']);

        $this->assertDatabaseHas('leads', [
            'email' => 'michael@dundermifflin.com',
            'source' => 'Quote Request',
        ]);

        $this->assertDatabaseHas('quotes', [
            'status' => 'draft',
        ]);
    }

    /**
     * Test visitors can book consultation.
     */
    public function test_visitor_can_book_consultation(): void
    {
        $payload = [
            'name' => 'Pam Beesly',
            'email' => 'pam@dundermifflin.com',
            'phone' => '+1 555-0101',
            'organization' => 'Dunder Mifflin',
            'topic' => 'Web & SaaS Platform Development',
            'meeting_format' => 'Google Meet',
            'date' => date('Y-m-d', strtotime('+3 days')),
            'time_slot' => '02:00 PM - 03:00 PM',
            'notes' => 'Looking to discuss UI/UX redesign and architecture.',
        ];

        $response = $this->postJson('/book-consultation', $payload);
        $response->assertStatus(200)
            ->assertJson(['status' => 'success']);

        $this->assertDatabaseHas('appointments', [
            'status' => 'scheduled',
            'location' => 'Google Meet',
        ]);
    }

    /**
     * Test visitors can submit review.
     */
    public function test_visitor_can_submit_review(): void
    {
        $payload = [
            'name' => 'Jim Halpert',
            'position' => 'Sales Director, Athlead',
            'rating' => 5,
            'description' => 'Remarkable work on our platform. The performance optimization was stellar!',
        ];

        $response = $this->postJson('/reviews', $payload);
        $response->assertStatus(200)
            ->assertJson(['status' => 'success']);

        $this->assertDatabaseHas('feedback', [
            'name' => 'Jim Halpert',
            'rating' => 5,
        ]);
    }

    /**
     * Test visitors can chat with public AI assistant.
     */
    public function test_visitor_can_use_public_ai_assistant(): void
    {
        $payload = [
            'message' => 'What services do you provide?',
        ];

        $response = $this->postJson('/ai-assistant/chat', $payload);
        $response->assertStatus(200)
            ->assertJson(['status' => 'success']);

        $this->assertStringContainsString('Services', $response->json('response'));
    }
}
