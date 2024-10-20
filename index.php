<?php
$receive_data = file_get_contents('php://input');

$content = json_decode($receive_data, true);

const START_MESSAGE = 'سلام. به ربات ایش تاپ خوش آمدید. شما می تونید در این ربات خدمات مربوط به سایت بانک اطلاعات مشاغل ایش تاپ رو دریافت کنید.';
const ERROR_MESSAGE = 'متاسفم، برای سایر پیام ها برنامه نویسی نشده ام.';
const THANK_MESSAGE = 'ممنون که عضو کانال ما شده اید';
const CHANEL_ID = '@khabar_tap';
const REQUEST_JOIN_MESSAGE = 'شما عضو کانال '. CHANEL_ID ." نیستید. ممنون می شویم اگر عضو شوید";

require 'bot.php';


if (isset($content['message']['chat']['id']) && isset($content['message']['text'])) {
    $chat_id = $content['message']['chat']['id'];
    $message = $content['message']['text'];
    $user_id = $content['message']['from']['id'];
    if ($message == '/start') {
        $check_member = msg('getChatMember', array('chat_id' => CHANEL_ID, 'user_id' => $user_id));
        $check_member = json_decode($check_member, true);
        if ($check_member['ok']) {
            if ($check_member['result']['status'] == 'member' || $check_member['result']['status'] == 'creator' || $check_member['result']['status'] == 'administrator')
            {
                msg('sendMessage', array('chat_id' => $chat_id, 'text' => THANK_MESSAGE));
            }else{
                msg('sendMessage', array('chat_id' => $chat_id, 'text' => REQUEST_JOIN_MESSAGE));
            }
        }
        msg('sendMessage', array('chat_id' => $chat_id, 'text' => START_MESSAGE));
    }else{
        msg('sendMessage', array('chat_id' => $chat_id, 'text' => ERROR_MESSAGE));
    }
} else {
    error_log('Invalid data received: ' . $receive_data);
}