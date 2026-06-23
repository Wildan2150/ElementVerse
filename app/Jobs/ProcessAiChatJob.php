<?php

namespace App\Jobs;

use App\Ai\Agents\ChemistryTutorAgent;
use App\Models\AiChatLog;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessAiChatJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 120;
    public $tries = 50; // 50 percobaan untuk menghadapi rate limit Gemini

    public function __construct(
        public AiChatLog $chatLog,
        public string $topicContext = '',
        public ?string $chatbotPrompt = null,
    ) {}

    public function handle(): void
    {
        try {
            $response = (new ChemistryTutorAgent($this->topicContext, $this->chatbotPrompt))
                ->prompt($this->chatLog->prompt);

            if ($response) {
                $this->chatLog->update([
                    'response' => (string) $response,
                ]);
            }
        } catch (\Exception $e) {
            $errorMessage = $e->getMessage();

            if (str_contains($errorMessage, '429') || str_contains($errorMessage, '503') || str_contains($errorMessage, 'overloaded') || str_contains($errorMessage, 'quota')) {
                Log::warning('Gemini rate limit tercapai saat Chatbot berjalan. Menunda antrean 30 detik...');
                $this->release(30);
                return;
            }

            Log::error('ChemistryTutorAgent Exception: '.$errorMessage);
            throw $e;
        }
    }
}