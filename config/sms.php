<?php
return [
    'timeout' => env('SMS_CODE_TIMEOUT_MINUTES', 10),
    'trial_limit' => env('SMS_TRIAL_LIMIT', 25),
    'basic_limit' => env('SMS_BASIC_LIMIT', 100),
    'black_label_limit' => env('SMS_BLACK_LABEL_LIMIT', 500),
];