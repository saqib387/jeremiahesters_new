<?php

namespace App\Helpers;

use App\Model\UserList;
use Illuminate\Support\Facades\Auth;

class UserHelper
{
    /**
     * Safely get the blocked list ID for a user
     * This helps avoid the "Call to undefined method TCG\Voyager\Models\User::getBlockedListId()" error
     *
     * @return int|null
     */
    public static function getBlockedListId()
    {
        if (!Auth::check()) {
            return null;
        }
        
        $user = Auth::user();
        
        // If the user model has the method, use it
        if (method_exists($user, 'getBlockedListId')) {
            return $user->getBlockedListId();
        }
        
        // Otherwise get the blocked list manually
        $blockedList = UserList::where('user_id', $user->id)
            ->where('type', 'blocked')
            ->first();
            
        return $blockedList ? $blockedList->id : null;
    }
} 