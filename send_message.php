<?php

include_once 'bot.php';

//$user_ids = getAllUsers($pdo);

$user_ids = array(700556696 , 1291617421 , 92514487 , 92514488);

foreach ($user_ids as $userId) {
    // ارسال پیام با تابع msg
    $response = msg('sendMessage', [
        'chat_id' => $userId,
        'text' => 'از تاخیر بوجود آمده عذرخواهی می کنیم. شما از طریق منوی زیر می توانید در طرح راکت سایت ایش تاپ شرکت کنید',
        'reply_markup' => json_encode(START_MENU)
    ]);

    // بررسی نتیجه ارسال پیام
    if ($response && isset($response['ok']) && $response['ok']) {
        echo "Message sent to user ID: {$userId}\n";
    } else {
        // خطا را ثبت می‌کنیم اگر پیام ارسال نشد یا کاربر ربات را بلاک کرده باشد
        error_log("Error sending message to user ID: {$userId}. Response: " . json_encode($response));
    }

    // تاخیر بین هر درخواست برای جلوگیری از محدودیت‌های تلگرام
    sleep(1);
}