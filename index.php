<?php
$receive_data = file_get_contents('php://input');
$content = json_decode($receive_data, true);

const START_MESSAGE = 'سلام. به ربات ایش تاپ خوش آمدید. شما می تونید در این ربات خدمات مربوط به سایت بانک اطلاعات مشاغل ایش تاپ رو دریافت کنید.';
const THANK_MESSAGE = 'ممنون که عضو کانال ما شده اید';
const CHANEL_ID = '@khabar_tap';
const REQUEST_JOIN_MESSAGE = 'شما عضو کانال ' . CHANEL_ID . " نیستید. ممنون می شویم اگر عضو شوید";
const START_MENU = array(
    'resize_keyboard' => true,
    'inline_keyboard' => array(
        array(
            array('text' => 'ورود به سایت ایش تاپ', 'url' => 'https://ishtap.ir'),
            array('text' => 'تعرفه ها', 'url' => 'https://ishtap.ir/pricing'),
        ),
        array(
            array('text' => 'شرکت در طرح راکت', 'callback_data' => 'join_project'),
        )
    )
);

// فراخوانی فایل‌های مورد نیاز
require 'bot.php';
require_once 'connect.php';

if (isset($content['message']['chat']['id']) && isset($content['message']['text'])) {
    $chat_id = $content['message']['chat']['id'];
    $message = $content['message']['text'];
    $user_id = $content['message']['from']['id'];
    $first_name = $content['message']['from']['first_name'] ?? null;
    $last_name = $content['message']['from']['last_name'] ?? null;
    $username = $content['message']['from']['username'] ?? null;

    if ($message == '/start') {
        saveUserIfNotExists($pdo, $user_id, $first_name, $last_name, $username);

        $check_member = msg('getChatMember', array('chat_id' => CHANEL_ID, 'user_id' => $user_id));
        $check_member = json_decode($check_member, true);
        if ($check_member['ok']) {
            if (in_array($check_member['result']['status'], ['member', 'creator', 'administrator'])) {
                msg('sendMessage', array('chat_id' => $chat_id, 'text' => THANK_MESSAGE, 'reply_markup' => json_encode(START_MENU)));
            } else {
                msg('sendMessage', array('chat_id' => $chat_id, 'text' => REQUEST_JOIN_MESSAGE));
            }
        }
        msg('sendMessage', array('chat_id' => $chat_id, 'text' => START_MESSAGE));
    }
    else{
        $user_state = getUserState($pdo , $chat_id);
        if ($user_state == 'awaiting_instagram_id') {
            $instagram_id = $message;
            msg('sendMessage', array('chat_id' => $chat_id, 'text' => 'instagram_id = '. $instagram_id));
            saveInstagramId($pdo , $chat_id, $instagram_id);

            msg('sendMessage', array('chat_id' => $chat_id, 'text' => 'آیدی اینستاگرام شما با موفقیت ذخیره شد. لطفا نام کسب و کار خود را وارد کنید.'));
            resetUserState($pdo, $chat_id , 'awaiting_job_name');
        }elseif($user_state == 'awaiting_job_name') {
            $job_name = $message;
            saveJobName($pdo , $chat_id, $job_name);
            msg('sendMessage', array('chat_id' => $chat_id, 'text' => 'نام کسب و کار شما با موفقیت ذخیره شد. '));
        }
    }
//    else {msg('sendMessage', array('chat_id' => $chat_id, 'text' => ERROR_MESSAGE));}
}
else {
    error_log('Invalid data received: ' . $receive_data);
}

if (isset($content['callback_query'])) {
    $callback_query = $content['callback_query'];
    $chat_id = $callback_query['from']['id'];
    $callback_data = $callback_query['data'];

    if ($callback_data == 'join_project') {
        msg('sendMessage', array('chat_id' => $chat_id, 'text' => 'لطفاً آیدی اینستاگرام خود را ارسال کنید:'));
        saveUserState($pdo , $chat_id, 'awaiting_instagram_id');
    }
}