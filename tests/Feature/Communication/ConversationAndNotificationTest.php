<?php

namespace Tests\Feature\Communication;

use App\Models\Company;
use App\Models\Conversation;
use App\Models\Customer;
use App\Models\User;
use App\Notifications\NewLeadNotification;
use App\Services\Communication\ConversationService;
use App\Services\Communication\NotificationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ConversationAndNotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_conversation_and_send_messages()
    {
        $company = Company::create(['name' => 'Support Org']);
        $user = User::factory()->create(['company_id' => $company->id]);
        $customer = Customer::create(['company_id' => $company->id, 'name' => 'Client User', 'email' => 'client@support.com']);

        $service = app(ConversationService::class);
        $conv = $service->startConversation([
            'customer_id' => $customer->id,
            'subject' => 'Project milestone question',
            'channel' => 'chat',
            'message' => 'Hello team, do you have updates?',
        ], $company->id, $user);

        $this->assertDatabaseHas('conversations', [
            'id' => $conv->id,
            'subject' => 'Project milestone question',
        ]);
        $this->assertDatabaseHas('messages', [
            'conversation_id' => $conv->id,
            'body' => 'Hello team, do you have updates?',
        ]);

        $reply = $service->sendMessage($conv, 'Yes, the milestones are being finalized.', $user, 'outbound');
        $this->assertDatabaseHas('messages', [
            'id' => $reply->id,
            'body' => 'Yes, the milestones are being finalized.',
        ]);
    }

    public function test_notifications_service_manages_user_notifications()
    {
        $company = Company::create(['name' => 'Support Org']);
        $user = User::factory()->create(['company_id' => $company->id]);

        $notifService = app(NotificationService::class);
        $user->notify(new NewLeadNotification(['id' => 10, 'name' => 'Hot Lead', 'email' => 'hotlead@example.com']));

        $unread = $notifService->getUnreadNotifications($user);
        $this->assertCount(1, $unread);

        $notifService->markAllAsRead($user);
        $this->assertCount(0, $notifService->getUnreadNotifications($user));
    }
}
