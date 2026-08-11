<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Log;

class SmsGateway
{
    public static function send($to, $message)
    {
        if (get_option('sms_verification_enabled', '0') !== '1') {
            return false;
        }

        $apiKey = get_option('sms_revesms_api_key');
        $secretKey = get_option('sms_revesms_secret_key');
        $callerId = get_option('sms_revesms_caller_id');

        if (!$apiKey || !$secretKey) {
            Log::error('SMS Gateway: Revesms API keys not configured.');
            return false;
        }

        $url = 'http://smpp.revesms.com:7788/send';

        $payload = [
            "apikey" => $apiKey,
            "secretkey" => $secretKey,
            "content" => [
                [
                    "callerID" => $callerId,
                    "toUser" => $to,
                    "messageContent" => strip_tags($message)
                ]
            ]
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        $response = curl_exec($ch);
        
        if (curl_errno($ch)) {
            Log::error('SMS Gateway Error: ' . curl_error($ch));
            $result = false;
        } else {
            $result = json_decode($response);
            Log::info('SMS Gateway Response: ' . $response);
        }
        
        curl_close($ch);
        
        return $result;
    }
}
