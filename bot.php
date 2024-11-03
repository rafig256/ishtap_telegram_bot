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

// تابعی برای ذخیره کاربر در صورت عدم وجود در پایگاه داده
function saveUserIfNotExists($pdo, $user_id, $first_name, $last_name, $username) {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE id = :user_id");
    $stmt->execute(['user_id' => $user_id]);

    if ($stmt->fetchColumn() == 0) { // اگر کاربر وجود ندارد
        $stmt = $pdo->prepare("INSERT INTO users (id, first_name, last_name, username) VALUES (:user_id, :first_name, :last_name, :username)");
        $stmt->execute([
            'user_id' => $user_id,
            'first_name' => $first_name,
            'last_name' => $last_name,
            'username' => $username
        ]);
    }
}


// تابع ذخیره وضعیت کاربر
function saveUserState($pdo, $user_id, $state) {
    $query = "UPDATE users SET status = :state WHERE id = :user_id";
    $stmt = $pdo->prepare($query);
    $stmt->execute(['state' => $state, 'user_id' => $user_id]);
}

// تابع بازیابی وضعیت کاربر
function getUserState($pdo, $user_id) {
    $query = "SELECT status FROM users WHERE id = :user_id";
    $stmt = $pdo->prepare($query);
    $stmt->execute(['user_id' => $user_id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result ? $result['status'] : null;
}

function getUser($pdo, $user_id){
    $query = "SELECT * FROM users WHERE id = :user_id";
    $stmt = $pdo->prepare($query);
    $stmt->execute(['user_id' => $user_id]);
    $result = $stmt->fetch(PDO::FETCH_OBJ);
    return $result;
}
// تابع ذخیره آیدی اینستاگرام
function saveInstagramId($pdo, $user_id, $instagram_id) {
    $query = "UPDATE users SET instagram_ids = :instagram_id WHERE id = :user_id";
    $stmt = $pdo->prepare($query);
    $stmt->execute(['instagram_id' => $instagram_id, 'user_id' => $user_id]);
}

function saveJobName($pdo , $user_id, $job_name){
    $query = "UPDATE users SET job_name = :job_name WHERE id = :user_id";
    $stmt = $pdo->prepare($query);
    $stmt->execute(['job_name' => $job_name, 'user_id' => $user_id]);
}

// تابع تنظیم مجدد وضعیت کاربر
function resetUserState($pdo, $user_id , $state ) {
    $query = "UPDATE users SET status = :state WHERE id = :user_id";
    $stmt = $pdo->prepare($query);
    $stmt->execute(['state' => $state, 'user_id' => $user_id]);
}

// تابع بازیابی تعداد کاربرانی که وضعیت مشخصی دارند
function countUsers($pdo , $status) {
    $query = "SELECT COUNT(*) AS count FROM users WHERE status = :status";
    $stmt = $pdo->prepare($query);
    $stmt->execute(['status' => $status]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result ? $result['count'] : 0;
}

function getDiscountCode($percent, $string, $number_random_char): string
{
    $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
    $random = substr(str_shuffle($characters), 0, $number_random_char);

    $discount_code = $percent . '-' . $string . '-' . $random;

    return $discount_code;
}