<?php
// Устанавливаем заголовки
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

// Принимаем только POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Метод не поддерживается.']);
    exit;
}

// Получаем и очищаем данные
$name    = trim(strip_tags($_POST['name'] ?? ''));
$email   = trim(strip_tags($_POST['email'] ?? ''));
$phone   = trim(strip_tags($_POST['phone'] ?? ''));
$topic   = trim(strip_tags($_POST['topic'] ?? ''));
$messageText = trim(strip_tags($_POST['message'] ?? ''));

// Простейшая валидация на сервере
if (empty($name) || empty($email) || empty($topic) || empty($messageText)) {
    echo json_encode(['status' => 'error', 'message' => 'Пожалуйста, заполните все обязательные поля.']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['status' => 'error', 'message' => 'Укажите корректный e-mail адрес.']);
    exit;
}

// === НАСТРОЙКИ ПОЧТЫ (замени на свои) ===
$to = "college-95@yandex.ru";   // <-- СЮДА приходит письмо (директору)
$subject = "=?UTF-8?B?" . base64_encode("Обращение с сайта: " . $topic) . "?=";

// Тело письма (HTML)
$body = "
<html>
<head>
    <meta charset='utf-8'>
    <style>
        body { font-family: Arial, sans-serif; }
        table { border-collapse: collapse; width: 100%; max-width: 600px; }
        td { padding: 10px; border: 1px solid #ddd; }
        .label { background: #f5f5f5; font-weight: bold; width: 150px; }
    </style>
</head>
<body>
    <h2>Новое обращение с сайта</h2>
    <table>
        <tr><td class='label'>Отправитель</td><td>{$name}</td></tr>
        <tr><td class='label'>E-mail</td><td>{$email}</td></tr>
        <tr><td class='label'>Телефон</td><td>" . ($phone ?: 'не указан') . "</td></tr>
        <tr><td class='label'>Тема</td><td>{$topic}</td></tr>
        <tr><td class='label'>Сообщение</td><td>" . nl2br($messageText) . "</td></tr>
    </table>
    <p><small>Письмо отправлено автоматически с формы обратной связи колледжа.</small></p>
</body>
</html>
";

// Заголовки письма
$headers  = "MIME-Version: 1.0\r\n";
$headers .= "Content-type: text/html; charset=utf-8\r\n";
$headers .= "From: =?UTF-8?B?" . base64_encode("Сайт колледжа") . "?= <noreply@" . $_SERVER['HTTP_HOST'] . ">\r\n";
$headers .= "Reply-To: {$email}\r\n";

// Отправляем
$mailSent = mail($to, $subject, $body, $headers);

if ($mailSent) {
    echo json_encode(['status' => 'success', 'message' => 'Спасибо! Ваше обращение успешно отправлено. Мы ответим вам в ближайшее время.']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Не удалось отправить письмо. Пожалуйста, позвоните в приёмную +7 (938) 902-12-22.']);
}
?>