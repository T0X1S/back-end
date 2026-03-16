<?php
/**
 * Скопируйте этот файл как config/email.php и укажите данные Gmail SMTP.
 * Gmail: Включите двухфакторную аутентификацию → Учётная запись Google →
 * Безопасность → Пароли приложений → сгенерируйте пароль для «Почта».
 */
return [
    'smtp_host'   => 'smtp.gmail.com',
    'smtp_port'   => 587,
    'smtp_user'   => 'your@gmail.com',
    'smtp_pass'   => 'xxxx xxxx xxxx xxxx', // 16-символьный пароль приложения
    'smtp_secure' => 'tls',
    'from_email'  => 'your@gmail.com',
    'from_name'   => 'Our Place',
];
