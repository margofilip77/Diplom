<?php

namespace App\Http\Controllers;

use App\Models\Favorite;
use App\Models\Accommodation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FavoriteController extends Controller
{
    public function toggle(Accommodation $accommodation)
    {
        $user = Auth::user();

        // Перевіряємо, чи помешкання вже улюблене
        $favorite = Favorite::where('user_id', $user->id)
            ->where('accommodation_id', $accommodation->id)
            ->first();

        if ($favorite) {
            // Видаляємо з улюблених
            $favorite->delete();
            return response()->json(['is_favorited' => false, 'message' => 'Видалено з улюблених']);
        } else {
            // Додаємо до улюблених
            Favorite::create([
                'user_id' => $user->id,
                'accommodation_id' => $accommodation->id,
            ]);
            return response()->json(['is_favorited' => true, 'message' => 'Додано до улюблених']);
        }
    }
}