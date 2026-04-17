<?php

namespace App\Http\Controllers;

use App\Models\Score;
use Illuminate\Support\Facades\Auth;

class ScoreController extends Controller
{
    public function index()
    {
        $scores = Score::with('user')->orderByDesc('score')->limit(20)->get();
        return view('highscores', compact('scores'));
    }
}
