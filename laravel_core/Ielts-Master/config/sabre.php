<?php

return [
    'username' => env('SABRE_USERNAME'),
    'password' => env('SABRE_PASSWORD'),
    'access_token' => env('SABRE_ACCESS_TOKEN'),
    'base_url' => env('SABRE_URL', 'https://api.cert.platform.sabre.com'),
    'token_path' => env('SABRE_TOKEN_PATH', '/v2/auth/token'),
    'shop_path' => env('SABRE_SHOP_PATH', '/v5/offers/shop'),
    'point_of_sale' => env('SABRE_POINT_OF_SALE', 'US'),
    'company_code' => env('SABRE_COMPANY_CODE', 'TN'),
    'pcc' => env('SABRE_PCC'),
    'currency' => env('SABRE_CURRENCY', 'USD'),
    'max_solutions' => (int) env('SABRE_MAX_SOLUTIONS', 20),
];