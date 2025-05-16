<?php

namespace App\Http\Controllers;

use App\Models\SupportMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ContactController extends Controller
{
    public function showForm()
    {
        return view('contact.form');
    }

    public function submit(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'message' => 'required|string',
        ]);

        SupportMessage::create($validated);

        return redirect()->route('contact.form')->with('success', 'Повідомлення успішно надіслано!');
    }

    public function adminList(Request $request)
    {
        $filter = $request->query('filter', 'all');
        $query = SupportMessage::query();

        if ($filter === 'recent') {
            $query->where('created_at', '>=', now()->subHours(24));
        } elseif ($filter === 'older') {
            $query->where('created_at', '<', now()->subHours(24));
        }

        $messages = $query->orderBy('created_at', 'desc')->paginate(10);

        // Оновлення часу перегляду для непрочитаних повідомлень
        $unviewedMessages = $messages->where('is_viewed', false);
        if ($unviewedMessages->isNotEmpty()) {
            foreach ($unviewedMessages as $message) {
                $message->update([
                    'is_viewed' => true,
                    'last_viewed_at' => now(),
                ]);
            }
        }

        return view('admin.support-messages', compact('messages', 'filter'));
    }
// Новий метод для перевірки недавніх повідомлень (до 24 годин)
public function checkUnviewedMessages()
    {
        $unviewedMessages = SupportMessage::where('is_viewed', false)
            ->orderBy('created_at', 'desc')
            ->get(['id', 'name', 'message', 'created_at']);

        return response()->json([
            'hasUnviewedMessages' => $unviewedMessages->isNotEmpty(),
            'messages' => $unviewedMessages,
        ]);
    }

    public function checkRecentMessages()
    {
        $recentMessages = SupportMessage::where('is_viewed', false)
            ->where('created_at', '>=', now()->subHours(24))
            ->orderBy('created_at', 'desc')
            ->get(['id', 'name', 'message', 'created_at']);

        return response()->json([
            'hasRecentMessages' => $recentMessages->isNotEmpty(),
            'messages' => $recentMessages,
        ]);
    }
    public function delete(SupportMessage $message)
    {
        $message->delete();
        
        // Якщо це AJAX-запит, повертаємо JSON
        if (request()->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Повідомлення видалено!']);
        }
    
        return redirect()->route('admin.support-messages')->with('success', 'Повідомлення видалено!');
    }
    public function respond(Request $request, SupportMessage $message)
    {
        $request->validate(['response' => 'required|string|max:1000']);
        $message->update([
            'response' => $request->response,
            'is_viewed' => true,
            'responded_at' => now(), // Додаємо поточну дату та час як дату відповіді
        ]);

        return redirect()->route('admin.support-messages')->with('success', 'Відповідь надіслано!');
    }
    
}