<?php
//  Firebase Cloud Messaging

// File: application/helpers/fcm_helper.php
require_once FCPATH . 'vendor/autoload.php';


use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;
use Kreait\Firebase\Messaging\MulticastSendReport;


if (!function_exists('send_fcm_push_bulk')) {
    function send_fcm_push_bulk(array $deviceTokens, $title, $body, $data = []) {
        try {
            $serviceAccountPath = APPPATH . 'third_party/firebase-service-account.json';
            $factory = (new Factory)->withServiceAccount($serviceAccountPath);
            $messaging = $factory->createMessaging();

            // Create a single notification to send to all devices
            $notification = Notification::create($title, $body);

            $message = CloudMessage::new()
                ->withNotification($notification)
                ->withData($data);

            // Send multicast message
            $report = $messaging->sendMulticast($message, $deviceTokens);

            return [
                'status' => true,
                'successCount' => $report->successes()->count(),
                'failureCount' => $report->failures()->count(),
                'responses' => array_map(function ($response) {
                    return $response->rawData();
                }, $report->responses())
            ];
        } catch (\Kreait\Firebase\Exception\MessagingException $e) {
            return ['status' => false, 'message' => 'Messaging error: ' . $e->getMessage()];
        } catch (\Kreait\Firebase\Exception\FirebaseException $e) {
            return ['status' => false, 'message' => 'Firebase error: ' . $e->getMessage()];
        }
    }
}
