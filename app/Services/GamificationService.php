<?php

namespace App\Services;

use App\Model\User;
use App\Models\UserAchievement;
use Illuminate\Support\Carbon;

/**
 * Lightweight gamification: daily streaks + XP + levels.
 * No external packages — runs on standard shared hosting.
 */
class GamificationService
{
    /** XP required to advance one level. */
    const XP_PER_LEVEL = 100;

    /** Base XP awarded for a daily visit. */
    const XP_DAILY_VISIT = 10;

    /** Celebration events (level-ups / badge unlocks) collected during a request. */
    protected static $events = [];

    /** Return and clear the celebration events queued this request. */
    public static function pullEvents()
    {
        $events = self::$events;
        self::$events = [];
        return $events;
    }

    /**
     * Tick the user's daily streak. Safe to call on every request —
     * it only writes once per calendar day.
     */
    public static function touchDailyStreak(User $user)
    {
        $today = Carbon::today();
        $last = $user->last_activity_date ? Carbon::parse($user->last_activity_date) : null;

        // Already counted today — nothing to do.
        if ($last && $last->isSameDay($today)) {
            return;
        }

        $oldLevel = (int) ($user->level ?? 1);

        if ($last && $last->isSameDay($today->copy()->subDay())) {
            // Visited yesterday → streak continues.
            $user->streak_count = (int) ($user->streak_count ?? 0) + 1;
        } else {
            // First ever visit, or streak was broken → restart at 1.
            $user->streak_count = 1;
        }

        $user->last_activity_date = $today->toDateString();

        if ((int) ($user->longest_streak ?? 0) < $user->streak_count) {
            $user->longest_streak = $user->streak_count;
        }

        // Daily XP + a small bonus that grows with the streak (capped).
        $streakBonus = min((int) $user->streak_count, 7) * 2;
        self::applyXp($user, self::XP_DAILY_VISIT + $streakBonus);

        // Unlock any newly-earned achievements (safe if that table isn't migrated yet).
        try {
            self::checkAndUnlockAchievements($user);
        } catch (\Throwable $e) {
            // ignore — never block the streak save
        }

        if ((int) $user->level > $oldLevel) {
            self::$events[] = ['type' => 'level', 'level' => (int) $user->level];
        }

        $user->save();
    }

    /**
     * Achievement definitions. Icons are emoji so they render everywhere
     * without depending on an icon font.
     */
    public static function achievements()
    {
        return [
            'welcome'   => ['name' => 'Welcome!',    'desc' => 'Joined the community.',    'icon' => '👋', 'xp' => 20,  'check' => function ($u) { return true; }],
            'streak_3'  => ['name' => 'On Fire',      'desc' => 'Reached a 3-day streak.',  'icon' => '🔥', 'xp' => 30,  'check' => function ($u) { return (int) ($u->streak_count ?? 0) >= 3; }],
            'streak_7'  => ['name' => 'Week Warrior', 'desc' => 'Reached a 7-day streak.',  'icon' => '⚡', 'xp' => 70,  'check' => function ($u) { return (int) ($u->streak_count ?? 0) >= 7; }],
            'streak_14' => ['name' => 'Committed',    'desc' => 'Reached a 14-day streak.', 'icon' => '💪', 'xp' => 140, 'check' => function ($u) { return (int) ($u->streak_count ?? 0) >= 14; }],
            'streak_30' => ['name' => 'Unstoppable',  'desc' => 'Reached a 30-day streak.', 'icon' => '👑', 'xp' => 300, 'check' => function ($u) { return (int) ($u->streak_count ?? 0) >= 30; }],
            'level_5'   => ['name' => 'Rising Star',  'desc' => 'Reached level 5.',         'icon' => '⭐', 'xp' => 50,  'check' => function ($u) { return (int) ($u->level ?? 1) >= 5; }],
            'level_10'  => ['name' => 'Veteran',      'desc' => 'Reached level 10.',        'icon' => '🏆', 'xp' => 100, 'check' => function ($u) { return (int) ($u->level ?? 1) >= 10; }],
            'level_25'  => ['name' => 'Legend',       'desc' => 'Reached level 25.',        'icon' => '💎', 'xp' => 250, 'check' => function ($u) { return (int) ($u->level ?? 1) >= 25; }],
        ];
    }

    /**
     * Grant any achievements the user now qualifies for. XP is applied
     * in-memory; the caller is responsible for persisting the user.
     */
    public static function checkAndUnlockAchievements(User $user)
    {
        $already = UserAchievement::where('user_id', $user->id)->pluck('achievement_key')->all();

        foreach (self::achievements() as $key => $a) {
            if (in_array($key, $already, true)) {
                continue;
            }
            $met = false;
            try {
                $met = (bool) call_user_func($a['check'], $user);
            } catch (\Throwable $e) {
                $met = false;
            }
            if ($met) {
                UserAchievement::create([
                    'user_id' => $user->id,
                    'achievement_key' => $key,
                    'unlocked_at' => Carbon::now(),
                ]);
                self::applyXp($user, $a['xp'] ?? 0);
                self::$events[] = ['type' => 'achievement', 'name' => $a['name'], 'icon' => $a['icon']];
            }
        }
    }

    /**
     * Award XP for an action (posting, tipping, etc.) and persist.
     */
    public static function addXp(User $user, $amount)
    {
        self::applyXp($user, (int) $amount);
        $user->save();
    }

    protected static function applyXp(User $user, $amount)
    {
        $user->xp = (int) ($user->xp ?? 0) + max(0, (int) $amount);
        $user->level = intdiv($user->xp, self::XP_PER_LEVEL) + 1;
    }
}
