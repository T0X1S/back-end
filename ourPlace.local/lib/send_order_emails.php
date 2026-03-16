<?php
/**
 * Отправка писем о заказе: уведомление о покупке + чек на email (Gmail SMTP).
 */
function sendOrderEmails($orderId, $customerEmail, $customerName, $cartItems, $cartTotal, $addressLine) {
    $vendor = __DIR__ . '/../vendor/autoload.php';
    if (!file_exists($vendor)) return;
    require_once $vendor;

    $configPath = __DIR__ . '/../config/email.php';
    if (!file_exists($configPath)) return;
    $config = require $configPath;
    if (empty($config['smtp_user']) || empty($config['smtp_pass'])) return;

    $mail = new \PHPMailer\PHPMailer\PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host       = $config['smtp_host'];
        $mail->SMTPAuth   = true;
        $mail->Username   = trim($config['smtp_user']);
        $mail->Password   = str_replace(' ', '', $config['smtp_pass']);
        $mail->SMTPSecure = $config['smtp_secure'] ?? 'tls';
        $mail->Port       = (int) ($config['smtp_port'] ?? 587);
        $mail->CharSet    = 'UTF-8';
        $mail->setFrom($config['from_email'] ?: $config['smtp_user'], $config['from_name'] ?? 'Our Place');
        $mail->addAddress($customerEmail, $customerName);
        $mail->Subject = 'Заказ #' . $orderId . ' — Our Place';
        $mail->isHTML(true);

        $pdfPath = generateReceiptPdf($orderId, $customerName, $customerEmail, $cartItems, $cartTotal, $addressLine);
        $attachmentAdded = false;
        if ($pdfPath && file_exists($pdfPath)) {
            $pathForAttachment = realpath($pdfPath);
            if ($pathForAttachment && is_readable($pathForAttachment)) {
                $mail->addAttachment($pathForAttachment, 'receipt_' . $orderId . '.pdf');
                $attachmentAdded = true;
            }
        }
        $mail->Body = getOrderEmailBody($orderId, $customerName, $cartItems, $cartTotal, $addressLine, $attachmentAdded);
        $mail->send();
        if ($pdfPath && file_exists($pdfPath)) {
            @unlink($pdfPath);
        }
    } catch (\Throwable $e) {
        // письмо не отправлено — заказ уже сохранён, не показываем ошибку пользователю
    }
}

function getOrderEmailBody($orderId, $customerName, $cartItems, $cartTotal, $addressLine, $withAttachment = false) {
    $rows = '';
    foreach ($cartItems as $item) {
        $rows .= '<tr><td>' . htmlspecialchars($item['name']) . '</td><td>' . (int)$item['quantity'] . '</td><td>' . number_format($item['price'], 2) . ' BYN</td><td>' . number_format($item['subtotal'], 2) . ' BYN</td></tr>';
    }
    $footer = $withAttachment ? '<p>Чек во вложении.</p>' : '<p>Чек можно скачать на сайте в истории заказов.</p>';
    return '<!DOCTYPE html><html><head><meta charset="UTF-8"></head><body style="font-family:Roboto,sans-serif;color:#35312e;padding:20px;">' .
        '<h2 style="color:#d37657;">Спасибо за заказ!</h2><p>Здравствуйте, ' . htmlspecialchars($customerName) . '.</p>' .
        '<p>Ваш заказ <strong>#' . $orderId . '</strong> принят.</p>' .
        '<p><strong>Адрес доставки:</strong> ' . htmlspecialchars($addressLine) . '</p>' .
        '<table border="1" cellpadding="8" style="border-collapse:collapse;width:100%;max-width:500px;">' .
        '<thead><tr><th>Товар</th><th>Кол-во</th><th>Цена</th><th>Сумма</th></tr></thead><tbody>' . $rows . '</tbody></table>' .
        '<p><strong>Итого: ' . number_format($cartTotal, 2) . ' BYN</strong></p>' . $footer . '<p>— Our Place</p></body></html>';
}

function generateReceiptPdf($orderId, $customerName, $customerEmail, $cartItems, $cartTotal, $addressLine) {
    $vendor = __DIR__ . '/../vendor/autoload.php';
    if (!file_exists($vendor)) return null;
    require_once $vendor;
    try {
        $dompdf = new \Dompdf\Dompdf();
        $html = '<!DOCTYPE html><html><head><meta charset="UTF-8"><style>body{font-family:DejaVu Sans,sans-serif;color:#35312e;padding:40px;} h1{color:#d37657;font-size:22px;} table{width:100%;border-collapse:collapse;margin:20px 0;} th,td{border:1px solid #ddd;padding:10px;text-align:left;} th{background:#f5f1e9;} .total{font-size:16px;font-weight:bold;margin-top:20px;} .meta{color:#666;font-size:12px;margin-bottom:20px;}</style></head><body>';
        $html .= '<h1>Our Place — Чек #' . $orderId . '</h1><p class="meta">Дата: ' . date('d.m.Y H:i') . '<br>Клиент: ' . htmlspecialchars($customerName) . '<br>Email: ' . htmlspecialchars($customerEmail) . '<br>Адрес: ' . htmlspecialchars($addressLine) . '</p>';
        $html .= '<table><thead><tr><th>Товар</th><th>Кол-во</th><th>Цена</th><th>Сумма</th></tr></thead><tbody>';
        foreach ($cartItems as $item) {
            $html .= '<tr><td>' . htmlspecialchars($item['name']) . '</td><td>' . (int)$item['quantity'] . '</td><td>' . number_format($item['price'], 2) . ' BYN</td><td>' . number_format($item['subtotal'], 2) . ' BYN</td></tr>';
        }
        $html .= '</tbody></table><p class="total">Итого: ' . number_format($cartTotal, 2) . ' BYN</p><p style="margin-top:30px;color:#999;">Our Place.</p></body></html>';
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $dir = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'temp';
        if (!is_dir($dir)) @mkdir($dir, 0755, true);
        $path = $dir . DIRECTORY_SEPARATOR . 'receipt_' . $orderId . '_' . uniqid() . '.pdf';
        $written = @file_put_contents($path, $dompdf->output());
        if ($written === false || !file_exists($path)) return null;
        return $path;
    } catch (Throwable $e) { return null; }
}
