<?php
// phpcs:ignoreFile
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
            ->withBaseUri('https://meetbeat-openai.openai.azure.com/openai/deployments/gpt-35-turbo-16k')
            ->withQueryParam('api-version', '2023-06-01-preview')
            ->withHttpHeader('api-key', $ApiKey)
            ->make();

        $summary = $client_2->chat()->create([
            'messages' => [
                ['role' => 'system',
                'content' =>
                "You are an intelligent assistant trained to summarize meeting transcripts. You will Assist in summarizing meeting transcripts.
                You will receive a meeting transcript and are tasked with providing detailed key points, action items, and decisions.
                Think about all the information that was discussed and break it down into the most important points. 
                The format has to stay consistent for every meeting summary.
                Present the information in HTML format as follows:

                    1. **Key Points:**
                       - List key points in an HTML unordered list (<ul>).
                       - If no key points, return a sentence indicating so.
                    
                    2. **Action Items:**
                       - List action items in an HTML unordered list (<ul>).
                       - If no action items, return a sentence indicating so.
                    
                    3. **Decisions:**
                       - List decisions in an HTML unordered list (<ul>).
                       - If no decisions, return a sentence indicating so.
                    
                    Ensure each list is separated by a line break. Your assistant should understand that it's expected to format the response in HTML, 
                    with distinct sections for key points, action items, and decisions, and provide clear messaging if no items are present.
                    
                    Below is a fictional condensed example response demonstrating the structure and format for the HTML Result, 
                    although it only contains a small amount of user types and features.

                    <h2>Key Points</h2><br>
                    <ul>
                    <li>Discussed project timeline and resource allocation.</li>
                    <li>Reviewed progress on deliverables.</li>
                    </ul><br>
                    <h2>Action Items</h2><br>
                    <ul>
                    <li>Assign tasks to team members for next sprint.</li>
                    <li>Schedule follow-up meeting with stakeholders.</li>
                    </ul><br>
                    <h2>Decisions</h2><br>
                    <ul>
                    <li>Approved changes to project scope.</li>
                    <li>Agreed to increase budget for marketing campaign.</li>
                    </ul><br>
                    
                    Remember that the response should be in HTML format. It is not an issue if the response is long, as long as it is accurate and well-structured.
                    I want you to write as my points as possible, but it is important that the response is accurate and well-structured."],
                [
                    'role' => 'assistant',
                    'content' => '<h2>Key Points</h2><br>' .
                    '<ul>' .
                    '<li>Key Point 1</li>' .
                    '<li>Key Point 2</li>' .
                    '</ul><br>' .
                    '<h2>Action Items</h2><br>' .
                    '<ul>' .
                    '<li>Action Item 1</li>' .
                    '<li>Action Item 2</li>' .
                    '</ul><br>' .
                    '<h2>Decisions</h2><br>' . 
                    '<ul>' .
                    '<li>Decision 1</li>' .
                    '<li>Decision 2</li>' .
                    '</ul><br>'
                ],
                ['role' => 'user', 'content' => $transcript],
            ],
        ]);

        $this->meeting->update([
            'summary' => $summary->choices[0]->message->content,
        ]);
            
    }
}
