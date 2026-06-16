<?php
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Метод не поддерживается.']);
    exit;
}

$name    = trim(strip_tags($_POST['senderName'] ?? ''));
$email   = trim(strip_tags($_POST['senderEmail'] ?? ''));
$theme   = trim(strip_tags($_POST['messageTheme'] ?? ''));
$messageText = trim(strip_tags($_POST['messageText'] ?? ''));

if (empty($name) || empty($email) || empty($theme) || empty($messageText)) {
    echo json_encode(['status' => 'error', 'message' => 'Пожалуйста, заполните все обязательные поля.']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['status' => 'error', 'message' => 'Укажите корректный e-mail адрес.']);
    exit;
}

// ⚠️ ЗАМЕНИ НА РЕАЛЬНУЮ ПОЧТУ
$to = "college-95@yandex.ru";
$subject = "=?UTF-8?B?" . base64_encode("Вопрос по питанию: " . $theme) . "?=";

$body = "
<html><head><meta charset='utf-8'></head><body>
<h2>Новое сообщение с формы обратной связи (питание)</h2>
<table style='border-collapse:collapse;width:100%;max-width:600px;'>
<tr><td style='padding:10px;border:1px solid #ddd;background:#f5f5f5;font-weight:bold;'>Отправитель</td><td style='padding:10px;border:1px solid #ddd;'>{$name}</td></tr>
<tr><td style='padding:10px;border:1px solid #ddd;background:#f5f5f5;font-weight:bold;'>E-mail</td><td style='padding:10px;border:1px solid #ddd;'>{$email}</td></tr>
<tr><td style='padding:10px;border:1px solid #ddd;background:#f5f5f5;font-weight:bold;'>Тема</td><td style='padding:10px;border:1px solid #ddd;'>{$theme}</td></tr>
<tr><td style='padding:10px;border:1px solid #ddd;background:#f5f5f5;font-weight:bold;'>Сообщение</td><td style='padding:10px;border:1px solid #ddd;'>" . nl2br($messageText) . "</td></tr>
</table>
<p><small>Письмо отправлено автоматически с формы обратной связи колледжа.</small></p>
</body></html>
";

$headers  = "MIME-Version: 1.0\r\n";
$headers .= "Content-type: text/html; charset=utf-8\r\n";
$headers .= "From: =?UTF-8?B?" . base64_encode("Сайт колледжа") . "?= <noreply@" . $_SERVER['HTTP_HOST'] . ">\r\n";
$headers .= "Reply-To: {$email}\r\n";

$mailSent = mail($to, $subject, $body, $headers);

if ($mailSent) {
    echo json_encode(['status' => 'success', 'message' => 'Спасибо! Ваше сообщение успешно отправлено.']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Не удалось отправить письмо. Пожалуйста, позвоните в приёмную.']);
}
?>