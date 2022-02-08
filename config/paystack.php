<?php

return [
  'payment_url' => env('PAYSTACK_URL'),
  'secrete_key' => env('PAYSTACK_SEC_KEY'),
  'domain' => env('PAYSTACK_DOMAIN', 'test'),
];
