<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\ConversationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\CursorPaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use App\Models\Notification;


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

        // if they have blocked each other
        $reciever = $conversation->users->firstWhere('id', '!=', $user->id);
        if ($user->hasBlocked($reciever) || $reciever->hasBlocked($user)) {
            return response()->json(['message' => 'Blocked'], 403);
        }

        $message = $conversation->messages()->create([
            'sender_id' => $user->id,
            'body'      => $request->body,
        ]);

        // touch conversation updated_at for sorting
        $conversation->touch();

        // (optional) broadcast later
        // broadcast(new MessageSent($message))->toOthers();
        $this->addNotification($conversation, $message, $user);

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

        $this->removeNotification($message);

        return response()->json(['status' => 'read']);
    }

    public function conversations(Request $request) : View | RedirectResponse {
        $user = $request->user();

        if(!$user) {
            return redirect()->route('login');
        }

        $conversations = Conversation::with([
                'users:id,nickname,image_folder',
                'Messages:id,conversation_id,sender_id,body,created_at',
                'Messages.sender:id,nickname'
            ])
            ->whereHas('users', function ($query) use ($user){
                $query->where('users.id', $user->id);
            })
            ->paginate(5);

        return view('messages.messages-page')->with('conversations', $conversations);
    }

    private function addNotification(Conversation $conversation, Message $message, User $sender): void
    {
        $recipient = $conversation->users->firstWhere('id', '!=', $sender->id);
        if (!$recipient) return;

        $message_piece = substr($message->body, 0, 20) . '...';
        $Notfication_message = "{$sender->nickname} sent you a message: {$message_piece}";
        $sender_nickname = $sender->nickname;

        $link = vsprintf(
            '<a href="%s" data-conv="%s" data-nick="%s" onclick="sessionStorage.setItem(\'openConv\', this.dataset.conv); sessionStorage.setItem(\'openNick\', this.dataset.nick);">%s</a>',
            [
                route('conversations'),
                $conversation->id,
                $sender_nickname,
                $Notfication_message
            ]
        );

        Notification::create([
            'notification_owner_id' => $recipient->id,
            'sender_id' => $sender->id,
            'content' => $message_piece,
            'place' => 'messages',
            'link' => $link,
            'used' => 0
        ]);
    }

    private function removeNotification(Message $message) {

        $message_piece = substr($message->body, 0, 20) . '...';

        $notification = Notification::where('content', $message_piece);

        $notification->delete();

        return true;
    }

}
