<?php
/**
 * contact.php
 * Handles the customer inquiry form at the bottom of index.html.
 * Saves the inquiry to the database and optionally emails the agency.
 */

require_once 'config.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Only POST requests are allowed']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
if (!$data) {
    $data = $_POST; // fallback if sent as a normal form post
}

$name    = trim($data['name']    ?? '');
$email   = trim($data['email']   ?? '');
$phone   = trim($data['phone']   ?? '');
$budget  = trim($data['budget']  ?? '');
$message = trim($data['message'] ?? '');
$propertyId = !empty($data['property_id']) ? (int) $data['property_id'] : null;

// ---- Basic validation ----------------------------------------------------
if ($name === '' || $email === '' || $message === '') {
    http_response_code(422);
    echo json_encode(['success' => false, 'error' => 'Name, email, and message are required']);
    exit;
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(422);
    echo json_encode(['success' => false, 'error' => 'Please enter a valid email address']);
    exit;
}

// ---- Save to database ------------------------------------------------------
$stmt = $pdo->prepare(
    "INSERT INTO inquiries (property_id, name, email, phone, budget, message) VALUES (?,?,?,?,?,?)"
);
$stmt->execute([$propertyId, $name, $email, $phone, $budget, $message]);

// ---- Optional: notify the agency by email ----------------------------------
// Uncomment and configure once the server has mail sending set up.
//
// $to      = 'agent@yourdomain.com';
// $subject = 'New property inquiry from ' . $name;
// $body    = "Name: $name\nEmail: $email\nPhone: $phone\nBudget: $budget\n\nMessage:\n$message";
// $headers = "From: no-reply@yourdomain.com\r\nReply-To: $email";
// mail($to, $subject, $body, $headers);

echo json_encode(['success' => true, 'message' => 'Thank you! Your inquiry has been received. Our team will contact you shortly.']);