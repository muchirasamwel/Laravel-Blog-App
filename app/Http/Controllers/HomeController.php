<?php

namespace App\Http\Controllers;

use App\Blog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $blogs=Cache::remember('blogs',1,function (){// this will only be updated if expired
            return Blog::all();;
        });
        return view('home')->with(['blogs'=>$blogs,'user'=>$blogs]);
      //  return view('home');
    }
}
