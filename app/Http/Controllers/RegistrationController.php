<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

use App\Models\User;

class RegistrationController extends Controller
{
    //GET: index page for registration
    public function index()
    {
        return view('pages.auth.register');
    }

    //POST: register a new user
    public function register(Request $request)
    {
        if(env('LDAP_ENABLED') == 'true'){
            return back()->withErrors([
                'username' => 'LDAP Authentication is enabled. Please contact your administrator.',
            ]);
        }

        $this->validate($request, [
            'username' => 'required|string|min:6|max:255|unique:users',
            'email' => 'required|string|email|max:255',
            'password' => 'required|string|min:6|confirmed',
        ]);

        Log::info('[RegistrationController@register] User ' . $request->input('username') . ' registered.');
        $user = new User;
        $user->username = $request->input('username');
        $user->email = $request->input('email');
        $user->password = Hash::make($request->input('password'));
        $user->save();

        auth()->login($user);

        return redirect()->route('home');
    }
}
