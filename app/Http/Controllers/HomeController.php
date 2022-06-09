<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Bookmark;

class HomeController extends Controller
{
    //GET: index home page
    public function index()
    {
        return view('pages.home', [
            'bookmarks' => Bookmark::all()
        ]);
    }
}
