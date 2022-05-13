<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class BookmarksController extends Controller
{
    //GET: index page of login
    public function index()
    {
        return view('pages.bookmarks');
    }
}
