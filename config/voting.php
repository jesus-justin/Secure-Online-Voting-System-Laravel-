<?php

return [
    'vote_encryption_key' => env('VOTE_ENCRYPTION_KEY'),
    'enable_ip_validation' => env('ENABLE_IP_VALIDATION', true),
    'enable_device_fingerprint' => env('ENABLE_DEVICE_FINGERPRINT', true),
    'max_vote_attempts' => env('MAX_VOTE_ATTEMPTS', 3),
    'rate_limit_per_minute' => env('RATE_LIMIT_PER_MINUTE', 10),
    'vote_hash_algorithm' => 'sha256',
    'token_expiry_hours' => 24,
];
