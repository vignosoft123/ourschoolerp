<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Push_notification extends Admin_Controller {
    /*
    | -----------------------------------------------------
    | PRODUCT NAME: 	INILABS SCHOOL MANAGEMENT SYSTEM
    | -----------------------------------------------------
    | AUTHOR:			INILABS TEAM
    | -----------------------------------------------------
    | EMAIL:			info@inilabs.net
    | -----------------------------------------------------
    | COPYRIGHT:		RESERVED BY INILABS IT
    | -----------------------------------------------------
    | WEBSITE:			http://inilabs.net
    | -----------------------------------------------------
    */

    //FCM
    function __construct() {
        parent::__construct();
    // Load student model
    $this->load->model('student_m');
        
    }

public function send_push_to_students()
{
    $studentIDs = [155,349]; // <-- Replace with real student IDs

    
    $students = $this->student_m->get_tokens_by_studentIDs($studentIDs);

    $deviceTokens = [];
    foreach ($students as $student) {
        if (!empty($student->device_token)) {
            $deviceTokens[] = $student->device_token;
        }
    }
    
    echo "<h3>Debug Information:</h3>";
    echo "Found " . count($deviceTokens) . " device tokens<br>";
    
    if (!empty($deviceTokens)) {
        // Validate token format
        $validTokens = [];
        $invalidTokens = [];
        
        foreach ($deviceTokens as $token) {
            echo "Token: " . substr($token, 0, 20) . "... (length: " . strlen($token) . ")<br>";
            
            // FCM tokens should be 152-163 characters long
            if (strlen($token) >= 140 && strlen($token) <= 170) {
                $validTokens[] = $token;
            } else {
                $invalidTokens[] = $token;
            }
        }
        
        echo "<br>Valid tokens: " . count($validTokens) . "<br>";
        echo "Invalid tokens: " . count($invalidTokens) . "<br>";
        
        if (!empty($invalidTokens)) {
            echo "<strong>Invalid tokens found:</strong><br>";
            foreach ($invalidTokens as $invalidToken) {
                echo "- " . $invalidToken . " (length: " . strlen($invalidToken) . ")<br>";
            }
        }
        
        if (!empty($validTokens)) {
            $title = "Exam Reminder";
            $message = "Dear student, your exam starts tomorrow. Please be prepared.";
            $data = [
                'type' => 'exam_alert',
                'screen' => 'exam_schedule'
            ];

            echo "<br><strong>Sending to valid tokens...</strong><br>";
            $response = send_fcm_push_bulk($validTokens, $title, $message, $data);
            echo "Push sent result: <pre>"; print_r($response); echo "</pre>";
            
            // If we have failures, let's analyze them
            if (isset($response['responses']) && !empty($response['responses'])) {
                echo "<br><strong>Detailed Response Analysis:</strong><br>";
                foreach ($response['responses'] as $index => $responseData) {
                    echo "Token " . ($index + 1) . ": ";
                    if (isset($responseData['error'])) {
                        echo "❌ ERROR - " . $responseData['error']['message'] . "<br>";
                    } else {
                        echo "✅ SUCCESS<br>";
                    }
                }
            }
        } else {
            echo "<strong>No valid tokens to send to!</strong>";
        }
    } else {
        echo "No tokens found.";
    }
}

public function regenerate_service_account()
{
    echo "<h3>Service Account File Generator</h3>";
    echo "<p>If the current service account file continues to have issues, you can manually regenerate it:</p>";
    
    echo "<h4>Step 1: Go to Firebase Console</h4>";
    echo "<ol>";
    echo "<li>Visit <a href='https://console.firebase.google.com/project/ourschoolerp-738d6/settings/serviceaccounts/adminsdk' target='_blank'>Firebase Console - Service Accounts</a></li>";
    echo "<li>Click 'Generate new private key'</li>";
    echo "<li>Download the new JSON file</li>";
    echo "<li>Replace the current file at: " . APPPATH . "third_party/firebase-service-account.json</li>";
    echo "</ol>";
    
    echo "<h4>Step 2: Alternative - Manual Key Creation</h4>";
    echo "<p>You can also create a properly formatted service account file by copying this template and filling in your values:</p>";
    
    $template = [
        "type" => "service_account",
        "project_id" => "ourschoolerp-738d6",
        "private_key_id" => "YOUR_PRIVATE_KEY_ID",
        "private_key" => "-----BEGIN PRIVATE KEY-----\nYOUR_PRIVATE_KEY_CONTENT\n-----END PRIVATE KEY-----\n",
        "client_email" => "firebase-adminsdk-fbsvc@ourschoolerp-738d6.iam.gserviceaccount.com",
        "client_id" => "YOUR_CLIENT_ID",
        "auth_uri" => "https://accounts.google.com/o/oauth2/auth",
        "token_uri" => "https://oauth2.googleapis.com/token",
        "auth_provider_x509_cert_url" => "https://www.googleapis.com/oauth2/v1/certs",
        "client_x509_cert_url" => "https://www.googleapis.com/robot/v1/metadata/x509/firebase-adminsdk-fbsvc%40ourschoolerp-738d6.iam.gserviceaccount.com"
    ];
    
    echo "<textarea style='width: 100%; height: 300px;'>" . json_encode($template, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "</textarea>";
    
    echo "<h4>Step 3: Test After Replacement</h4>";
    echo "<p>After replacing the file, test with: <a href='" . base_url('Push_notification/test_single_token') . "'>test_single_token</a></p>";
}

public function fix_firebase_key()
{
    $serviceAccountPath = APPPATH . 'third_party/firebase-service-account.json';
    
    if (!file_exists($serviceAccountPath)) {
        echo "Service account file not found!";
        return;
    }
    
    $serviceAccountData = json_decode(file_get_contents($serviceAccountPath), true);
    
    if (json_last_error() !== JSON_ERROR_NONE) {
        echo "Invalid JSON in service account file!";
        return;
    }
    
    // Fix the private key formatting
    $originalKey = $serviceAccountData['private_key'];
    $fixedKey = str_replace('\\n', "\n", $originalKey);
    
    echo "<h3>Private Key Analysis:</h3>";
    echo "Original key contains \\n: " . (strpos($originalKey, '\\n') !== false ? 'YES' : 'NO') . "<br>";
    echo "Fixed key contains actual newlines: " . (strpos($fixedKey, "\n") !== false ? 'YES' : 'NO') . "<br>";
    echo "Original key length: " . strlen($originalKey) . "<br>";
    echo "Fixed key length: " . strlen($fixedKey) . "<br>";
    
    if ($originalKey !== $fixedKey) {
        // Create backup
        $backupPath = $serviceAccountPath . '.backup';
        copy($serviceAccountPath, $backupPath);
        echo "<br>✅ Backup created: $backupPath<br>";
        
        // Apply fix
        $serviceAccountData['private_key'] = $fixedKey;
        $result = file_put_contents($serviceAccountPath, json_encode($serviceAccountData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        
        if ($result !== false) {
            echo "✅ Private key formatting fixed!<br>";
            echo "<br>Now try testing again with: <a href='" . base_url('Push_notification/test_single_token') . "'>test_single_token</a>";
        } else {
            echo "❌ Failed to write fixed file!";
        }
    } else {
        echo "<br>Private key formatting is already correct.";
    }
}

public function test_single_token()
{
    // Test with just the first token to isolate issues
    $studentIDs = [155]; // Test with just first student
    $students = $this->student_m->get_tokens_by_studentIDs($studentIDs);

    if (!empty($students) && !empty($students[0]->device_token)) {
        $deviceToken = $students[0]->device_token;
        
        echo "<h3>Testing Single Token:</h3>";
        echo "Token: " . substr($deviceToken, 0, 30) . "...<br>";
        echo "Length: " . strlen($deviceToken) . "<br>";
        
        $title = "Test Notification";
        $message = "This is a test message.";
        $data = ['type' => 'test'];

        $response = send_fcm_push_bulk([$deviceToken], $title, $message, $data);
        echo "<br>Result: <pre>"; print_r($response); echo "</pre>";
        
    } else {
        echo "No token found for student ID 155";
    }
}

public function test_firebase_credentials()
{
    try {
        require_once FCPATH . 'vendor/autoload.php';
        $serviceAccountPath = APPPATH . 'third_party/firebase-service-account.json';
        
        if (!file_exists($serviceAccountPath)) {
            echo "❌ Service account file not found: $serviceAccountPath";
            return;
        }
        
        $serviceAccountData = json_decode(file_get_contents($serviceAccountPath), true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            echo "❌ Invalid JSON: " . json_last_error_msg();
            return;
        }
        
        echo "✅ Service account file found<br>";
        echo "Project ID: " . $serviceAccountData['project_id'] . "<br>";
        echo "Client Email: " . $serviceAccountData['client_email'] . "<br>";
        
        // Test Firebase initialization
        $factory = (new \Kreait\Firebase\Factory)->withServiceAccount($serviceAccountPath);
        $messaging = $factory->createMessaging();
        
        echo "✅ Firebase SDK initialized successfully<br>";
        echo "✅ Messaging service ready<br>";
        
        // Test with a simple validation token (this will fail gracefully if service account is invalid)
        try {
            $testMessage = \Kreait\Firebase\Messaging\CloudMessage::new();
            echo "✅ Message creation successful<br>";
        } catch (\Exception $e) {
            echo "❌ Message creation failed: " . $e->getMessage();
        }
        
    } catch (\Kreait\Firebase\Exception\FirebaseException $e) {
        echo "❌ Firebase error: " . $e->getMessage() . "<br>";
        echo "Code: " . $e->getCode() . "<br>";
    } catch (\Exception $e) {
        echo "❌ General error: " . $e->getMessage() . "<br>";
        echo "File: " . $e->getFile() . "<br>";
        echo "Line: " . $e->getLine() . "<br>";
    }
}

}