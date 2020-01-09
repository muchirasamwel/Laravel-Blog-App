<?php

namespace App\Http\Controllers;

use App\Blog;
use App\Mail\MyMail;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Log;

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class blogController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $logged_user = Cache::remember('user', 10, function () {// this will only be updated if expired
            return Auth::user();
        });
        //Cache::put('user_cache',$logged_user,10); --illustrates how to use frequent updating of cache
        $blogs = Cache::remember('blogs', 1, function () {// this will only be updated if expired
            return Blog::all();;
        });
        return view('manageblogs')->with(['blogs' => $blogs, 'user' => $logged_user]);
    }

    public function sendNotification($message)
    {
        try {
            $user = Cache::remember('user', 10, function () {// this will only be updated if expired
                return Auth::user();
            });
            $email = $user->email;
            $emaildata = new \stdClass();
            $emaildata->subject = "Blog Notification";
            $emaildata->message = $message;
            Mail::to($email)->send(new MyMail($emaildata));
            return $this->index();
        } catch (\Exception $ex) {
            $logged_user = Cache::remember('user', 10, function () {
                return Auth::user();
            });
            $blogs = Blog::all();
            Log::error('An error occured when sending Mail "Blog notification" ' . $ex);
            return view('manageblogs')->with(['blogs' => $blogs, 'user' => $logged_user, 'errors' => ['Mail send Failed']]);
        }
    }

    public function create(Request $request)
    {

    }

    public function store(Request $request)
    {
        try {
            $blog = Blog::create($request->all());
            return $this->sendNotification("Your Blog '" . $blog->title . "' Has been Published");
        } catch (\Exception $ex) {
            $logged_user = Cache::remember('user', 10, function () {
                return Auth::user();
            });
            $blogs = Blog::all();
            Log::error('An error occured when creating a blog' . $ex);
            if (Str::contains($ex, 'Duplicate entry')) {
                return view('manageblogs')->with(['blogs' => $blogs, 'user' => $logged_user, 'errors' => ['Blog Title Already Exists']]);
            }
            return view('manageblogs')->with(['blogs' => $blogs, 'user' => $logged_user, 'errors' => ['Unexpected error occurred']]);
        }
    }

    public function show($id)
    {
        $blog = DB::table('users')->where('id', $id)->first();
        return view("home")->with(['blogs' => [$blog]]);
    }

    public function edit($id)
    {
        //
    }

    public function update(Request $request, $id)
    {
        //
    }

    public function destroy($id)
    {
//        var_dump($id);
//        $blog = Blog::find($id);
//        $this->sendNotification("Your Blog '" . $blog->title . "' Has been Deleted");
//        Blog::destroy([$id]);
//        return $this->index();
    }
}
