<?php 
function send_push_notification_bulk($device_tokens, $title, $message, $data = []) {
    $serverKey = 'YOUR_FCM_SERVER_KEY_HERE';

    $payload = [
        "registration_ids" => $device_tokens,
        "notification" => [
            "title" => $title,
            "body"  => $message,
            "sound" => "default"
        ],
        "data" => $data
    ];

    $headers = [
        "Authorization: key=$serverKey",
        "Content-Type: application/json"
    ];

    $ch = curl_init();

    // curl_setopt($ch, CURLOPT_URL, "https://fcm.googleapis.com/fcm/send");
    curl_setopt($ch, CURLOPT_URL, "https://fcm.googleapis.com/v1/projects/ourschoolerp-738d6/messages:send");
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));

    $response = curl_exec($ch);
    curl_close($ch);

    return $response;
}

?>