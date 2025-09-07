<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\UserAccess;

class SetActingUser
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check() && session()->has('acting_owner_id')) {
            $ownerId = (int) session('acting_owner_id');
            if ($ownerId !== Auth::id()) {
                // Verify still has access; otherwise remove.
                $hasAccess = UserAccess::where('owner_user_id', $ownerId)
                    ->where('invited_user_id', Auth::id())
                    ->whereNotNull('accepted_at')
                    ->exists();
                if (!$hasAccess) {
                    session()->forget('acting_owner_id');
                }
            }
        }
        return $next($request);
    }
}
