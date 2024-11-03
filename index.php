<?php
$receive_data = file_get_contents('php://input');
$content = json_decode($receive_data, true);

const START_MESSAGE = 'سلام. به ربات ایش تاپ خوش آمدید. شما می تونید در این ربات خدمات مربوط به سایت بانک اطلاعات مشاغل ایش تاپ رو دریافت کنید.';
const THANK_MESSAGE = 'ممنون که عضو کانال ما شده اید';
const CHANEL_ID = '@khabar_tap';
const TOTAL_COUNT_USER = 20;
const REQUEST_JOIN_MESSAGE = 'شما عضو کانال ' . CHANEL_ID . " نیستید. ممنون می شویم اگر عضو شوید";
const LAW = "<b>🚀 شرایط شرکت در طرح راکت:</b>\n\n" .
    "1️⃣ شما باید پیج اینستاگرامی با بیش از 1000 نفر دنبال‌کننده داشته باشید. " .
    "(پس از ایجاد صفحه‌ی شغلی، لینک ایجاد شده را در بخش bio پیج اینستاگرام خود قرار دهید.)\n\n" .
    "2️⃣ دفتر فیزیکی شما باید در مشگین‌شهر باشد. این طرح مخصوص کسب‌وکارهای مشگین‌شهر است.\n\n" .
    "<b>⛔️ توجه:</b> در صورتی که شرایط طرح رعایت نشده باشد، صفحه‌ی شما در سایت <b>معلق</b> خواهد شد.\n\n" .

    "<b>📋 مراحل کار:</b>\n\n" .
    "1️⃣ ابتدا روی <a href='https://ishtap.ir/pricing' target='_blank'>🔗 تعرفه‌ها</a> کلیک کرده و بررسی کنید " .
    "آیا دسته‌ی شغلی شما در سایت تعریف شده است یا نه. در صورت نبودن دسته شغلی، با <a href='https://t.me/ishtap_site'>پشتیبان</a> تماس بگیرید.\n\n" .

    "2️⃣ روی <a href='https://ishtap.ir/register' target='_blank'>🔗 ثبت نام</a> کلیک کرده و در سایت بانک مشاغل ایش‌تاپ ثبت‌نام کنید.\n\n" .

    "3️⃣ با کلیک روی دکمه‌ی انتهای این پیام، کد تخفیف <b>۹۵ درصدی</b> خود را دریافت کنید." .
    "\n(این کد ویژه شماست. لطفا پس از انجام مراحل ۱ و ۲، کد را دریافت کنید. کد تخفیف تاریخ انقضا دارد.)\n\n" .

    "4️⃣ پس از اینکه وارد سایت شدید، شغل خود را <a href='https://ishtap.ir/user/listing/create' target='_blank'>ثبت کرده</a> و با کد تخفیف پرداخت کنید. (هزینه با کد تخفیف بین <b>۱۵ تا ۵۰ هزار تومان</b> خواهد بود.)\n\n".

    "5️⃣ پس از بررسی توسط مدیریت، شغل شما منتشر خواهد شد.\n\n" .

    "<b>🔍 بعد از انتشار:</b> می‌توانید با استفاده از دکمه‌ی آبی رنگ در همین ربات و ورود به مینی‌اپ، وضعیت خود را مشاهده کنید.\n\n" .

    "<b>📈 گارانتی:</b>\n" .
    "اگر ظرف <b>دو هفته</b> پس از انتشار، صفحه‌ی شغلی شما با جستجوی نام کسب‌وکار در صفحه اول گوگل ظاهر نشود، " .
    "هزینه پرداختی عودت داده شده و صفحه شما تا پایان یکسال در سایت باقی خواهد ماند.";
const START_MENU = array(
    'resize_keyboard' => true,
    'inline_keyboard' => array(
        array(
            array('text' => 'ورود به سایت ایش تاپ', 'url' => 'https://ishtap.ir'),
            array('text' => 'تعرفه ها', 'url' => 'https://ishtap.ir/pricing'),
        ),
        array(
            array('text' => 'شرکت در طرح راکت 🚀', 'callback_data' => 'join_project'),
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
        //پاسخ ها بر اساس وضعیت کاربر
        $user_state = getUserState($pdo , $chat_id);
        if ($user_state == 'awaiting_instagram_id') {
            $instagram_id = $message;

            msg('sendMessage', array('chat_id' => $chat_id, 'text' => 'instagram_id = '. $instagram_id));
            saveInstagramId($pdo , $chat_id, $instagram_id);

            msg('sendMessage', array('chat_id' => $chat_id, 'text' => 'آیدی اینستاگرام شما با موفقیت ذخیره شد. لطفا نام کسب و کار خود را وارد کنید.'));
            resetUserState($pdo, $chat_id , 'awaiting_job_name');
        }
        elseif($user_state == 'awaiting_job_name') {
            $job_name = $message;
            saveJobName($pdo , $chat_id, $job_name);
            msg('sendMessage', array('chat_id' => $chat_id, 'text' => 'نام کسب و کار شما با موفقیت ذخیره شد. '));
            msg('sendMessage', array('chat_id' => $chat_id, 'parse_mode' => 'HTML', 'text' => LAW));
            resetUserState($pdo, $chat_id , 'create_discount_code');
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
        $count_completed = countUsers($pdo , 'completed');
        msg('sendMessage', array('chat_id' => $chat_id, 'text' => 'فقط ' . TOTAL_COUNT_USER - $count_completed . ' نفر دیگر تا پایان طرح باقی مانده است '));
        msg('sendMessage', array('chat_id' => $chat_id, 'text' => 'لطفاً آیدی اینستاگرام خود را ارسال کنید:'));
        saveUserState($pdo , $chat_id, 'awaiting_instagram_id');
    }
}