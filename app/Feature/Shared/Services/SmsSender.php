<?php

namespace App\Feature\Shared\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

/**
 * Class SmsSender
 *
 * This class is responsible for sending SMS messages using a third-party SMS gateway.
 *
 * Sample Usage in other PHP files or code (below 2 lines):
 *
 * // Resolving the SmsSender instance from the Laravel service container
 * $smsSender = app(SmsSender::class);
 *
 * // Using the resolved instance to send an SMS
 * $result = $smsSender->sendSms($mobileNo, $testsms);
 *
 *
 * @package App\Feature\Shared\Services
 */
class SmsSender
{
    private $baseUrl;
    private $user;
    private $key;
    private $senderId;
    private $accUsage;
    private $entityId;
    private $tempId;

    /**
     * SmsSender constructor.
     * Initializes the class with configuration values from the 'sms' configuration file.
     */
    public function __construct()
    {
        // Load configuration values from config/sms.php
        $this->baseUrl = config('sms.base_url');
        $this->user = config('sms.user');
        $this->key = config('sms.key');
        $this->senderId = config('sms.sender_id');
        $this->accUsage = config('sms.acc_usage');
        $this->entityId = config('sms.entity_id');
        $this->tempId = config('sms.temp_id');
    }

    /**
     * Sends an SMS message to a specified mobile number.
     *
     * @param string $mobileNumber The recipient's mobile number.
     * @param string $message The message to be sent.
     * @param array $params Additional parameters for the SMS API.
     * @return array The result of the SMS sending operation.
     * @throws \Exception If the SMS sending fails.
     */
    public function sendSms(string $mobileNumber, string $message, array $params = []): array
    {
        $url = $this->baseUrl;

        // Default parameters for the SMS API request
        $defaultParams = [
            'user' => $this->user,
            'key' => $this->key,
            'mobile' => $mobileNumber,
            'message' => $message, // No URL encoding required
            'senderid' => $this->senderId,
            'accusage' => $this->accUsage,
            'entityid' => $this->entityId,
            'tempid' => $this->tempId,
        ];

        // Merge default parameters with any additional parameters
        $params = array_merge($defaultParams, $params);

        try {
            // Send the SMS request to the API
            $response = Http::get($url, $params);
            $contents = $response->body();
            Log::info("SMS API Response: $contents");

            // Parse the response
            $res = explode(",", $contents);

            // Check if the SMS was sent successfully
            if (trim($res[0]) === "sent" && trim($res[1]) === "success") {
                return ['status' => 'success', 'message' => 'SMS sent successfully'];
            } else {
                // Throw an exception if the SMS sending failed
                throw new \Exception("SMS sending failed: " . implode(" ", $res));
            }
        } catch (\Exception $e) {
            // Log the error and rethrow the exception
            Log::error('Failed to send SMS: ' . $e->getMessage());
            throw $e;
        }
    }
}
