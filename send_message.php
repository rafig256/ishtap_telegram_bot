<?php

require_once 'connect.php';
include_once 'bot.php';

$user_ids = getAllUsers($pdo);

foreach ($user_ids as $userId) {
    // ارسال پیام با تابع msg
    $response = msg('sendMessage', [
        'chat_id' => $userId,
        'text' => 'از تاخیر بوجود آمده عذرخواهی می کنیم. شما از طریق منوی زیر می توانید در طرح راکت سایت ایش تاپ شرکت کنید. در صورتیکه قبلا کد تخفیف دریافت کرده اید نیاز به اقدام دیگری ندارید.',
        'reply_markup' => json_encode(START_MENU)
    ]);

    $response = json_decode($response , true);
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