<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\ConversationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\CursorPaginator;
use Illuminate\Support\Facades\DB;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;

class MessageController extends Controller
{
    public function __construct(private ConversationService $conversationService) {}

    public function index() : JsonResponse {

        $user = auth()->guard('web')->user();

        $conversations = Conversation::with([
                'user:id,name,nickname,image_folder',
                'latestMessage:id,conversation_id,sender_id,body,created_at',
                'latestMessage.sender:id,nickname'
            ])
            ->whereHas('users', function ($query) use ($user) {
                    $query->where('users.id', $user->id);
            })
            ->withCount(['messages as unread' => function ($query) use ($user) {
                $query->where('sender_id', '!=', $user->id)
                    ->whereNull('read_at');
            }])
            ->latest('updated_at')
            ->cursorPaginate(15);

        return response()->json($conversations);
    }

    public function start(Request $request) : JsonResponse {

        $request->validate([
            'receiver_id' => 'required|exists:users,id'
        ]);
        $user = auth()->guard('web')->user();
        $conversation = $this->conversationService->between($user, User::find($request->receiver_id));

        return response()->json(['conversation_id' => $conversation->id], 201);
    }

    /**
     * Store a new message in the conversation.
     * POST /messages/{conversation}  { "body": "hello" }
     */
    public function store(Request $request, Conversation $conversation) {

        //current user
        $user = auth()->guard('web')->user();

        // authorise participant
        abort_unless($conversation->users->contains($user->id), 403);

        $request->validate(['body' => 'required|string|max:2000']);

        $message = $conversation->messages()->create([
            'sender_id' => $user->id,
            'body'      => $request->body,
        ]);

        // touch conversation updated_at for sorting
        $conversation->touch();

        // (optional) broadcast later
        // broadcast(new MessageSent($message))->toOthers();

        return response()->json($message->load('sender:id,nickname,image_folder'), 201);
    }

    public function show(Conversation $conversation, Request $request) {

        //current user
        $user = auth()->guard('web')->user();

        abort_unless($conversation->users->contains($user->id), 403,
        'User ' . $user->id . ' is not a participant of conversation ' . $conversation->id);

        $messages = $conversation->messages()
            ->with('user:id,nickname,image_folder')
            ->cursorPaginate(30);
        
        // mark messages older than the first unread as read (simple approach)
        $conversation->messages()
            ->where('sender_id', '!=', $request->user()->id)
            ->whereNull('read')
            ->update(['read' => now()]);

        return $messages;
    }

    /**
     * Mark a single message as read (or use bulk if you prefer).
     * PUT /messages/{message}/read
     */
    public function markRead(Request $request, Message $message): JsonResponse
    {
        abort_unless($message->conversation->users->contains($request->user()->id), 403);
        abort_if($message->sender_id === $request->user()->id, 422, 'Cannot mark own message read');

        $message->update(['read' => now()]);

        return response()->json(['status' => 'read']);
    }

}
