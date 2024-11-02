<?php
$receive_data = file_get_contents('php://input');
$content = json_decode($receive_data, true);

const START_MESSAGE = 'سلام. به ربات ایش تاپ خوش آمدید. شما می تونید در این ربات خدمات مربوط به سایت بانک اطلاعات مشاغل ایش تاپ رو دریافت کنید.';
const ERROR_MESSAGE = 'متاسفم، برای سایر پیام ها برنامه نویسی نشده ام.';
const THANK_MESSAGE = 'ممنون که عضو کانال ما شده اید';
const CHANEL_ID = '@khabar_tap';
const REQUEST_JOIN_MESSAGE = 'شما عضو کانال ' . CHANEL_ID . " نیستید. ممنون می شویم اگر عضو شوید";
const START_MENU = array(
    'resize_keyboard' => true,
    'inline_keyboard' => array(
        array(
            array('text' => 'ورود به سایت ایش تاپ', 'url' => 'https://ishtap.ir'),
            array('text' => 'تعرفه ها', 'url' => 'https://ishtap.ir/pricing'),
            array('text' => 'شرکت در طرح راکت', 'callback_data' => 'join_project')
        )
    )
);

// فراخوانی فایل‌های مورد نیاز
require 'bot.php';
require_once 'connect.php';

// تابعی برای ذخیره کاربر در صورت عدم وجود در پایگاه داده
function saveUserIfNotExists($pdo, $user_id, $first_name, $last_name, $username) {
    // بررسی اینکه آیا کاربر قبلاً ثبت شده است یا خیر
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE id = :user_id");
    $stmt->execute(['user_id' => $user_id]);

    if ($stmt->fetchColumn() == 0) { // اگر کاربر وجود ندارد
        // ثبت کاربر جدید در پایگاه داده
        $stmt = $pdo->prepare("INSERT INTO users (id, first_name, last_name, username) VALUES (:user_id, :first_name, :last_name, :username)");
        $stmt->execute([
            'user_id' => $user_id,
            'first_name' => $first_name,
            'last_name' => $last_name,
            'username' => $username
        ]);
    }
}

if (isset($content['message']['chat']['id']) && isset($content['message']['text'])) {
    $chat_id = $content['message']['chat']['id'];
    $message = $content['message']['text'];
    $user_id = $content['message']['from']['id'];
    $first_name = $content['message']['from']['first_name'] ?? null;
    $last_name = $content['message']['from']['last_name'] ?? null;
    $username = $content['message']['from']['username'] ?? null;

    if ($message == '/start') {
        // ذخیره کاربر در پایگاه داده در صورت عدم وجود
        saveUserIfNotExists($pdo, $user_id, $first_name, $last_name, $username);

        // بررسی عضویت کاربر در کانال و ارسال پیام مناسب
        $check_member = msg('getChatMember', array('chat_id' => CHANEL_ID, 'user_id' => $user_id));
        $check_member = json_decode($check_member, true);
        if ($check_member['ok']) {
            if ($check_member['result']['status'] == 'member' || $check_member['result']['status'] == 'creator' || $check_member['result']['status'] == 'administrator') {
                msg('sendMessage', array('chat_id' => $chat_id, 'text' => THANK_MESSAGE, 'reply_markup' => json_encode(START_MENU)));
            } else {
                msg('sendMessage', array('chat_id' => $chat_id, 'text' => REQUEST_JOIN_MESSAGE));
            }
        }
        msg('sendMessage', array('chat_id' => $chat_id, 'text' => START_MESSAGE));
    } else {
        msg('sendMessage', array('chat_id' => $chat_id, 'text' => ERROR_MESSAGE));
    }
} else {
    error_log('Invalid data received: ' . $receive_data);
}
