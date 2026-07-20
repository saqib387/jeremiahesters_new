<?php

namespace App\Http\Controllers;

use App\Model\User;
use App\Models\UserAchievement;
use App\Services\GamificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GamificationController extends Controller
{
    /**
     * Leaderboard — top users by XP or by current streak.
     */
    public function leaderboard(Request $request)
    {
        $tab = $request->get('tab') === 'streaks' ? 'streaks' : 'xp';
        $me = Auth::user();

        $rows = collect();
        $myRank = null;
        try {
            if ($tab === 'streaks') {
                $rows = User::orderByDesc('streak_count')->orderByDesc('xp')->take(50)->get();
                $myRank = User::where('streak_count', '>', (int) ($me->streak_count ?? 0))->count() + 1;
            } else {
                $rows = User::orderByDesc('xp')->take(50)->get();
                $myRank = User::where('xp', '>', (int) ($me->xp ?? 0))->count() + 1;
            }
        } catch (\Throwable $e) {
            $rows = collect();
            $myRank = null;
        }

        return view('gamification.leaderboard', compact('rows', 'tab', 'myRank', 'me'));
    }

    /**
     * The user's achievements page — all badges with locked/unlocked state.
     */
    public function achievements()
    {
        $all = GamificationService::achievements();

        $unlocked = collect();
        try {
            $unlocked = UserAchievement::where('user_id', Auth::id())->get()->keyBy('achievement_key');
        } catch (\Throwable $e) {
            // Table not migrated yet — show everything as locked.
            $unlocked = collect();
        }

        return view('gamification.achievements', compact('all', 'unlocked'));
    }
}
