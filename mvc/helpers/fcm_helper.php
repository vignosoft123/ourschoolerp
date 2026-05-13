<?php
// Firebase Cloud Messaging helper
require_once FCPATH . 'vendor/autoload.php';

use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;

if (!function_exists('send_fcm_push_bulk')) {
    function send_fcm_push_bulk(array $deviceTokens, $title, $body, $data = [], $imageUrl = null) {
        try {
            $serviceAccountPath = APPPATH . 'third_party/firebase-service-account.json';

            if (!file_exists($serviceAccountPath)) {
                return ['status' => false, 'message' => 'Firebase service account file not found at: ' . $serviceAccountPath];
            }

            $serviceAccountData = json_decode(file_get_contents($serviceAccountPath), true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                return ['status' => false, 'message' => 'Invalid JSON in service account file: ' . json_last_error_msg()];
            }

            $requiredFields = ['type', 'project_id', 'private_key_id', 'private_key', 'client_email', 'client_id'];
            foreach ($requiredFields as $field) {
                if (empty($serviceAccountData[$field])) {
                    return ['status' => false, 'message' => "Missing or empty field in service account: $field"];
                }
            }

            $factory   = (new Factory)->withServiceAccount($serviceAccountPath);
            $messaging = $factory->createMessaging();

            $notification = Notification::create($title, $body, (!empty($imageUrl) ? $imageUrl : null));

            $successCount = 0;
            $failureCount = 0;
            $responses    = [];

            // Send individually — Google removed the /batch multicast endpoint
            foreach ($deviceTokens as $token) {
                try {
                    $message = CloudMessage::withTarget('token', $token)
                        ->withNotification($notification)
                        ->withData($data);
                    $messaging->send($message);
                    $successCount++;
                    $responses[] = ['success' => true];
                } catch (\Kreait\Firebase\Exception\MessagingException $me) {
                    $failureCount++;
                    $responses[] = ['error' => ['message' => $me->getMessage()]];
                }
            }

            return [
                'status'       => true,
                'successCount' => $successCount,
                'failureCount' => $failureCount,
                'responses'    => $responses,
            ];

        } catch (\Kreait\Firebase\Exception\FirebaseException $e) {
            return ['status' => false, 'message' => 'Firebase error: ' . $e->getMessage(), 'error_code' => $e->getCode()];
        } catch (\Exception $e) {
            return ['status' => false, 'message' => 'General error: ' . $e->getMessage(), 'file' => $e->getFile(), 'line' => $e->getLine()];
        }
    }
}
