<?php

return [
    'user_id' => env('TRAWEX_USER_ID'),
    'password' => env('TRAWEX_PASSWORD'),
    'access' => env('TRAWEX_ACCESS', 'Test'),
    'url' => env('TRAWEX_URL', 'https://travelnext.works/api'),
    'ip_address' => env('TRAWEX_IP_ADDRESS', '127.0.0.1'),
];
