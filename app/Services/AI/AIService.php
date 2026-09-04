<?php

namespace App\Services\AI;

use App\Models\Lead;
use App\Models\Quote;
use App\Models\Service;
use Illuminate\Support\Str;

class AIService
{
    public function generateAssistantResponse(string $userMessage, array $context = []): array
    {
        $normalized = strtolower(trim($userMessage));
        $services = Service::take(5)->pluck('name')->toArray();
        $servicesList = !empty($services) ? implode(', ', $services) : 'Web Development, UI/UX Design, Cloud Architecture, Mobile Apps';

        $intent = 'general';
        $reply = '';

        if (Str::contains($normalized, ['quote', 'price', 'cost', 'pricing', 'estimate', 'how much'])) {
            $intent = 'pricing_inquiry';
            $reply = "I would be happy to assist you with estimating your project! You can explore our instant quote calculator or request a custom proposal on our Quote Request page. We offer competitive tiers tailored to your business needs.";
        } elseif (Str::contains($normalized, ['service', 'offer', 'what do you do', 'skills', 'portfolio'])) {
            $intent = 'services_inquiry';
            $reply = "We specialize in end-to-end digital solutions including: {$servicesList}. Would you like to schedule a quick consultation to discuss your specific requirements?";
        } elseif (Str::contains($normalized, ['book', 'appointment', 'meeting', 'consultation', 'call', 'schedule'])) {
            $intent = 'booking_request';
            $reply = "You can easily schedule a consultation directly with our team! Please visit our 'Book Consultation' section to choose a convenient time slot.";
        } elseif (Str::contains($normalized, ['contract', 'sign', 'agreement', 'proposal'])) {
            $intent = 'contract_inquiry';
            $reply = "All our agreements and proposals can be reviewed and digitally signed securely with full legal checksum compliance through our client portal.";
        } else {
            $intent = 'general_assistance';
            $reply = "Hello! I am your AI Assistant. I can help you with project inquiries, service details, booking appointments, reviewing quotes, or generating contract proposals. How can I assist you today?";
        }

        return [
            'reply' => $reply,
            'intent' => $intent,
            'confidence' => 0.94,
            'suggested_actions' => [
                ['label' => 'Request a Quote', 'url' => route('quote.request')],
                ['label' => 'Book Consultation', 'url' => route('consultation.book')],
                ['label' => 'Browse Services', 'url' => route('services.index')],
            ],
        ];
    }

    public function generateProposalSummary(Quote $quote): string
    {
        $customerName = $quote->customer->name ?? $quote->name;
        $total = number_format($quote->total ?? $quote->total_amount ?? 0, 2);

        return "This comprehensive proposal prepared for {$customerName} encompasses all deliverables for Quote #{$quote->quote_number} for a total investment of \${$total}. It includes full lifecycle development, quality assurance, milestone deliverables, and ongoing support.";
    }

    public function analyzeSentiment(string $text): array
    {
        $positiveWords = ['great', 'excellent', 'love', 'amazing', 'excited', 'best', 'good', 'happy', 'urgent', 'ready'];
        $negativeWords = ['bad', 'poor', 'slow', 'expensive', 'cancel', 'problem', 'unhappy', 'delayed', 'error'];

        $score = 0;
        $words = explode(' ', strtolower($text));

        foreach ($words as $w) {
            if (in_array($w, $positiveWords)) $score++;
            if (in_array($w, $negativeWords)) $score--;
        }

        $label = $score > 0 ? 'positive' : ($score < 0 ? 'negative' : 'neutral');

        return [
            'sentiment' => $label,
            'score' => $score,
        ];
    }
}