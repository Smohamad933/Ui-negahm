<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Message;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class MessageController extends Controller
{
    public function index(): View
    {
        $messages = Message::query()->orderByDesc('created_at')->get();

        return view('admin.messages', compact('messages'));
    }

    public function toggleRead(Message $message): RedirectResponse
    {
        $message->update(['is_read' => ! $message->is_read]);

        return back();
    }

    public function destroy(Message $message): RedirectResponse
    {
        $message->delete();

        return back()->with('status', 'پیام حذف شد.');
    }
}
