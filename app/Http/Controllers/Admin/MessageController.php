<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMessageRequest;
use App\Models\Conversation;
use App\Models\Message;
use App\Services\Communication\ConversationService;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function __construct(protected ConversationService $conversationService) {}

    public function store(StoreMessageRequest $request, Conversation $conversation)
    {
        $message = $this->conversationService->sendMessage(
            $conversation,
            $request->body,
            auth()->user(),
            $request->direction ?? 'outbound'
        );

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Message sent.', 'data' => $message], 201);
        }

        toastr()->success('Message sent.');
        return redirect()->back();
    }

    public function destroy(Message $message)
    {
        $message->delete();

        toastr()->success('Message deleted.');
        return redirect()->back();
    }
}