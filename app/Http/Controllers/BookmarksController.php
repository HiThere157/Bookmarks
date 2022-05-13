<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

use App\Models\Bookmark;

class BookmarksController extends Controller
{
    //GET: index page of login
    public function index()
    {
        return view('pages.bookmarks', [
            'bookmarks' => Bookmark::all()
        ]);
    }

    //API GET: get all bookmarks
    public function bookmarks_json()
    {
        return response()->json(Bookmark::all());
    }

    //POST: create a new bookmark
    public function create(Request $request)
    {
        if(!Gate::allows('can-create')) {
            return back()->withErrors([
                'error' => 'You are not allowed to create new Bookmarks. Contanct an admin to get access.'
            ]);
        }

        $request->validate([
            'title' => 'required|max:255',
            'url' => 'required|url|max:255'
        ]);

        $bookmark = new Bookmark;
        $bookmark->title = $request->title;
        $bookmark->url = $request->url;
        $bookmark->created_by_id = Auth::user()->id;
        $bookmark->save();

        Log::info('[BookmarksController@create] User ' . Auth::user()->username . ' created a new bookmark (id: ' . $bookmark->id . ').');
        return redirect()->route('bookmarks');
    }

    //GET: delete a bookmark
    public function delete($id)
    {
        if(!Gate::allows('can-create')) {
            return back()->withErrors([
                'error' => 'You are not allowed to delete Bookmarks. Contanct an admin to get access.'
            ]);
        }

        $bookmark = Bookmark::find($id);
        $bookmark->delete();

        Log::info('[BookmarksController@delete] User ' . Auth::user()->username . ' deleted bookmark (id: ' . $bookmark->id . ').');
        return redirect()->route('bookmarks');
    }

}
