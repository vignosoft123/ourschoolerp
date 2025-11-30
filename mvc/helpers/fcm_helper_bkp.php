<?php
// Firebase Cloud Messaging Helper

defined('BASEPATH') or exit('No direct script access allowed');

// Load Composer autoloader (for Kreait SDK)
require_once FCPATH . 'vendor/autoload.php';

use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;

if (!function_exists('send_fcm_push_bulk')) {
    /**
     * Send a Firebase Cloud Messaging notification to multiple tokens
     *
     * @param array  $deviceTokens
     * @param string $title
     * @param string $body
     * @param array  $data
     * @return array
     */
    function send_fcm_push_bulk(array $deviceTokens, $title, $body, $data = [])
    {
        try {
            // ✅ Make sure this path exists and the JSON file is valid
            $serviceAccountPath = APPPATH . 'third_party/firebase-service-account.json';
            if (!file_exists($serviceAccountPath)) {
                return ['status' => false, 'message' => 'Firebase credentials file not found'];
            }

            // Initialize Firebase
            $factory = (new Factory)->withServiceAccount($serviceAccountPath);
            $messaging = $factory->createMessaging();

            // Create notification
            $notification = Notification::create($title, $body);

            // Create message payload
            $message = CloudMessage::new()
                ->withNotification($notification)
                ->withData($data);

            // Send to multiple tokens
            $report = $messaging->sendMulticast($message, $deviceTokens);

            return [
                'status'       => true,
                'successCount' => $report->successes()->count(),
                'failureCount' => $report->failures()->count(),
                'errors'       => array_map(function ($failure) {
                    return $failure->error()->getMessage();
                }, $report->failures()->getItems())
            ];

        } catch (\Kreait\Firebase\Exception\MessagingException $e) {
            return ['status' => false, 'message' => 'Messaging error: ' . $e->getMessage()];
        } catch (\Kreait\Firebase\Exception\FirebaseException $e) {
            return ['status' => false, 'message' => 'Firebase error: ' . $e->getMessage()];
        } catch (\Throwable $e) {
            return ['status' => false, 'message' => 'General error: ' . $e->getMessage()];
        }
    }
}
