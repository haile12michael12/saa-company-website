<?php

namespace App\Services\Communication;

use App\Models\Conversation;
use App\Models\Customer;
use App\Models\Message;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class ConversationService
{
    public function getConversationsForCompany(?int $companyId, array $filters = []): LengthAwarePaginator
    {
        $query = Conversation::with(['customer', 'latestMessage']);

        if ($companyId) {
            $query->where('company_id', $companyId);
        }

        if (!empty($filters['status']) && $filters['status'] !== 'all') {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['channel'])) {
            $query->where('channel', $filters['channel']);
        }

        if (!empty($filters['customer_id'])) {
            $query->where('customer_id', $filters['customer_id']);
        }

        return $query->latest('updated_at')->paginate($filters['per_page'] ?? 15);
    }

    public function startConversation(array $data, ?int $companyId = null, ?User $sender = null): Conversation
    {
        $companyId = $companyId ?? ($sender?->company_id ?? null);

        return DB::transaction(function () use ($data, $companyId, $sender) {
            $conversation = Conversation::create([
                'company_id' => $companyId,
                'customer_id' => $data['customer_id'] ?? null,
                'channel' => $data['channel'] ?? 'email',
                'subject' => $data['subject'] ?? 'New Message Thread',
                'status' => 'open',
            ]);

            if (!empty($data['message'])) {
                $this->sendMessage($conversation, $data['message'], $sender, $data['direction'] ?? 'outbound');
            }

            return $conversation;
        });
    }

    public function sendMessage(Conversation $conversation, string $body, ?User $sender = null, string $direction = 'outbound'): Message
    {
        return DB::transaction(function () use ($conversation, $body, $sender, $direction) {
            $message = Message::create([
                'conversation_id' => $conversation->id,
                'user_id' => $sender?->id,
                'direction' => $direction,
                'body' => $body,
                'sent_at' => now(),
            ]);

            $conversation->touch();

            return $message;
        });
    }

    public function closeConversation(Conversation $conversation): Conversation
    {
        $conversation->update(['status' => 'closed']);
        return $conversation;
    }

    public function reopenConversation(Conversation $conversation): Conversation
    {
        $conversation->update(['status' => 'open']);
        return $conversation;
    }

    public function getUnreadCount(?int $companyId): int
    {
        $query = Message::where('direction', 'inbound')
            ->whereHas('conversation', function ($q) use ($companyId) {
                if ($companyId) {
                    $q->where('company_id', $companyId);
                }
                $q->where('status', 'open');
            });

        return $query->count();
    }
}