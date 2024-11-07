<?php
$receive_data = file_get_contents('php://input');
$content = json_decode($receive_data, true);

const THANK_MESSAGE = 'ممنون که عضو کانال ما شده اید';
const CHANEL_ID = '@khabar_tap';
const TOTAL_COUNT_USER = 20;
const DISCOUNT_PERCENT = 95;

const MINIMUM_FOLLOWERS = 1000;
const REQUEST_JOIN_MESSAGE = 'شما عضو کانال ' . CHANEL_ID . " نیستید. ممنون می شویم اگر عضو شوید";
const LAW = "<b>🚀 شرایط شرکت در طرح راکت:</b>\n\n" .
    "1️⃣ شما باید پیج اینستاگرامی با بیش از 1000 نفر دنبال‌کننده داشته باشید. " .
    "\n\n" .
    "2️⃣ دفتر فیزیکی شما باید در مشگین‌شهر باشد. این طرح مخصوص کسب‌وکارهای مشگین‌شهر است.\n\n" .
    "<b>⛔️ توجه:</b> در صورتی که شرایط طرح رعایت نشده باشد، صفحه‌ی شما در سایت <b>معلق</b> خواهد شد.\n\n" ;
const PROCESS = "<b>📋 مراحل کار:</b>\n\n" .
    "1️⃣ ابتدا روی <a href='https://ishtap.ir/pricing' target='_blank'>🔗 تعرفه‌ها</a> کلیک کرده و بررسی کنید " .
    "آیا دسته‌ی شغلی شما در سایت تعریف شده است یا نه. در صورت نبودن دسته شغلی، با <a href='https://t.me/ishtap_site'>پشتیبان</a> تماس بگیرید.\n\n" .

    "2️⃣ روی <a href='https://ishtap.ir/register' target='_blank'>🔗 ثبت نام</a> کلیک کرده و در سایت بانک مشاغل ایش‌تاپ ثبت‌نام کنید.\n\n" .

    "3️⃣ با کلیک روی دکمه‌ی انتهای این پیام، کد تخفیف <b>" . DISCOUNT_PERCENT . " درصدی</b> خود را دریافت کنید.".
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
const LAW_MENU = array(
    'resize_keyboard' => true,
    'inline_keyboard' => array(
        array(
            array('text' => 'مشاهده تعرفه‌ها', 'url' => 'https://ishtap.ir/pricing'),
            array('text' => 'تماس با پشتیبان', 'url' => 'https://t.me/ishtap_site')
        ),
        array(
            array('text' => 'ثبت نام در سایت', 'url' => 'https://ishtap.ir/register')
        ),
        array(
            array('text' => 'دریافت کد تخفیف ۹۵ درصدی', 'callback_data' => 'get_discount')
        ),
        array(
            array('text' => 'ثبت شغل در سایت', 'url' => 'https://ishtap.ir/user/listing/create')
        ),
        array(array(
            'text' => 'ورود به Mini app',
            'web_app' => array('url' => 'https://ishtap.ir/api/tbot/281810766:AAGwctqoowPoqNEH27MlTRUeYFucziJLlUQ') // آدرس مینی اپ
        ))
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

        //send message to admin
        msg('sendMessage', array('chat_id' => ADMIN_ID, 'text' => 'نوید: کاربر با آیدی ' . $user_id . ' و نام کاربری ' . $username . ' وارد ربات شد.'));

        $check_member = msg('getChatMember', array('chat_id' => CHANEL_ID, 'user_id' => $user_id));
        $check_member = json_decode($check_member, true);
        if ($check_member['ok']) {
            if (in_array($check_member['result']['status'], ['member', 'creator', 'administrator'])) {
                msg('sendMessage', array('chat_id' => $chat_id, 'text' => THANK_MESSAGE, 'reply_markup' => json_encode(START_MENU)));
            } else {
                msg('sendMessage', array('chat_id' => $chat_id, 'text' => REQUEST_JOIN_MESSAGE));
            }
        }
    }
    else{
        //پاسخ ها بر اساس وضعیت کاربر
        $user = getUser($pdo , $chat_id);
        $user_state = $user->status;
        if ($user_state == 'awaiting_instagram_id') {

            $new_state = 'awaiting_job_name';
            $instagram_id = $message;
            setUser($pdo , $chat_id , 'instagram_ids' , $instagram_id);

            $count = getInstagramFollowerCount($instagram_id);
            $message_insta = 'آیدی اینستاگرام شما با موفقیت ذخیره شد. لطفا نام کسب و کار خود را وارد کنید.';

            if($count){
                if($count < 1000 ){
                    $message_insta = "شما ".$count." فالور دارید. این تعداد کمتر از حداقل فالور مورد نیاز است.";
                    $new_state = 'completed';
                }else{
                    $message_insta = "شما ".$count." فالور دارید. این تعداد مناسب است. لطفا نام کسب و کار خود را وارد کنید.";
                }
            }
            msg('sendMessage', array('chat_id' => $chat_id, 'text' => $message_insta));
            setUser($pdo , $chat_id , 'status' , $new_state);

        }
        elseif($user_state == 'awaiting_job_name') {
            $job_name = $message;
            setUser($pdo , $chat_id , 'job_name' , $job_name);
            msg('sendMessage', array('chat_id' => $chat_id, 'text' => 'نام کسب و کار شما با موفقیت ذخیره شد. '));
            msg('sendMessage', array(
                'chat_id' => $chat_id,
                'parse_mode' => 'HTML',
                'text' => LAW,
                'reply_markup' => json_encode(array(
                    'inline_keyboard' => array(
                        array(
                            array(
                                'text' => '✅ شرایط رو دارا هستم',
                                'callback_data' => 'have_conditions'
                            ),
                            array(
                                'text' => '❌ شرایط رو ندارم',
                                'callback_data' => 'do_not_have_conditions'
                            )
                        )
                    )
                ))
            ));
            setUser($pdo , $chat_id , 'status' , 'check_conditions');
        }
        else{
            msg('sendMessage', array(
                'chat_id' => ADMIN_ID,
                'text' => "کاربر با آیدی $user_id و نام کاربری $username پیام زیر را فرستاد:\n\"$message\""
            ));
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
    $user_id = $callback_query['from']['id'];
    $user = getUser($pdo, $user_id);
    $user_state = $user->status;

    if ($callback_data == 'join_project') {
        if ($user_state == 'completed') {
            msg('sendMessage', array('chat_id' => $chat_id, 'text' => 'شما قبلا در این طرح شرکت کرده اید'));
        } else {
            $count_completed = countUsers($pdo , 'completed');
            msg('sendMessage', array('chat_id' => $chat_id, 'text' => 'فقط ' . TOTAL_COUNT_USER - $count_completed . ' نفر دیگر تا پایان طرح باقی مانده است '));
            msg('sendMessage', array('chat_id' => $chat_id, 'text' => 'لطفاً آیدی اینستاگرام خود را ارسال کنید:'));
            setUser($pdo , $chat_id , 'status' , 'awaiting_instagram_id');
        }
    }
    elseif ($callback_data == 'have_conditions'){
        if ($user_state == 'check_conditions'){
            msg('sendMessage', array('chat_id' => $chat_id, 'parse_mode' => 'HTML', 'text' => PROCESS , 'reply_markup' => json_encode(LAW_MENU)));
            setUser($pdo , $chat_id , 'status' , 'create_discount_code');
        }else{
            msg('sendMessage', array('chat_id' => $chat_id, 'text' => 'انتخاب این کلید برای شما مجاز نیست'));
        }
    }
    elseif ($callback_data == 'do_not_have_conditions'){
        msg('sendMessage', array('chat_id' => $chat_id, 'text' => 'شما می توانید به صورت عادی در سایت ایش تاپ ثبت نام کنید و از خدمات آن استفاده نمایید.'));
        setUser($pdo , $user_id , 'status' , 'completed');
        msg('editMessageReplyMarkup', array(
            'chat_id' => $chat_id,
            'message_id' => $callback_query['message']['message_id'],
            'reply_markup' => json_encode(array('inline_keyboard' => array()))
        ));
    }
    elseif ($callback_data == 'get_discount') {
        if($user_state == 'create_discount_code'){
            $get_instagram_id = $user->instagram_ids;
            $discount_code = getDiscountCode(DISCOUNT_PERCENT, $get_instagram_id,4);
            $response = sendDiscountDataToSite($discount_code, DISCOUNT_PERCENT , 2 , 2);
            if ($response){
                setUser($pdo , $user_id , 'discount_code' , $discount_code);
                // ارسال پیام معرفی کد تخفیف
                msg('sendMessage', array('chat_id' => $chat_id,'text' => "کد تخفیف شما:"));
                msg('sendMessage', array('chat_id' => $chat_id,'text' => $discount_code));
                setUser($pdo , $user_id , 'status' , 'completed');
            }else{
                msg('sendMessage', array('chat_id' => $chat_id,'text' => "متاسفانه در حال حاضر خطایی در ذخیره ی کد تخفیف در سایت اصلی رخ داد. لطفا با پشتیان سایت تماس بگیرید."));
            }
        }else{
            msg('sendMessage', array('chat_id' => $chat_id,'text' => "شما در مرحله معرفی کد تخفیف نیستید."));
        }

    }
}