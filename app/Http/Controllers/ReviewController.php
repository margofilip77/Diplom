<?php

namespace App\Http\Controllers;

use App\Models\Accommodation;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function store(Request $request, $accommodationId)
    {
        $request->validate([
            'rating' => 'required|integer|between:1,5',
            'comment' => 'required|string|max:1000',
        ]);

        $accommodation = Accommodation::findOrFail($accommodationId);

        Review::create([
            'user_id' => Auth::id(),
            'accommodation_id' => $accommodation->id,
            'rating' => $request->rating,
            'comment' => $request->comment,
        ]);

        return redirect()->back()->with('success', 'Відгук успішно додано!');
    }

    public function destroy($id)
    {
        $review = Review::findOrFail($id);

        if (Auth::id() !== $review->user_id) {
            return redirect()->back()->with('error', 'Ви не можете видалити цей відгук.');
        }

        $review->delete();

        return redirect()->back()->with('success', 'Відгук успішно видалено.');
    }
}