<?php

return [
    'driver' => getenv('MAIL_DRIVER') ?: 'smtp',
    'host' => getenv('MAIL_HOST') ?: 'smtp.mailtrap.io',
    'port' => getenv('MAIL_PORT') ?: 2525,
    'username' => getenv('MAIL_USERNAME'),
    'password' => getenv('MAIL_PASSWORD'),
    'encryption' => getenv('MAIL_ENCRYPTION') ?: 'tls',
    'from' => [
        'address' => getenv('MAIL_FROM_ADDRESS') ?: 'noreply@adipati.local',
        'name' => getenv('MAIL_FROM_NAME') ?: 'ADIPATI System',
    ],
];
