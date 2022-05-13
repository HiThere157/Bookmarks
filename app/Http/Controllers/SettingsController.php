<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;

use App\Models\User;

class SettingsController extends Controller
{
    //GET: index page of settings
    public function index()
    {
        if(Gate::allows('is-admin')) {
            Log::info('[SettingsController@index_settings] User ' . Auth::user()->username . ' opended settings.');
            return view('pages.auth.settings', ['users' => User::withTrashed()->get()]);
        }
       
        return redirect()->route('home');
    }

    //GET: disable a user
    public function disable($id)
    {
        if(Auth::user()->id == $id) {
            return back()->withErrors([
                'error' => 'You cannot disable yourself.'
            ]);
        }

        if(Gate::allows('is-admin')) {
            $user = User::find($id);
            if($user){
                Log::info('[SettingsController@disable] User ' . Auth::user()->username . ' disabled user ' . $user->username . '.');
                $user->delete();
            }

            return redirect()->route('settings');
        }
       
        return back()->withErrors([
            'error' => 'You are not allowed to disable users.'
        ]);
    }

    //GET: enable a user
    public function enable($id)
    {
        if(Gate::allows('is-admin')) {
            $user = User::withTrashed()->find($id);
            if($user){
                Log::info('[SettingsController@enable] User ' . Auth::user()->username . ' enabled user ' . $user->username . '.');
                $user->restore();
            }

            return redirect()->route('settings');
        }
       
        return back()->withErrors([
            'error' => 'You are not allowed to enable users.'
        ]);
    }

    //GET: promote a user to admin
    public function promote($id)
    {
        if(Gate::allows('is-admin')) {
            $user = User::find($id);
            if($user){
                Log::info('[SettingsController@promote] User ' . Auth::user()->username . ' promoted user ' . $user->username . ' to admin.');
                $user->is_admin = true;
                $user->can_sign = true;
                $user->save();
            }

            return redirect()->route('settings');
        }
       
        return back()->withErrors([
            'error' => 'You are not allowed to promote users.'
        ]);
    }

    //GET: demote a user from admin
    public function demote($id)
    {
        if(Gate::allows('is-admin')) {
            $user = User::find($id);
            if($user){
                Log::info('[SettingsController@demote] User ' . Auth::user()->username . ' demoted user ' . $user->username . ' from admin.');
                $user->is_admin = false;
                $user->save();
            }

            return redirect()->route('settings');
        }
       
        return back()->withErrors([
            'error' => 'You are not allowed to demote users.'
        ]);
    }

    //GET: give a user the ability create new Bookmarks
    public function give_permission($id)
    {
        if(Gate::allows('is-admin')) {
            $user = User::find($id);
            if($user){
                Log::info('[SettingsController@give_permission] User ' . Auth::user()->username . ' gave user ' . $user->username . ' the ability to create new Bookmarks.');
                $user->can_sign = true;
                $user->save();
            }

            return redirect()->route('settings');
        }
       
        return back()->withErrors([
            'error' => 'You are not allowed to give users the ability to create new Bookmarks.'
        ]);
    }

    //GET: revoke a user's ability to create new Bookmars
    public function remove_permission($id)
    {
        if(Gate::allows('is-admin')) {
            $user = User::find($id);
            if($user){
                Log::info('[SettingsController@remove_permission] User ' . Auth::user()->username . ' revoked user ' . $user->username . '\'s ability to create new Bookmarks.');
                $user->can_sign = false;
                $user->save();
            }

            return redirect()->route('settings');
        }
       
        return back()->withErrors([
            'error' => 'You are not allowed to revoke users\'s ability to create new Bookmarks.'
        ]);
    }
}
