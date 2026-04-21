<?php

namespace App\Http\Controllers;

use App\Models\Score;
use Illuminate\Support\Facades\Auth;

class ScoreController extends Controller
{
    public function index()
    {
        // No scores table, just show the highscores view (it will use users table for stats)
        return view('highscores');
    }
}
