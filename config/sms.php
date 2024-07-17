<?php

return [
    'base_url' => env('SMS_BASE_URL', 'http://mobicomm.dove-sms.com/submitsms.jsp'),
    'user' => env('SMS_USER', 'XYZ'),
    'key' => env('SMS_KEY', 'xyz'),
    'sender_id' => env('SMS_SENDER_ID', 'VTCSMS'),
    'acc_usage' => env('SMS_ACC_USAGE', 1),
    'entity_id' => env('SMS_ENTITY_ID', '10000000000000000000'),
    'temp_id' => env('SMS_TEMP_ID', '0000000000000000001'),
];
