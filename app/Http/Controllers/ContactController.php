<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function showForm()
    {
        return view('contact.form');
    }
    public function submit(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'message' => 'required|string',
        ]);

        // Обробка форми (можна надіслати листа або зберегти в БД)
        Mail::raw("Ім'я: {$request->name}\nEmail: {$request->email}\nПовідомлення: {$request->message}", function ($message) use ($request) {
            $message->to('admin@example.com')->subject('Нове повідомлення з контактної форми');
        });

        return back()->with('success', 'Ваше повідомлення успішно надіслано!');
    }
}
