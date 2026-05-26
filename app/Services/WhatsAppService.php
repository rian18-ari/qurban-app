<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    protected string $token;
    protected string $baseUrl;

    public function __construct()
    {
        // Ambil dari .env atau config
        $this->token = config('services.whatsapp.token', 'MOCK_TOKEN');
        $this->baseUrl = config('services.whatsapp.url', 'https://api.fonnte.com/send');
    }

    /**
     * Mengirim pesan WhatsApp.
     * 
     * @param string $to Nomor tujuan (format: 0812... atau 62812...)
     * @param string $message Isi pesan
     * @return array
     */
    public function sendMessage(string $to, string $message): array
    {
        try {
            // Kita gunakan Fonnte sebagai referensi skeleton
            $response = Http::withHeaders([
                'Authorization' => $this->token,
            ])->post($this->baseUrl, [
                'target' => $to,
                'message' => $message,
                'delay' => '2', // Delay dari sisi provider (jika ada)
                'countryCode' => '62', // Default Indonesia
            ]);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json(),
                ];
            }

            Log::error('WhatsApp API Error: ' . $response->body());
            return [
                'success' => false,
                'message' => 'Failed to send WhatsApp message.',
            ];

        } catch (\Exception $e) {
            Log::error('WhatsApp Service Exception: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }
}
