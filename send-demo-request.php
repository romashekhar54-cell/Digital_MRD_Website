<?php
ini_set('display_errors', '0');
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);

$name = trim($input['name'] ?? '');
$hospital = trim($input['hospital'] ?? '');
$email = trim($input['email'] ?? '');
$phone = trim($input['phone'] ?? '');

if ($name === '' || $hospital === '' || $email === '' || $phone === '') {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Please complete all fields.']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Please enter a valid email address.']);
    exit;
}

$cleanName = str_replace(["\r", "\n"], ' ', $name);
$cleanHospital = str_replace(["\r", "\n"], ' ', $hospital);
$cleanEmail = str_replace(["\r", "\n"], '', $email);
$cleanPhone = str_replace(["\r", "\n"], ' ', $phone);

$to = 'info@neolotex.com';
$subject = 'Digital MRD Demo Request from Website';
$message = "New Digital MRD demo request submitted from the website:\n\n";
$message .= "Name: {$cleanName}\n";
$message .= "Hospital / Organization: {$cleanHospital}\n";
$message .= "Email: {$cleanEmail}\n";
$message .= "Phone: {$cleanPhone}\n";

$headers = [];
$headers[] = 'From: NeoLotex Website <sales@neolotex.com>';
$headers[] = "Reply-To: {$cleanName} <{$cleanEmail}>";
$headers[] = 'Content-Type: text/plain; charset=UTF-8';

$sent = @mail($to, $subject, $message, implode("\r\n", $headers));

if (!$sent) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Unable to send your request right now.']);
    exit;
}

echo json_encode(['success' => true]);
