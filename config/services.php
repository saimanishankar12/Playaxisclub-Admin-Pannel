<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],
    'google' => [
        'sheet_id' => env('GOOGLE_SHEET_ID'),
    ],
      'smsstriker' => [
    'username'            => env('SMSSTRIKER_USERNAME'),
    'password'            => env('SMSSTRIKER_PASSWORD'),
    'sender_id'           => env('SMSSTRIKER_SENDER_ID', 'LORHAN'),
    'template_id_otp'     => env('SMSSTRIKER_TEMPLATE_ID_OTP'),
    'template_id_successmsg' => env('SMSSTRIKER_TEMPLATE_ID_SUCCESSMSG'),
],
//     'razorpay' => [
//     'key_id'     => env('RAZORPAY_KEY_ID'),
//     'key_secret' => env('RAZORPAY_KEY_SECRET'),
//     'webhook_secret' => env('RAZORPAY_WEBHOOK_SECRET'),
// ],

'razorpay' => [
    'key_id'         => env('RAZORPAY_KEY_ID'),
    'key_secret'     => env('RAZORPAY_KEY_SECRET'),
    'webhook_secret' => env('RAZORPAY_WEBHOOK_SECRET'),
],

// 'razorpay' => [
//     'key_id'     => env('RAZORPAY_KEY_ID'),
//     'key_secret' => env('RAZORPAY_KEY_SECRET'),
// ],

];
