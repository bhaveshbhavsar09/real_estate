<?php
/**
 * add_property.php
 * Handles submission of the "Add Property" form (add-property.html).
 * Accepts multipart/form-data POST and inserts a new listing.
 */

require_once 'config.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Only POST requests are allowed']);
    exit;
}

// ---- Validate required fields ------------------------------------------
$required = ['title', 'property_type', 'listing_type', 'price', 'address', 'city', 'latitude', 'longitude'];
foreach ($required as $field) {
    if (empty($_POST[$field])) {
        http_response_code(422);
        echo json_encode(['success' => false, 'error' => "Missing required field: $field"]);
        exit;
    }
}

// ---- Handle image upload (optional) ------------------------------------
$imagePath = null;
if (!empty($_FILES['image']['name'])) {
    $allowed = ['jpg', 'jpeg', 'png', 'webp'];
    $ext     = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));

    if (!in_array($ext, $allowed)) {
        http_response_code(422);
        echo json_encode(['success' => false, 'error' => 'Image must be jpg, jpeg, png, or webp']);
        exit;
    }
    if ($_FILES['image']['size'] > 5 * 1024 * 1024) {
        http_response_code(422);
        echo json_encode(['success' => false, 'error' => 'Image must be under 5MB']);
        exit;
    }

    $uploadDir = __DIR__ . '/../uploads/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    $filename = uniqid('prop_') . '.' . $ext;
    if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadDir . $filename)) {
        $imagePath = 'uploads/' . $filename;
    }
}

// Fall back to a placeholder photo if no image was uploaded
if (!$imagePath) {
    $imagePath = 'https://images.unsplash.com/photo-1560518883-ce09059eeffa?w=900';
}

// ---- Insert into database ------------------------------------------------
$amenities = isset($_POST['amenities']) ? trim($_POST['amenities']) : '';

$stmt = $pdo->prepare(
    "INSERT INTO properties
     (title, property_type, listing_type, price, bedrooms, bathrooms, area_sqft, year_built,
      address, city, state, country, latitude, longitude, description, amenities, image, featured, status)
     VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)"
);

$stmt->execute([
    $_POST['title'],
    $_POST['property_type'],
    $_POST['listing_type'],
    $_POST['price'],
    $_POST['bedrooms']   ?? 0,
    $_POST['bathrooms']  ?? 0,
    $_POST['area_sqft']  ?? 0,
    $_POST['year_built'] ?: null,
    $_POST['address'],
    $_POST['city'],
    $_POST['state']   ?? '',
    $_POST['country'] ?? '',
    $_POST['latitude'],
    $_POST['longitude'],
    $_POST['description'] ?? '',
    $amenities,
    $imagePath,
    isset($_POST['featured']) ? 1 : 0,
    $_POST['status'] ?? 'Available',
]);

echo json_encode([
    'success'     => true,
    'message'     => 'Property added successfully',
    'property_id' => $pdo->lastInsertId(),
]);