<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Admin;
use App\Traits\AdminTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Notification;
use App\Notifications\InvitePeopleNotification;

class AuthController extends Controller
{
    use AdminTrait;
    public function register()
    {
        return inertia("Auth/Register");
    }
    public function login()
    {
        return inertia("Auth/Login");
    }




    public function invite(Request $request)
    {
        if (!$request->hasValidSignature()) {
            abort(401);
        }
        return inertia('Auth/Invite', [
            'guest' => (object)['email' => $request->email, 'admin' => $request->admin]
        ]);
    }


    public function addInvite(Request $request)
    {
        return $this->saveInvite($request);
    }
    public function signout(Request $request)
    {
        User::where('id', Auth::id())->update(['status' => 0]);

        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect(route('login'));
    }
}
