<?php

namespace App\Jobs;

use App\Services\WhatsAppService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class SendWhatsAppMessage implements ShouldQueue
{
    use Queueable;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 3;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public string $to,
        public string $message
    ) {}

    /**
     * Execute the job.
     */
    public function handle(WhatsAppService $waService): void
    {
        // 1. Smart Delay: Berikan jeda acak 2-5 detik sebelum kirim
        // Ini membantu menghindari deteksi bot oleh Meta/Provider
        $delay = rand(2, 5);
        sleep($delay);

        Log::info("Sending WA to {$this->to} after {$delay}s delay...");

        // 2. Kirim pesan via service
        $result = $waService->sendMessage($this->to, $this->message);

        if (!$result['success']) {
            // Jika gagal, lempar exception agar di-retry oleh queue worker
            throw new \Exception("WhatsApp delivery failed for {$this->to}: " . ($result['message'] ?? 'Unknown Error'));
        }
    }
}
