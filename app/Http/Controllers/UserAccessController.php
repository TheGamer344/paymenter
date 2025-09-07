<?php

namespace App\Http\Controllers;

use App\Models\UserAccess;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;

class UserAccessController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        return view('user_access.index', [
            'granted' => $user->grantedAccesses()->get(),
            'shared' => $user->sharedAccounts()->with('owner')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'email' => ['required','email'],
            'permissions' => ['array'],
        ]);
        $user = Auth::user();
        $token = Str::uuid()->toString();

        $access = UserAccess::updateOrCreate(
            ['owner_user_id' => $user->id, 'email' => $data['email']],
            [
                'token' => $token,
                'permissions' => $data['permissions'] ?? [],
            ]
        );

        // TODO: send real mailable / notification
        // Mail::to($data['email'])->send(new UserAccessInvitation($access));

        return redirect()->back()->with('status', 'Invitation sent.');
    }

    public function accept(Request $request, string $token)
    {
        $access = UserAccess::where('token', $token)->firstOrFail();
        if ($access->isAccepted()) {
            return redirect()->route('dashboard')->with('status','Already accepted');
        }
        $user = Auth::user();
        if (!$user) {
            // store intended URL and redirect to login/registration
            return redirect()->route('login')->with('status','Login to accept invitation.');
        }
        // Attach invited user id and mark accepted
        $access->invited_user_id = $user->id;
        $access->accepted_at = now();
        $access->save();
        session(['acting_owner_id' => $access->owner_user_id]);
        return redirect()->route('dashboard')->with('status','Invitation accepted.');
    }

    public function switch(Request $request)
    {
        $request->validate(['owner_id' => ['required','integer']]);
        $user = Auth::user();
        if ($request->owner_id == 'self' || (int)$request->owner_id === (int)$user->id) {
            session()->forget('acting_owner_id');
            return redirect()->route('dashboard');
        }
        $hasAccess = $user->sharedAccounts()->where('owner_user_id', $request->owner_id)->exists();
        if (!$hasAccess) {
            abort(403);
        }
        session(['acting_owner_id' => (int)$request->owner_id]);
        return redirect()->route('dashboard');
    }
}
