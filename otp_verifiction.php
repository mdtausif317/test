<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$MSG91_AUTHKEY = '464047AHvTcZtS68999c6cP1';   // replace with your Auth Key
$SENDER_ID     = 'VEDOTP';               // optional for sendotp message param
// Note: For India use DLT templates. For simple testing, legacy endpoints may work.

// ==== CONFIGURATION ====
$TEMPLATE_ID    = "48572145695358";     // Approved DLT template ID (must include ##OTP##)
$COUNTRY_CODE   = "91";                   // Change as needed
$MOBILE_NUMBER  = "9113307622";         // Full number (country code + mobile)

// ==== Helper function for POST request ====
function http_post_json($url, $data, $authkey) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "authkey: $authkey",
        "Content-Type: application/json"
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    $resp = curl_exec($ch);
    $err  = curl_error($ch);
    curl_close($ch);

    if ($resp === false) {
        return ['success' => false, 'error' => $err];
    }
    return ['success' => true, 'body' => $resp];
}

// ==== Send OTP ====
function send_otp($mobile) {
    global $MSG91_AUTHKEY, $TEMPLATE_ID;
    $url  = "https://control.msg91.com/api/v5/otp";
    $data = [
        "template_id" => $TEMPLATE_ID,
        "mobile"      => $mobile
    ];
    return http_post_json($url, $data, $MSG91_AUTHKEY);
}

// ==== Verify OTP ====
function verify_otp($mobile, $otp) {
    global $MSG91_AUTHKEY;
    $url  = "https://control.msg91.com/api/v5/otp/verify";
    $data = [
        "otp"    => $otp,
        "mobile" => $mobile
    ];
    return http_post_json($url, $data, $MSG91_AUTHKEY);
}

// ==== Resend OTP ====
function resend_otp($mobile, $retryType = "text") {
    global $MSG91_AUTHKEY;
    $url  = "https://control.msg91.com/api/v5/otp/retry";
    $data = [
        "mobile"    => $mobile,
        "retrytype" => $retryType // 'text' or 'voice'
    ];
    return http_post_json($url, $data, $MSG91_AUTHKEY);
}

// ==== DEMO ====

// Format mobile number (country code + number)
$mobile_full = $COUNTRY_CODE . substr($MOBILE_NUMBER, -10);

// 1) Send OTP
$response = send_otp($mobile_full);
echo "Send OTP Response:\n";
print_r($response);

// 2) To verify OTP (replace 123456 with user input)
//$response = verify_otp($mobile_full, "123456");
//echo "Verify OTP Response:\n";
//print_r($response);

// 3) To resend OTP
//$response = resend_otp($mobile_full);
//echo "Resend OTP Response:\n";
//print_r($response);
?>
