<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function index(Request $request)
    {
        $currentUser = $request->user();
        $latestMessages = Message::with(['sender', 'receiver'])
            ->where(fn ($query) => $query
                ->where('sender_id', $currentUser->id)
                ->orWhere('receiver_id', $currentUser->id))
            ->latest()
            ->get();

        $contacts = $latestMessages
            ->map(fn (Message $message) => $message->sender_id === $currentUser->id ? $message->receiver : $message->sender)
            ->filter()
            ->unique('id')
            ->values();

        $activeContact = $contacts->firstWhere('id', (int) $request->query('contact')) ?? $contacts->first();
        $conversation = $activeContact
            ? $this->conversation($currentUser, $activeContact)
            : collect();

        return view('host.inbox.index', compact('contacts', 'activeContact', 'conversation'));
    }

    public function show(Request $request, User $contact)
    {
        abort_if($contact->is($request->user()), 404);
        abort_unless($this->mayMessage($request->user(), $contact), 403);

        $conversation = $this->conversation($request->user(), $contact);

        return view('messages.show', compact('contact', 'conversation'));
    }

    public function store(Request $request, User $receiver)
    {
        abort_if($receiver->is($request->user()), 422);
        abort_unless($this->mayMessage($request->user(), $receiver), 403);

        $validated = $request->validate([
            'content' => ['required', 'string', 'max:2000'],
        ]);

        Message::create([
            'sender_id' => $request->user()->id,
            'receiver_id' => $receiver->id,
            'content' => trim($validated['content']),
        ]);

        if ($request->user()->hasRole('host')) {
            return redirect()->route('host.inbox', ['contact' => $receiver->id]);
        }

        return redirect()->route('messages.show', $receiver);
    }

    private function conversation(User $first, User $second)
    {
        return Message::with(['sender', 'receiver'])
            ->where(function ($query) use ($first, $second) {
                $query->where('sender_id', $first->id)->where('receiver_id', $second->id);
            })
            ->orWhere(function ($query) use ($first, $second) {
                $query->where('sender_id', $second->id)->where('receiver_id', $first->id);
            })
            ->oldest()
            ->limit(100)
            ->get();
    }

    private function mayMessage(User $sender, User $receiver): bool
    {
        if ($receiver->hasRole('host')) {
            return true;
        }

        if (! $sender->hasRole('host')) {
            return false;
        }

        return Message::where(function ($query) use ($sender, $receiver) {
            $query->where('sender_id', $sender->id)->where('receiver_id', $receiver->id);
        })->orWhere(function ($query) use ($sender, $receiver) {
            $query->where('sender_id', $receiver->id)->where('receiver_id', $sender->id);
        })->exists();
    }
}
