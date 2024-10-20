<?php

include_once 'config.php'; //Token
const URL = 'https://api.telegram.org/bot' . TOKEN . '/';

function msg($method, $param) {
    if (!$param) {
        $param = array();
    }
    $handle = curl_init(URL . $method);
    curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($handle, CURLOPT_CONNECTTIMEOUT, 10);
    curl_setopt($handle, CURLOPT_TIMEOUT, 60);
    curl_setopt($handle, CURLOPT_POSTFIELDS, json_encode($param)); // تبدیل آرایه به JSON
    curl_setopt($handle, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
    curl_setopt($handle, CURLOPT_POST, true);
    $result = curl_exec($handle);
    curl_close($handle);

    // برای بررسی خطاها، نتیجه را بررسی کنید:
    if ($result === false) {
        error_log('Curl error: ' . curl_error($handle));
    }
    return $result;
}