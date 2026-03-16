<?php
/**
 * Настройки Gmail SMTP для отправки писем.
 * Для Gmail: включите 2FA и создайте «Пароль приложения» в аккаунте Google.
 */
return [
    'smtp_host'   => 'smtp.gmail.com',
    'smtp_port'   => 587,
    'smtp_user'   => 'gamerrayx851@gmail.com', // ваш@gmail.com
    'smtp_pass'   => 'gjpf clxf ocgp qxuf', // пароль приложения (16 символов)
    'smtp_secure' => 'tls',
    'from_email'  => 'gamerrayx851@gmail.com', // обычно тот же что smtp_user
    'from_name'   => 'Our Place',
];
