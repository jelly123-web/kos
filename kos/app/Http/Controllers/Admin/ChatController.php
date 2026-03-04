<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Message;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function index(Request $request)
    {
        $contacts = User::where('id', '!=', auth()->id())
            ->whereIn('role', ['super_admin','admin','owner','manager','staff','tenant'])
            ->get();
        $unread = Message::select('sender_id', \DB::raw('COUNT(*) as c'))
            ->where('receiver_id', auth()->id())
            ->where('is_read', false)
            ->groupBy('sender_id')
            ->pluck('c', 'sender_id');

        $selectedContact = null;
        $messages = collect();
        if ($request->has('user_id')) {
            $selectedContact = User::findOrFail($request->user_id);
            $messages = Message::where(function ($q) use ($selectedContact) {
                $q->where('sender_id', auth()->id())->where('receiver_id', $selectedContact->id);
            })->orWhere(function ($q) use ($selectedContact) {
                $q->where('sender_id', $selectedContact->id)->where('receiver_id', auth()->id());
            })->orderBy('created_at', 'asc')->get();
            Message::where('sender_id', $selectedContact->id)
                ->where('receiver_id', auth()->id())
                ->update(['is_read' => true]);
        }

        return view('pages.admin.chat', compact('contacts','selectedContact','messages','unread'));
    }

    public function send(Request $request)
    {
        $data = $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'message' => 'required|string',
        ]);
        Message::create([
            'sender_id' => auth()->id(),
            'receiver_id' => $data['receiver_id'],
            'message' => $data['message'],
            'is_read' => false,
        ]);
        return response()->json(['ok' => true]);
    }

    public function fetch(User $user)
    {
        $messages = Message::where(function ($q) use ($user) {
            $q->where('sender_id', auth()->id())->where('receiver_id', $user->id);
        })->orWhere(function ($q) use ($user) {
            $q->where('sender_id', $user->id)->where('receiver_id', auth()->id());
        })->orderBy('created_at', 'asc')->get();
        return response()->json($messages);
    }
}

