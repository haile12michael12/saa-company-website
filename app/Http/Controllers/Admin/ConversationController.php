<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreConversationRequest;
use App\Http\Requests\StoreMessageRequest;
use App\Models\Conversation;
use App\Models\Customer;
use App\Services\Communication\ConversationService;
use Illuminate\Http\Request;

class ConversationController extends Controller
{
    public function __construct(protected ConversationService $conversationService) {}

    public function index(Request $request)
    {
        $this->authorize('viewAny', Conversation::class);

        $conversations = $this->conversationService->getConversationsForCompany(
            auth()->user()->company_id,
            $request->only(['status', 'channel', 'customer_id', 'per_page'])
        );

        $customers = Customer::where('company_id', auth()->user()->company_id)->get();
        $unreadCount = $this->conversationService->getUnreadCount(auth()->user()->company_id);

        if (view()->exists('admin.communication.index')) {
            return view('admin.communication.index', compact('conversations', 'customers', 'unreadCount'));
        }

        return response()->json($conversations);
    }

    public function store(StoreConversationRequest $request)
    {
        $this->authorize('create', Conversation::class);

        $conversation = $this->conversationService->startConversation(
            $request->validated(),
            auth()->user()->company_id,
            auth()->user()
        );

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Conversation started.', 'conversation' => $conversation], 201);
        }

        toastr()->success('Conversation started.');
        return redirect()->route('admin.conversations.show', $conversation);
    }

    public function show(Conversation $conversation)
    {
        $this->authorize('view', $conversation);

        $conversation->load(['customer', 'messages.user']);

        if (request()->wantsJson()) {
            return response()->json($conversation);
        }

        return view('admin.communication.show', compact('conversation'));
    }

    public function reply(StoreMessageRequest $request, Conversation $conversation)
    {
        $this->authorize('update', $conversation);

        $message = $this->conversationService->sendMessage(
            $conversation,
            $request->body,
            auth()->user(),
            $request->direction ?? 'outbound'
        );

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Reply sent.', 'data' => $message], 201);
        }

        toastr()->success('Message sent.');
        return redirect()->back();
    }

    public function close(Conversation $conversation)
    {
        $this->authorize('update', $conversation);

        $this->conversationService->closeConversation($conversation);

        toastr()->success('Conversation closed.');
        return redirect()->back();
    }

    public function destroy(Conversation $conversation)
    {
        $this->authorize('delete', $conversation);

        $conversation->delete();

        toastr()->success('Conversation thread deleted.');
        return redirect()->route('admin.conversations.index');
    }
}