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
        try {
            $ApiKey = getenv('AZURE_KEY');
            $client_1 = OpenAI::factory()
            ->withBaseUri("https://meetbeat-openai.openai.azure.com/openai/deployments/whisper")
            ->withQueryParam('api-version', '2023-09-01-preview')
            ->withHttpHeader('api-key', $ApiKey)
            ->make();

            $meeting = $this->meeting;
            $audioFilePath = storage_path('app/public/audio/' . $meeting->audio_file);

        //transcibe the audio file
            $response = $client_1->audio()->transcribe([
            'model' => 'whisper-1',
            'file' => fopen($audioFilePath, 'r'),
            'response_format' => 'verbose_json',
            ]);

        //store the transcription
            $meeting->update([
            'transcript' => $response->text,
            ]);
        } catch (\Exception $e) {
            \Log::error('Error while transcribing: ' . $e->getMessage());
        }

        try {
            $this->processSummary($meeting->transcript);
        } catch (\Exception $e) {
            \Log::error('Error while processing summary: ' . $e->getMessage());
        }
    }

    public function processSummary(String $transcript)
    {


        $ApiKey = getenv('AZURE_KEY');
        $client_2 = OpenAI::factory()
            ->withBaseUri('https://meetbeat-openai.openai.azure.com')
            ->withQueryParam('api-version', '2023-09-01-preview')
            ->withHttpHeader('api-key', $ApiKey)
            ->make();

        $summary = $client_2->chat()->create([
            'model' => 'gpt-3.5-turbo-16k',
            'messages' => [
                ['role' => 'system',
                'content' =>
                'You are an intelligent assistant trained to summarize meeting transcripts.' .
                'You can highlight key points, highlight decisions and identify action items.'],
                [
                    'role' => 'assistant',
                    'content' => 'Here are the key points, action items, and decisions:<br>' .
                    '<ul>' .
                    '<li>Key Point 1</li>' .
                    '<li>Key Point 2</li>' .
                    '</ul>' .
                    '<ul>' .
                    '<li>Action Item 1</li>' .
                    '<li>Action Item 2</li>' .
                    '</ul>' .
                    '<ul>' .
                    '<li>Decision 1</li>' .
                    '<li>Decision 2</li>' .
                    '</ul>'
                ],
                ['role' => 'user', 'content' => $transcript],
            ],
        ]);

        
    }
}
