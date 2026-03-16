<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/auth.php';

$orderId = isset($_GET['order_id']) ? (int)$_GET['order_id'] : 0;
if (!$orderId) {
    header('Location: index.php');
    exit;
}
$order = $pdo->prepare('SELECT * FROM orders WHERE id = ?');
$order->execute([$orderId]);
$order = $order->fetch();
if (!$order) {
    header('Location: index.php');
    exit;
}
if (isLoggedIn() && (int)$order['user_id'] !== (int)$_SESSION['user_id']) {
    header('Location: index.php');
    exit;
}
if (!isLoggedIn() && $order['user_id']) {
    header('Location: index.php');
    exit;
}

$items = $pdo->prepare('SELECT product_name, quantity, price_at_order FROM order_items WHERE order_id = ?');
$items->execute([$orderId]);
$items = $items->fetchAll();
$total = (float) $order['total_amount'];

$vendor = __DIR__ . '/vendor/autoload.php';
if (!file_exists($vendor)) {
    die('Для скачивания чека установите зависимости: composer install');
}
require_once $vendor;

$dompdf = new \Dompdf\Dompdf();
$addressLine = '';
if (!empty($order['notes']) && preg_match('/Адрес:\s*(.+)/u', $order['notes'], $m)) {
    $addressLine = trim($m[1]);
}

$html = '<!DOCTYPE html><html><head><meta charset="UTF-8"><style>
  body { font-family: DejaVu Sans, sans-serif; color: #35312e; padding: 40px; }
  .logo { color: #d37657; font-size: 24px; font-weight: bold; margin-bottom: 10px; }
  h1 { color: #d37657; font-size: 20px; margin: 20px 0 10px; }
  table { width: 100%; border-collapse: collapse; margin: 20px 0; }
  th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
  th { background: #f5f1e9; }
  .total { font-size: 16px; font-weight: bold; margin-top: 20px; }
  .meta { color: #666; font-size: 11px; margin-bottom: 25px; line-height: 1.5; }
  .footer { margin-top: 40px; color: #999; font-size: 11px; }
</style></head><body>';
$html .= '<div class="logo">Our Place</div>';
$html .= '<h1>Чек по заказу #' . $orderId . '</h1>';
$html .= '<p class="meta">Дата: ' . date('d.m.Y H:i', strtotime($order['created_at'])) . '<br>';
$html .= 'Клиент: ' . htmlspecialchars($order['customer_name']) . '<br>';
$html .= 'Email: ' . htmlspecialchars($order['customer_email']) . '<br>';
if ($order['customer_phone']) $html .= 'Телефон: ' . htmlspecialchars($order['customer_phone']) . '<br>';
if ($addressLine) $html .= 'Адрес: ' . htmlspecialchars($addressLine);
$html .= '</p>';
$html .= '<table><thead><tr><th>Товар</th><th>Кол-во</th><th>Цена</th><th>Сумма</th></tr></thead><tbody>';
foreach ($items as $item) {
    $sub = (float)$item['price_at_order'] * (int)$item['quantity'];
    $html .= '<tr><td>' . htmlspecialchars($item['product_name']) . '</td><td>' . (int)$item['quantity'] . '</td><td>' . number_format($item['price_at_order'], 2) . ' BYN</td><td>' . number_format($sub, 2) . ' BYN</td></tr>';
}
$html .= '</tbody></table><p class="total">Итого: ' . number_format($total, 2) . ' BYN</p>';
$html .= '<p class="footer">Спасибо за заказ! Our Place.</p></body></html>';

$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
$dompdf->stream('receipt_' . $orderId . '.pdf', ['Attachment' => true]);
