<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

class ScoreController extends Controller
{
    public function index()
    {
        $leaders = User::orderByDesc('games_won')
            ->orderBy('games_played')
            ->get(['id', 'name', 'games_played', 'games_won']);

        $currentUserPlace = null;
        $currentUserStats = null;

        if (Auth::check()) {
            $currentUser = Auth::user();
            foreach ($leaders as $index => $user) {
                if ($user->id === $currentUser->id) {
                    $currentUserPlace = $index + 1;
                    break;
                }
            }

            $currentUserStats = [
                'games_played' => $currentUser->games_played,
                'games_won' => $currentUser->games_won,
                'games_lost' => max(0, $currentUser->games_played - $currentUser->games_won),
            ];
        }

        return view('highscores', compact('leaders', 'currentUserPlace', 'currentUserStats'));
    }
}
