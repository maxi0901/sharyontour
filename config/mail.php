<?php

declare(strict_types=1);

/**
 * Mail transport configuration. Keep credentials outside the repository.
 */
return [
    'from_email' => getenv('MAIL_FROM_EMAIL') ?: 'no-reply@example.com',
    'from_name' => getenv('MAIL_FROM_NAME') ?: 'S-Art Tour',
    'smtp' => [
        'enabled' => filter_var(getenv('MAIL_SMTP_ENABLED') ?: false, FILTER_VALIDATE_BOOLEAN),
        'host' => getenv('MAIL_SMTP_HOST') ?: '',
        'port' => (int) (getenv('MAIL_SMTP_PORT') ?: 587),
        'username' => getenv('MAIL_SMTP_USER') ?: '',
        'password' => getenv('MAIL_SMTP_PASS') ?: '',
        'encryption' => getenv('MAIL_SMTP_ENCRYPTION') ?: 'tls',
    ],
];
