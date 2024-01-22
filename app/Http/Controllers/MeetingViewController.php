<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;

class MeetingViewController extends Controller
{
    public function index()
    {
        //If the user is signed in, show the meetings view depending on if they have a meeting or not
        if (Auth::check()) {
            $user = Auth::user();
            $meetings = $user->meetings;
            

            if ($meetings !== null && $meetings->count() > 0) {
                return Response::view('meetings.with-meetings');
            } else {
                return Response::view('meetings.no-meetings');
            }
        } else {
            //if the user is not signed in, redirect them to the login page
            return redirect()->route('welcome');
        }
    }
}
