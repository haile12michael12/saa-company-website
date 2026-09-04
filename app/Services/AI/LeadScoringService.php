<?php

namespace App\Services\AI;

use App\Models\Lead;
use Illuminate\Support\Str;

class LeadScoringService
{
    public function calculateScore(Lead $lead): array
    {
        $score = 20; // baseline
        $breakdown = [];

        // 1. Corporate Email vs generic provider
        if ($lead->email) {
            $domain = substr(strrchr($lead->email, "@"), 1);
            $freeProviders = ['gmail.com', 'yahoo.com', 'hotmail.com', 'outlook.com', 'aol.com', 'mail.com'];
            if (!in_array(strtolower($domain), $freeProviders)) {
                $score += 25;
                $breakdown[] = ['rule' => 'Corporate email domain (+25)', 'points' => 25];
            } else {
                $score += 10;
                $breakdown[] = ['rule' => 'Valid email address (+10)', 'points' => 10];
            }
        }

        // 2. Phone Provided
        if (!empty($lead->phone)) {
            $score += 15;
            $breakdown[] = ['rule' => 'Phone number provided (+15)', 'points' => 15];
        }

        // 3. Notes & Requirement detail
        if (!empty($lead->notes)) {
            $length = strlen($lead->notes);
            if ($length > 100) {
                $score += 20;
                $breakdown[] = ['rule' => 'Detailed project requirements (+20)', 'points' => 20];
            } elseif ($length > 20) {
                $score += 10;
                $breakdown[] = ['rule' => 'Basic project requirements (+10)', 'points' => 10];
            }
        }

        // 4. Quotes attached
        $quotesCount = $lead->quotes()->count();
        if ($quotesCount > 0) {
            $score += 20;
            $breakdown[] = ['rule' => 'Active quote requests generated (+20)', 'points' => 20];
        }

        $finalScore = min(100, max(0, $score));

        $grade = match(true) {
            $finalScore >= 80 => 'A (Hot Lead)',
            $finalScore >= 60 => 'B (Warm Lead)',
            $finalScore >= 40 => 'C (Interested)',
            default => 'D (Cold Lead)',
        };

        return [
            'score' => $finalScore,
            'grade' => $grade,
            'breakdown' => $breakdown,
        ];
    }

    public function scoreAndUpdateLead(Lead $lead): Lead
    {
        $result = $this->calculateScore($lead);
        $lead->update([
            'score' => $result['score'],
        ]);

        return $lead;
    }
}