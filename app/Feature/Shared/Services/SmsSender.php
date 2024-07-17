<?php

namespace App\Feature\Shared\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class SmsSender
{
    private $baseUrl;
    private $user;
    private $key;
    private $senderId;
    private $accUsage;
    private $entityId;
    private $tempId;

    public function __construct()
    {
        $this->baseUrl = config('sms.base_url');
        $this->user = config('sms.user');
        $this->key = config('sms.key');
        $this->senderId = config('sms.sender_id');
        $this->accUsage = config('sms.acc_usage');
        $this->entityId = config('sms.entity_id');
        $this->tempId = config('sms.temp_id');
    }

    public function sendSms(string $mobileNumber, string $message, array $params = []): array
    {
        $url = $this->baseUrl;
        $defaultParams = [
            'user' => $this->user,
            'key' => $this->key,
            'mobile' => $mobileNumber,
            'message' => urlencode($message),
            'senderid' => $this->senderId,
            'accusage' => $this->accUsage,
            'entityid' => $this->entityId,
            'tempid' => $this->tempId,
        ];

        $params = array_merge($defaultParams, $params);

        try {
            $response = Http::get($url, $params);
            $contents = $response->body();
            Log::info("SMS API Response: $contents");

            $res = explode(",", $contents);

            if (trim($res[0]) === "sent" && trim($res[1]) === "success") {
                return ['status' => 'success', 'message' => 'SMS sent successfully'];
            } else {
                throw new \Exception("SMS sending failed: " . implode(" ", $res));
            }
        } catch (\Exception $e) {
            Log::error('Failed to send SMS: ' . $e->getMessage());
            throw $e;
        }
    }
}
