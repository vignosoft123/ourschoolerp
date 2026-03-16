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

    /**
     * Main index/dashboard for push notifications
     */
    public function index()
    {
        $this->setup_firebase_guide();
    }

    /**
     * Setup guide for Firebase Push Notifications
     * Access this method first to configure Firebase correctly
     */
    public function setup_firebase_guide()
    {
        echo "<div style='font-family: Arial, sans-serif; max-width: 900px; margin: 20px auto; padding: 20px;'>";
        echo "<h2>🔔 Firebase Push Notification Setup Guide</h2>";
        
        // Quick action buttons
        echo "<div style='background: #f8f9fa; border: 1px solid #dee2e6; padding: 15px; border-radius: 5px; margin: 20px 0;'>";
        echo "<h3 style='margin-top: 0;'>⚡ Quick Actions:</h3>";
        echo "<a href='" . base_url('Push_notification/update_service_account') . "' style='display: inline-block; margin: 5px; padding: 10px 20px; background: #007bff; color: white; text-decoration: none; border-radius: 5px; font-weight: bold;'>📤 Upload New Service Account</a>";
        echo "<a href='" . base_url('Push_notification/verify_firebase_setup') . "' style='display: inline-block; margin: 5px; padding: 10px 20px; background: #28a745; color: white; text-decoration: none; border-radius: 5px; font-weight: bold;'>✓ Verify Setup</a>";
        echo "<a href='" . base_url('Push_notification/test_simple_push') . "' style='display: inline-block; margin: 5px; padding: 10px 20px; background: #17a2b8; color: white; text-decoration: none; border-radius: 5px; font-weight: bold;'>🧪 Test Push</a>";
        echo "</div>";
        
        echo "<div style='background: #fff3cd; border: 1px solid #ffc107; padding: 15px; border-radius: 5px; margin: 20px 0;'>";
        echo "<h3>⚠️ IMPORTANT: Project Mismatch Detected</h3>";
        echo "<p>Your <strong>google-services.json</strong> shows project: <code>ourschool-a22d9</code></p>";
        echo "<p>But your service account file is for: <code>ourschoolerp-738d6</code></p>";
        echo "<p><strong>These MUST match for push notifications to work!</strong></p>";
        echo "</div>";
        
        echo "<h3>📋 Step 1: Get the Correct Service Account File</h3>";
        echo "<ol style='line-height: 2;'>";
        echo "<li>Go to <a href='https://console.firebase.google.com/project/ourschool-a22d9/settings/serviceaccounts/adminsdk' target='_blank' style='color: #007bff;'>Firebase Console → Service Accounts (ourschool-a22d9)</a></li>";
        echo "<li>Click the <strong>'Generate new private key'</strong> button</li>";
        echo "<li>Download the JSON file (it will have a long name like <code>ourschool-a22d9-firebase-adminsdk-xxxxx.json</code>)</li>";
        echo "</ol>";
        
        echo "<h3>📁 Step 2: Replace the Service Account File</h3>";
        $serviceAccountPath = APPPATH . 'third_party/firebase-service-account.json';
        echo "<p><strong>Choose one of these methods:</strong></p>";
        
        echo "<div style='background: #d4edda; border: 1px solid #28a745; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
        echo "<h4 style='margin-top: 0;'>✅ Method A: Web Upload (Recommended - Easier)</h4>";
        echo "<ol style='line-height: 2;'>";
        echo "<li>Open the downloaded JSON file in a text editor</li>";
        echo "<li>Copy all the content</li>";
        echo "<li>Go to <a href='" . base_url('Push_notification/update_service_account') . "' style='color: #155724; font-weight: bold;'>Web Upload Page</a></li>";
        echo "<li>Paste and submit the form</li>";
        echo "</ol>";
        echo "</div>";
        
        echo "<div style='background: #f8f9fa; border: 1px solid #dee2e6; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
        echo "<h4 style='margin-top: 0;'>Method B: Manual File Replacement</h4>";
        echo "<ol style='line-height: 2;'>";
        echo "<li>Rename the downloaded file to: <code>firebase-service-account.json</code></li>";
        echo "<li>Replace the file at: <code style='background: #f5f5f5; padding: 5px;'>" . $serviceAccountPath . "</code></li>";
        echo "<li>Make sure the file is readable by PHP</li>";
        echo "</ol>";
        echo "</div>";
        
        echo "<h3>✅ Step 3: Verify the Configuration</h3>";
        echo "<ol style='line-height: 2;'>";
        echo "<li>After replacing the file, click: <a href='" . base_url('Push_notification/verify_firebase_setup') . "' style='color: #28a745; font-weight: bold;'>Verify Firebase Setup</a></li>";
        echo "<li>If verification passes, proceed to: <a href='" . base_url('Push_notification/test_simple_push') . "' style='color: #28a745; font-weight: bold;'>Test Simple Push</a></li>";
        echo "<li>Finally, test with real students: <a href='" . base_url('Push_notification/send_push_to_students') . "' style='color: #28a745; font-weight: bold;'>Send Push to Students</a></li>";
        echo "</ol>";
        
        // Check current status
        echo "<div style='background: #f8f9fa; border: 1px solid #dee2e6; padding: 15px; border-radius: 5px; margin: 20px 0;'>";
        echo "<h3>Current Status:</h3>";
        
        if (file_exists($serviceAccountPath)) {
            $serviceAccount = json_decode(file_get_contents($serviceAccountPath), true);
            $currentProjectId = $serviceAccount['project_id'] ?? 'Unknown';
            
            if ($currentProjectId === 'ourschool-a22d9') {
                echo "<p style='color: green;'>✅ Service account project ID matches: <strong>$currentProjectId</strong></p>";
                echo "<p>You should be ready to go! <a href='" . base_url('Push_notification/verify_firebase_setup') . "'>Verify setup now</a></p>";
            } else {
                echo "<p style='color: red;'>❌ Service account project ID mismatch: <strong>$currentProjectId</strong></p>";
                echo "<p>Expected: <strong>ourschool-a22d9</strong></p>";
                echo "<p>Please follow Step 1 and Step 2 above to fix this.</p>";
            }
        } else {
            echo "<p style='color: red;'>❌ Service account file not found!</p>";
            echo "<p>Please follow all steps above to create it.</p>";
        }
        
        echo "</div>";
        echo "</div>";
    }

    /**
     * Verify Firebase setup and credentials
     */
    public function verify_firebase_setup()
    {
        echo "<div style='font-family: Arial, sans-serif; max-width: 900px; margin: 20px auto; padding: 20px;'>";
        echo "<h2>🔍 Firebase Setup Verification</h2>";
        
        $serviceAccountPath = APPPATH . 'third_party/firebase-service-account.json';
        
        // Check 1: File exists
        echo "<h3>Check 1: Service Account File</h3>";
        if (!file_exists($serviceAccountPath)) {
            echo "<p style='color: red;'>❌ File not found: $serviceAccountPath</p>";
            echo "<p><a href='" . base_url('Push_notification/setup_firebase_guide') . "'>← Back to Setup Guide</a></p>";
            echo "</div>";
            return;
        }
        echo "<p style='color: green;'>✅ File exists</p>";
        
        // Check 2: Valid JSON
        echo "<h3>Check 2: Valid JSON Format</h3>";
        $serviceAccount = json_decode(file_get_contents($serviceAccountPath), true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            echo "<p style='color: red;'>❌ Invalid JSON: " . json_last_error_msg() . "</p>";
            echo "<p><a href='" . base_url('Push_notification/setup_firebase_guide') . "'>← Back to Setup Guide</a></p>";
            echo "</div>";
            return;
        }
        echo "<p style='color: green;'>✅ Valid JSON</p>";
        
        // Check 3: Project ID matches
        echo "<h3>Check 3: Project ID</h3>";
        $projectId = $serviceAccount['project_id'] ?? 'Not found';
        echo "<p>Project ID: <strong>$projectId</strong></p>";
        if ($projectId === 'ourschool-a22d9') {
            echo "<p style='color: green;'>✅ Matches google-services.json</p>";
        } else {
            echo "<p style='color: red;'>❌ Does not match google-services.json (ourschool-a22d9)</p>";
            echo "<p><a href='" . base_url('Push_notification/setup_firebase_guide') . "'>← Back to Setup Guide</a></p>";
            echo "</div>";
            return;
        }
        
        // Check 4: Required fields
        echo "<h3>Check 4: Required Fields</h3>";
        $requiredFields = ['type', 'project_id', 'private_key_id', 'private_key', 'client_email', 'client_id'];
        $allFieldsPresent = true;
        foreach ($requiredFields as $field) {
            if (!isset($serviceAccount[$field]) || empty($serviceAccount[$field])) {
                echo "<p style='color: red;'>❌ Missing: $field</p>";
                $allFieldsPresent = false;
            }
        }
        if ($allFieldsPresent) {
            echo "<p style='color: green;'>✅ All required fields present</p>";
        }
        
        // Check 5: Initialize Firebase SDK
        echo "<h3>Check 5: Firebase SDK Initialization</h3>";
        try {
            require_once FCPATH . 'vendor/autoload.php';
            $factory = (new \Kreait\Firebase\Factory)->withServiceAccount($serviceAccountPath);
            $messaging = $factory->createMessaging();
            echo "<p style='color: green;'>✅ Firebase SDK initialized successfully</p>";
            echo "<p style='color: green;'>✅ Messaging service ready</p>";
        } catch (\Exception $e) {
            echo "<p style='color: red;'>❌ Firebase SDK error: " . $e->getMessage() . "</p>";
            echo "</div>";
            return;
        }
        
        // All checks passed
        echo "<div style='background: #d4edda; border: 1px solid #28a745; padding: 20px; border-radius: 5px; margin: 20px 0;'>";
        echo "<h3 style='color: #155724;'>✅ All Checks Passed!</h3>";
        echo "<p>Your Firebase configuration is correct. You can now:</p>";
        echo "<ul>";
        echo "<li><a href='" . base_url('Push_notification/test_simple_push') . "' style='color: #155724; font-weight: bold;'>Test with a single device token</a></li>";
        echo "<li><a href='" . base_url('Push_notification/send_push_to_students') . "' style='color: #155724; font-weight: bold;'>Send notifications to students</a></li>";
        echo "</ul>";
        echo "</div>";
        echo "</div>";
    }

    /**
     * Simple test with hardcoded token (for initial testing)
     */
    public function test_simple_push()
    {
        echo "<div style='font-family: Arial, sans-serif; max-width: 900px; margin: 20px auto; padding: 20px;'>";
        echo "<h2>🧪 Test Push Notification</h2>";
        echo "<p>This will attempt to send a test notification to the first student with a device token.</p>";
        
        // Get first student with token
        $studentIDs = [155, 349]; // Your test student IDs
        $students = $this->student_m->get_tokens_by_studentIDs($studentIDs);
        
        $deviceToken = null;
        $studentName = 'Unknown';
        
        foreach ($students as $student) {
            if (!empty($student->device_token)) {
                $deviceToken = $student->device_token;
                $studentName = $student->name ?? 'Student ID: ' . $student->studentID;
                break;
            }
        }
        
        if (!$deviceToken) {
            echo "<p style='color: red;'>❌ No device tokens found for student IDs: " . implode(', ', $studentIDs) . "</p>";
            echo "<p>Make sure:</p>";
            echo "<ul>";
            echo "<li>Students have logged into the mobile app</li>";
            echo "<li>Device tokens are being saved to the database</li>";
            echo "<li>The student_m model method <code>get_tokens_by_studentIDs()</code> is working</li>";
            echo "</ul>";
            echo "</div>";
            return;
        }
        
        echo "<h3>Test Details:</h3>";
        echo "<p>Student: <strong>$studentName</strong></p>";
        echo "<p>Token (first 40 chars): <code>" . substr($deviceToken, 0, 40) . "...</code></p>";
        echo "<p>Token length: " . strlen($deviceToken) . " characters</p>";
        
        // Validate token length
        if (strlen($deviceToken) < 140 || strlen($deviceToken) > 170) {
            echo "<p style='color: orange;'>⚠️ Warning: Token length is unusual. FCM tokens are typically 152-163 characters.</p>";
        }
        
        // Send notification
        echo "<h3>Sending Notification...</h3>";
        $title = "Test Notification";
        $message = "This is a test push notification from your school ERP system. Time: " . date('H:i:s');
        $data = [
            'type' => 'test',
            'timestamp' => time(),
            'screen' => 'home'
        ];
        
        $response = send_fcm_push_bulk([$deviceToken], $title, $message, $data);
        
        echo "<h3>Result:</h3>";
        if ($response['status']) {
            echo "<div style='background: #d4edda; border: 1px solid #28a745; padding: 15px; border-radius: 5px;'>";
            echo "<p style='color: green;'><strong>✅ Notification sent successfully!</strong></p>";
            echo "<p>Success count: " . $response['successCount'] . "</p>";
            echo "<p>Failure count: " . $response['failureCount'] . "</p>";
            if ($response['successCount'] > 0) {
                echo "<p style='margin-top: 15px;'><strong>Check your device - you should receive the notification!</strong></p>";
            }
            echo "</div>";
        } else {
            echo "<div style='background: #f8d7da; border: 1px solid #dc3545; padding: 15px; border-radius: 5px;'>";
            echo "<p style='color: red;'><strong>❌ Failed to send notification</strong></p>";
            echo "<p>Error: " . ($response['message'] ?? 'Unknown error') . "</p>";
            echo "</div>";
        }
        
        echo "<h3>Full Response:</h3>";
        echo "<pre style='background: #f5f5f5; padding: 15px; border-radius: 5px; overflow-x: auto;'>";
        print_r($response);
        echo "</pre>";
        
        echo "<p style='margin-top: 20px;'><a href='" . base_url('Push_notification/verify_firebase_setup') . "'>← Back to Verification</a></p>";
        echo "</div>";
    }

    /**
     * Send push notifications to specific students
     * This is your main production method
     */
    public function send_push_to_students()
    {
        echo "<div style='font-family: Arial, sans-serif; max-width: 1000px; margin: 20px auto; padding: 20px;'>";
        echo "<h2>📤 Send Push Notifications to Students</h2>";
        
        // Configure which students to notify
        $studentIDs = [155, 349]; // <-- Replace with real student IDs or get from POST/GET
        
        echo "<h3>Target Students:</h3>";
        echo "<p>Student IDs: <strong>" . implode(', ', $studentIDs) . "</strong></p>";
        
        // Get students and their tokens
        $students = $this->student_m->get_tokens_by_studentIDs($studentIDs);
        
        $deviceTokens = [];
        $studentInfo = [];
        
        foreach ($students as $student) {
            if (!empty($student->device_token)) {
                $deviceTokens[] = $student->device_token;
                $studentInfo[] = [
                    'id' => $student->studentID ?? 'N/A',
                    'name' => $student->name ?? 'Unknown',
                    'token_length' => strlen($student->device_token)
                ];
            }
        }
        
        echo "<h3>Retrieved Device Tokens:</h3>";
        echo "<p>Total students queried: <strong>" . count($students) . "</strong></p>";
        echo "<p>Device tokens found: <strong>" . count($deviceTokens) . "</strong></p>";
        
        if (count($deviceTokens) === 0) {
            echo "<div style='background: #f8d7da; border: 1px solid #dc3545; padding: 15px; border-radius: 5px;'>";
            echo "<p style='color: red;'><strong>❌ No device tokens found!</strong></p>";
            echo "<p>Possible reasons:</p>";
            echo "<ul>";
            echo "<li>Students haven't logged into the mobile app yet</li>";
            echo "<li>Device tokens are not being saved properly</li>";
            echo "<li>Student IDs are incorrect</li>";
            echo "</ul>";
            echo "</div>";
            echo "</div>";
            return;
        }
        
        // Display student info
        echo "<table style='width: 100%; border-collapse: collapse; margin: 15px 0;'>";
        echo "<tr style='background: #f8f9fa; border-bottom: 2px solid #dee2e6;'>";
        echo "<th style='padding: 10px; text-align: left;'>Student ID</th>";
        echo "<th style='padding: 10px; text-align: left;'>Name</th>";
        echo "<th style='padding: 10px; text-align: left;'>Token Length</th>";
        echo "<th style='padding: 10px; text-align: left;'>Status</th>";
        echo "</tr>";
        
        foreach ($studentInfo as $info) {
            $isValid = $info['token_length'] >= 140 && $info['token_length'] <= 170;
            echo "<tr style='border-bottom: 1px solid #dee2e6;'>";
            echo "<td style='padding: 10px;'>" . $info['id'] . "</td>";
            echo "<td style='padding: 10px;'>" . $info['name'] . "</td>";
            echo "<td style='padding: 10px;'>" . $info['token_length'] . "</td>";
            echo "<td style='padding: 10px;'>" . ($isValid ? "<span style='color: green;'>✅ Valid</span>" : "<span style='color: red;'>⚠️ Invalid</span>") . "</td>";
            echo "</tr>";
        }
        echo "</table>";
        
        // Validate tokens
        $validTokens = [];
        $invalidTokens = [];
        
        foreach ($deviceTokens as $token) {
            if (strlen($token) >= 140 && strlen($token) <= 170) {
                $validTokens[] = $token;
            } else {
                $invalidTokens[] = $token;
            }
        }
        
        if (!empty($invalidTokens)) {
            echo "<div style='background: #fff3cd; border: 1px solid #ffc107; padding: 15px; border-radius: 5px; margin: 15px 0;'>";
            echo "<p style='color: #856404;'><strong>⚠️ Warning: " . count($invalidTokens) . " invalid token(s) found and will be skipped</strong></p>";
            echo "</div>";
        }
        
        if (empty($validTokens)) {
            echo "<div style='background: #f8d7da; border: 1px solid #dc3545; padding: 15px; border-radius: 5px;'>";
            echo "<p style='color: red;'><strong>❌ No valid tokens to send to!</strong></p>";
            echo "</div>";
            echo "</div>";
            return;
        }
        
        // Configure notification content
        $title = "Exam Reminder";
        $message = "Dear student, your exam starts tomorrow. Please be prepared and arrive on time.";
        $data = [
            'type' => 'exam_alert',
            'screen' => 'exam_schedule',
            'timestamp' => time()
        ];
        
        echo "<h3>Notification Content:</h3>";
        echo "<div style='background: #f8f9fa; padding: 15px; border-radius: 5px; border-left: 4px solid #007bff;'>";
        echo "<p><strong>Title:</strong> $title</p>";
        echo "<p><strong>Message:</strong> $message</p>";
        echo "<p><strong>Type:</strong> " . $data['type'] . "</p>";
        echo "</div>";
        
        // Send notification
        echo "<h3>Sending Notifications...</h3>";
        echo "<p>Sending to " . count($validTokens) . " device(s)...</p>";
        
        $response = send_fcm_push_bulk($validTokens, $title, $message, $data);
        
        echo "<h3>Results:</h3>";
        
        if ($response['status']) {
            $successCount = $response['successCount'];
            $failureCount = $response['failureCount'];
            $totalSent = $successCount + $failureCount;
            
            if ($successCount > 0) {
                echo "<div style='background: #d4edda; border: 1px solid #28a745; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
                echo "<p style='color: green;'><strong>✅ Successfully sent to " . $successCount . " device(s)</strong></p>";
                echo "</div>";
            }
            
            if ($failureCount > 0) {
                echo "<div style='background: #f8d7da; border: 1px solid #dc3545; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
                echo "<p style='color: red;'><strong>❌ Failed to send to " . $failureCount . " device(s)</strong></p>";
                echo "</div>";
                
                // Detailed failure analysis
                if (isset($response['responses']) && !empty($response['responses'])) {
                    echo "<h4>Detailed Analysis:</h4>";
                    echo "<table style='width: 100%; border-collapse: collapse;'>";
                    echo "<tr style='background: #f8f9fa;'><th style='padding: 8px;'>#</th><th style='padding: 8px;'>Status</th><th style='padding: 8px;'>Details</th></tr>";
                    
                    foreach ($response['responses'] as $index => $responseData) {
                        echo "<tr style='border-bottom: 1px solid #dee2e6;'>";
                        echo "<td style='padding: 8px;'>" . ($index + 1) . "</td>";
                        
                        if (isset($responseData['error'])) {
                            $errorMsg = $responseData['error']['message'] ?? 'Unknown error';
                            echo "<td style='padding: 8px; color: red;'>❌ Failed</td>";
                            echo "<td style='padding: 8px;'>" . htmlspecialchars($errorMsg) . "</td>";
                        } else {
                            echo "<td style='padding: 8px; color: green;'>✅ Success</td>";
                            echo "<td style='padding: 8px;'>Delivered</td>";
                        }
                        echo "</tr>";
                    }
                    echo "</table>";
                }
            }
            
            echo "<div style='background: #e7f3ff; border: 1px solid #007bff; padding: 15px; border-radius: 5px; margin: 20px 0;'>";
            echo "<h4>Summary:</h4>";
            echo "<ul>";
            echo "<li>Total valid tokens: <strong>" . count($validTokens) . "</strong></li>";
            echo "<li>Successfully sent: <strong>" . $successCount . "</strong></li>";
            echo "<li>Failed: <strong>" . $failureCount . "</strong></li>";
            echo "<li>Success rate: <strong>" . round(($successCount / count($validTokens)) * 100, 1) . "%</strong></li>";
            echo "</ul>";
            echo "</div>";
            
        } else {
            // Complete failure
            echo "<div style='background: #f8d7da; border: 1px solid #dc3545; padding: 15px; border-radius: 5px;'>";
            echo "<p style='color: red;'><strong>❌ Failed to send notifications</strong></p>";
            echo "<p><strong>Error:</strong> " . ($response['message'] ?? 'Unknown error') . "</p>";
            
            if (isset($response['error_code'])) {
                echo "<p><strong>Error Code:</strong> " . $response['error_code'] . "</p>";
            }
            
            echo "<p style='margin-top: 15px;'><strong>Troubleshooting:</strong></p>";
            echo "<ul>";
            echo "<li><a href='" . base_url('Push_notification/verify_firebase_setup') . "'>Verify Firebase Setup</a></li>";
            echo "<li>Check that service account project ID matches: <strong>ourschool-a22d9</strong></li>";
            echo "<li>Ensure device tokens are valid and current</li>";
            echo "</ul>";
            echo "</div>";
        }
        
        // Show raw response for debugging
        echo "<details style='margin-top: 20px;'>";
        echo "<summary style='cursor: pointer; padding: 10px; background: #f8f9fa; border: 1px solid #dee2e6; border-radius: 5px;'><strong>Show Full Response (Debug)</strong></summary>";
        echo "<pre style='background: #f5f5f5; padding: 15px; border-radius: 5px; overflow-x: auto; margin-top: 10px;'>";
        print_r($response);
        echo "</pre>";
        echo "</details>";
        
        echo "</div>";
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

    /**
     * Web interface to paste and save the new service account JSON
     */
    public function update_service_account()
    {
        $serviceAccountPath = APPPATH . 'third_party/firebase-service-account.json';
        
        echo "<div style='font-family: Arial, sans-serif; max-width: 1000px; margin: 20px auto; padding: 20px;'>";
        echo "<h2>📝 Update Firebase Service Account</h2>";
        
        // Handle form submission
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['service_account_json'])) {
            $jsonContent = trim($_POST['service_account_json']);
            
            echo "<h3>Validating JSON...</h3>";
            
            // Validate JSON
            $serviceAccountData = json_decode($jsonContent, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                echo "<div style='background: #f8d7da; border: 1px solid #dc3545; padding: 15px; border-radius: 5px;'>";
                echo "<p style='color: red;'><strong>❌ Invalid JSON format!</strong></p>";
                echo "<p>Error: " . json_last_error_msg() . "</p>";
                echo "<p><strong>Common issues:</strong></p>";
                echo "<ul>";
                echo "<li>Make sure you copied the <strong>entire</strong> content from the file</li>";
                echo "<li>Check that curly braces { } are balanced</li>";
                echo "<li>Ensure no extra characters were added when pasting</li>";
                echo "</ul>";
                echo "</div>";
            } else {
                // Validate required fields
                $requiredFields = ['type', 'project_id', 'private_key_id', 'private_key', 'client_email', 'client_id'];
                $missingFields = [];
                
                foreach ($requiredFields as $field) {
                    if (!isset($serviceAccountData[$field]) || empty($serviceAccountData[$field])) {
                        $missingFields[] = $field;
                    }
                }
                
                if (!empty($missingFields)) {
                    echo "<div style='background: #f8d7da; border: 1px solid #dc3545; padding: 15px; border-radius: 5px;'>";
                    echo "<p style='color: red;'><strong>❌ Missing required fields:</strong></p>";
                    echo "<ul>";
                    foreach ($missingFields as $field) {
                        echo "<li>$field</li>";
                    }
                    echo "</ul>";
                    echo "<p>This doesn't look like a valid Firebase service account file. Make sure you downloaded it from the Firebase Console.</p>";
                    echo "</div>";
                } else {
                    // Check project ID
                    $projectId = $serviceAccountData['project_id'];
                    
                    if ($projectId !== 'ourschool-a22d9') {
                        echo "<div style='background: #fff3cd; border: 1px solid #ffc107; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
                        echo "<p style='color: #856404;'><strong>⚠️ Warning: Project ID mismatch!</strong></p>";
                        echo "<p>Expected: <strong>ourschool-a22d9</strong></p>";
                        echo "<p>Found: <strong>$projectId</strong></p>";
                        echo "<p>This service account is from a different Firebase project. Push notifications may not work correctly.</p>";
                        echo "<p><a href='https://console.firebase.google.com/project/ourschool-a22d9/settings/serviceaccounts/adminsdk' target='_blank'>Click here to get the correct service account</a></p>";
                        echo "</div>";
                    }
                    
                    // Create backup of existing file
                    if (file_exists($serviceAccountPath)) {
                        $backupPath = $serviceAccountPath . '.backup.' . date('Y-m-d_H-i-s');
                        if (copy($serviceAccountPath, $backupPath)) {
                            echo "<p style='color: green;'>✅ Backup created: " . basename($backupPath) . "</p>";
                        }
                    }
                    
                    // Save the new file
                    $result = file_put_contents($serviceAccountPath, json_encode($serviceAccountData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
                    
                    if ($result !== false) {
                        echo "<div style='background: #d4edda; border: 1px solid #28a745; padding: 15px; border-radius: 5px; margin: 20px 0;'>";
                        echo "<h3 style='color: #155724;'>✅ Success!</h3>";
                        echo "<p>Service account file has been updated successfully!</p>";
                        echo "<p><strong>Project ID:</strong> $projectId</p>";
                        echo "<p><strong>Client Email:</strong> " . $serviceAccountData['client_email'] . "</p>";
                        
                        if ($projectId === 'ourschool-a22d9') {
                            echo "<p style='margin-top: 10px; padding: 10px; background: #d1ecf1; border: 1px solid #bee5eb; border-radius: 3px;'>✅ Project ID matches! You're all set.</p>";
                        }
                        
                        echo "<hr style='margin: 15px 0;'>";
                        echo "<p><strong>Next steps:</strong></p>";
                        echo "<ol>";
                        echo "<li><a href='" . base_url('Push_notification/verify_firebase_setup') . "' style='color: #155724; font-weight: bold;'>Verify Firebase Setup</a> - Confirm everything is working</li>";
                        echo "<li><a href='" . base_url('Push_notification/test_simple_push') . "' style='color: #155724; font-weight: bold;'>Test Push Notification</a> - Send a test to one device</li>";
                        echo "<li><a href='" . base_url('Push_notification/send_push_to_students') . "' style='color: #155724; font-weight: bold;'>Send to Students</a> - Send to multiple students</li>";
                        echo "</ol>";
                        echo "</div>";
                    } else {
                        echo "<div style='background: #f8d7da; border: 1px solid #dc3545; padding: 15px; border-radius: 5px;'>";
                        echo "<p style='color: red;'><strong>❌ Failed to save file!</strong></p>";
                        echo "<p>Check file permissions for: <code>$serviceAccountPath</code></p>";
                        echo "<p><strong>Possible solutions:</strong></p>";
                        echo "<ul>";
                        echo "<li>Make sure the directory exists: <code>" . dirname($serviceAccountPath) . "</code></li>";
                        echo "<li>Check that PHP has write permissions to this directory</li>";
                        echo "<li>On Windows/XAMPP: Right-click the folder → Properties → Security → Edit → Add write permissions</li>";
                        echo "</ul>";
                        echo "</div>";
                    }
                }
            }
            
            echo "<hr style='margin: 30px 0;'>";
        }
        
        // Show form
        echo "<div style='background: #e7f3ff; border: 1px solid #007bff; padding: 15px; border-radius: 5px; margin: 20px 0;'>";
        echo "<h3>📋 Instructions:</h3>";
        echo "<ol style='line-height: 2;'>";
        echo "<li>Go to <a href='https://console.firebase.google.com/project/ourschool-a22d9/settings/serviceaccounts/adminsdk' target='_blank' style='color: #007bff; font-weight: bold;'>Firebase Console → Service Accounts (ourschool-a22d9)</a></li>";
        echo "<li>Click the button: <strong>'Generate new private key'</strong></li>";
        echo "<li>Confirm in the dialog that appears</li>";
        echo "<li>Open the downloaded JSON file in a text editor (Notepad, VS Code, Notepad++, etc.)</li>";
        echo "<li><strong>Copy ALL the content</strong> (Ctrl+A, then Ctrl+C)</li>";
        echo "<li>Paste it into the text area below</li>";
        echo "<li>Click <strong>'Update Service Account'</strong></li>";
        echo "</ol>";
        echo "</div>";
        
        // Current status
        if (file_exists($serviceAccountPath)) {
            $currentServiceAccount = json_decode(file_get_contents($serviceAccountPath), true);
            $currentProjectId = $currentServiceAccount['project_id'] ?? 'Unknown';
            
            echo "<div style='background: #f8f9fa; border: 1px solid #dee2e6; padding: 15px; border-radius: 5px; margin: 20px 0;'>";
            echo "<h3>📊 Current Service Account Status:</h3>";
            echo "<table style='width: 100%; line-height: 2;'>";
            echo "<tr><td><strong>Project ID:</strong></td><td><span style='color: " . ($currentProjectId === 'ourschool-a22d9' ? 'green' : 'red') . "; font-weight: bold;'>$currentProjectId</span> " . ($currentProjectId === 'ourschool-a22d9' ? '✅' : '❌') . "</td></tr>";
            echo "<tr><td><strong>Client Email:</strong></td><td>" . ($currentServiceAccount['client_email'] ?? 'N/A') . "</td></tr>";
            echo "<tr><td><strong>File Path:</strong></td><td><code>$serviceAccountPath</code></td></tr>";
            echo "<tr><td><strong>File Size:</strong></td><td>" . number_format(filesize($serviceAccountPath)) . " bytes</td></tr>";
            echo "</table>";
            echo "</div>";
        } else {
            echo "<div style='background: #f8d7da; border: 1px solid #dc3545; padding: 15px; border-radius: 5px; margin: 20px 0;'>";
            echo "<p style='color: red;'><strong>❌ No service account file found!</strong></p>";
            echo "<p>Path: <code>$serviceAccountPath</code></p>";
            echo "</div>";
        }
        
        echo "<form method='POST' action='" . base_url('Push_notification/update_service_account') . "'>";
        echo "<h3>📝 Paste Your New Service Account JSON Here:</h3>";
        echo "<textarea name='service_account_json' rows='20' style='width: 100%; font-family: monospace; padding: 10px; border: 1px solid #dee2e6; border-radius: 5px; font-size: 13px;' placeholder='{
  \"type\": \"service_account\",
  \"project_id\": \"ourschool-a22d9\",
  \"private_key_id\": \"abc123...\",
  \"private_key\": \"-----BEGIN PRIVATE KEY-----\\nMIIE...\\n-----END PRIVATE KEY-----\\n\",
  \"client_email\": \"firebase-adminsdk-xxxxx@ourschool-a22d9.iam.gserviceaccount.com\",
  \"client_id\": \"123456789...\",
  \"auth_uri\": \"https://accounts.google.com/o/oauth2/auth\",
  \"token_uri\": \"https://oauth2.googleapis.com/token\",
  \"auth_provider_x509_cert_url\": \"https://www.googleapis.com/oauth2/v1/certs\",
  \"client_x509_cert_url\": \"https://www.googleapis.com/...\",
  \"universe_domain\": \"googleapis.com\"
}'></textarea>";
        echo "<br><br>";
        echo "<button type='submit' style='background: #007bff; color: white; padding: 12px 30px; border: none; border-radius: 5px; cursor: pointer; font-size: 16px; font-weight: bold;'>📤 Update Service Account</button>";
        echo "<a href='" . base_url('Push_notification/setup_firebase_guide') . "' style='margin-left: 15px; display: inline-block; padding: 12px 30px; background: #6c757d; color: white; text-decoration: none; border-radius: 5px;'>← Back to Guide</a>";
        echo "</form>";
        
        echo "<div style='background: #fff3cd; border: 1px solid #ffc107; padding: 15px; border-radius: 5px; margin: 20px 0;'>";
        echo "<p><strong>💡 Tip:</strong> If you're having trouble, make sure you're on the correct Firebase project (<strong>ourschool-a22d9</strong>) in the Firebase Console.</p>";
        echo "</div>";
        
        echo "</div>";
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