<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use App\Models\Meeting;

class MeetingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Response :: view('meetings.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $messages = [
            'meeting-title.required' => 'A meeting title is required',
            'meeting-title.max' => 'The meeting title may not be greater than 256 characters.',
            
            'meeting-desc.required' => 'A meeting description is required.',
            'meeting-desc.max' => 'The meeting description may not be greater than 1024 characters.',
           
            'audio-file.required' => 'An audio file is required.',
            'audio-file.mimes' => 'The audio file must be a mp3, mp4, m4a or wav file.',
            'audio-file.max' => 'The audio file may not be greater than 100MB.',
          ];
        
        // validate the request
        $validatedData = $request->validate([
            'meeting-title' => 'required|max:256',
            'meeting-desc' => 'required|max:1024',
            'audio-file' => 'required|file|mimes:mp3,mp4,m4a,wav|max:102400',
          ], $messages);
          
        // store the meeting
        $audioFile = $request->file('audio-file');

        //create a unique name for the file to not overwrite any existing file
        $fileName = uniqid() . '.' . $audioFile->getClientOriginalExtension();

        //store the file
        $audioFile->storeAs('public/audio', $fileName);

        //create the meeting
        Meeting::create([
            'title' => $validatedData['meeting-title'],
            'description' => $validatedData['meeting-desc'],
            'audio_file' => $fileName,
            'user_id' => auth()->id(),
        ]);
        //route to home
        return redirect()->route('home');

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
