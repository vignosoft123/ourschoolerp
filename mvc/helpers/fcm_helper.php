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
            
            // Check if service account file exists
            if (!file_exists($serviceAccountPath)) {
                return ['status' => false, 'message' => 'Firebase service account file not found at: ' . $serviceAccountPath];
            }
            
            // Validate JSON format
            $serviceAccountData = json_decode(file_get_contents($serviceAccountPath), true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                return ['status' => false, 'message' => 'Invalid JSON in service account file: ' . json_last_error_msg()];
            }
            
            // Check required fields
            $requiredFields = ['type', 'project_id', 'private_key_id', 'private_key', 'client_email', 'client_id'];
            foreach ($requiredFields as $field) {
                if (!isset($serviceAccountData[$field]) || empty($serviceAccountData[$field])) {
                    return ['status' => false, 'message' => "Missing or empty field in service account: $field"];
                }
            }
            
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
            return [
                'status' => false, 
                'message' => 'Messaging error: ' . $e->getMessage(),
                'error_code' => $e->getCode(),
                'error_details' => $e->getTrace()[0] ?? 'No trace available'
            ];
        } catch (\Kreait\Firebase\Exception\FirebaseException $e) {
            return [
                'status' => false, 
                'message' => 'Firebase error: ' . $e->getMessage(),
                'error_code' => $e->getCode(),
                'error_details' => $e->getTrace()[0] ?? 'No trace available'
            ];
        } catch (\Exception $e) {
            return [
                'status' => false, 
                'message' => 'General error: ' . $e->getMessage(),
                'error_code' => $e->getCode(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ];
        }
    }
}
