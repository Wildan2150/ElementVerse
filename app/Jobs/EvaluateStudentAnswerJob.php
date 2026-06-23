<?php

namespace App\Jobs;

use App\Ai\Agents\StudentAnswerEvaluatorAgent;
use App\Models\StudentAnswer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class EvaluateStudentAnswerJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 120;
    public $tries = 50; // 50 percobaan untuk menghadapi rate limit Gemini

    public function __construct(
        public StudentAnswer $answer,
        public ?string $systemPrompt = null,
    ) {}

    public function handle(): void
    {
        $this->answer->load('content');

        $question = $this->answer->content->content_data['question'] ?? 'Pertanyaan tidak diketahui';
        $studentAnswerText = $this->answer->answer_data;

        if (empty($studentAnswerText)) {
            return;
        }

        $userMessage = <<<MSG
PERTANYAAN:
{$question}

JAWABAN SISWA:
{$studentAnswerText}

Berikan evaluasi atau feedbackmu:
MSG;

        try {
            $response = (new StudentAnswerEvaluatorAgent($this->systemPrompt))
                ->prompt($userMessage);

            if ($response) {
                $this->answer->update([
                    'ai_feedback' => (string) $response,
                ]);
            }
        } catch (\Exception $e) {
            $errorMessage = $e->getMessage();

            if (str_contains($errorMessage, '429') || str_contains($errorMessage, '503') || str_contains($errorMessage, 'overloaded') || str_contains($errorMessage, 'quota')) {
                Log::warning('Gemini rate limit tercapai. Menunda antrean selama 60 detik...');
                $this->release(60);
                return;
            }

            Log::error('StudentAnswerEvaluatorAgent Exception: '.$errorMessage);
            throw $e;
        }
    }
}