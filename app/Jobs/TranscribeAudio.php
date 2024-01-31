<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use OpenAI;
use App\Models\Meeting;

class TranscribeAudio implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    protected $meeting;
    /**
     * Create a new job instance.
     */
    public function __construct(Meeting $meeting)
    {
        $this->meeting = $meeting;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {

        $client = OpenAI::factory()
            ->withBaseUri("https://meetbeat-openai.openai.azure.com/openai/deployments/whisper")
            ->withQueryParam('api-version', '2023-09-01-preview')
            ->withHttpHeader('api-key', '')
            ->make();

        $meeting = $this->meeting;
        $audioFilePath = storage_path('app/public/audio/' . $meeting->audio_file);

        //transcibe the audio file
        $response = $client->audio()->transcribe([
            'model' => 'whisper-1',
            'file' => fopen($audioFilePath, 'r'),
            'response_format' => 'verbose_json',
        ]);

        //store the transcription
        $meeting->update([
            'transcript' => $response->text,
        ]);
    }
}
