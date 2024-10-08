<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use App\Models\Meeting;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use OpenAI;
use App\Jobs\TranscribeAudio;

class MeetingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();
            $meetings = $user->meetings;
        if ($meetings !== null && $meetings->count() > 0) {
            return Response::view('meetings.with-meetings', ['meetings' => $meetings]);
        } else {
            return Response::view('meetings.no-meetings');
        }
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
        $meeting = Meeting::create([
            'title' => $validatedData['meeting-title'],
            'description' => $validatedData['meeting-desc'],
            'audio_file' => $fileName,
            'user_id' => auth()->id(),
        ]);

        TranscribeAudio::dispatch($meeting);

        return redirect()->route('home');
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $meeting = Meeting::findOrFail($id);
        return view('meetings.show', ['meeting' => $meeting]);
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
        $meeting = Meeting::findOrFail($id);

        //delete audio file
        $audioFilePath = 'public/audio/' . $meeting->audio_file;
        if (Storage::exists($audioFilePath)) {
            Storage::delete($audioFilePath);
        }
        //delete entry in database
        $meeting->delete();

        return redirect()->route('home');
    }
}
