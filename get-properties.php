<?php
/**
 * get_properties.php
 * Returns property listings as JSON. Supports optional filters via GET:
 *   ?type=House|Apartment|Villa|Plot|Commercial|Penthouse
 *   ?listing=Sale|Rent
 *   ?city=Austin
 *   ?min_price=100000&max_price=500000
 *   ?bedrooms=3           (3 or more)
 *   ?featured=1           (only featured listings)
 *   ?id=7                 (single property, returns one object)
 *   ?search=keyword       (matches title, city, or address)
 */

require_once 'config.php';
header('Content-Type: application/json');

// ---- Single property lookup -------------------------------------------
if (!empty($_GET['id'])) {
    $stmt = $pdo->prepare("SELECT * FROM properties WHERE id = ?");
    $stmt->execute([(int) $_GET['id']]);
    $property = $stmt->fetch();

    if (!$property) {
        http_response_code(404);
        echo json_encode(['success' => false, 'error' => 'Property not found']);
        exit;
    }
    $property['amenities'] = $property['amenities'] ? explode(',', $property['amenities']) : [];
    echo json_encode(['success' => true, 'property' => $property]);
    exit;
}

// ---- Build filtered listing query --------------------------------------
$where  = [];
$params = [];

if (!empty($_GET['type'])) {
    $where[]  = 'property_type = ?';
    $params[] = $_GET['type'];
}
if (!empty($_GET['listing'])) {
    $where[]  = 'listing_type = ?';
    $params[] = $_GET['listing'];
}
if (!empty($_GET['city'])) {
    $where[]  = 'city LIKE ?';
    $params[] = '%' . $_GET['city'] . '%';
}
if (!empty($_GET['min_price'])) {
    $where[]  = 'price >= ?';
    $params[] = (float) $_GET['min_price'];
}
if (!empty($_GET['max_price'])) {
    $where[]  = 'price <= ?';
    $params[] = (float) $_GET['max_price'];
}
if (!empty($_GET['bedrooms'])) {
    $where[]  = 'bedrooms >= ?';
    $params[] = (int) $_GET['bedrooms'];
}
if (!empty($_GET['featured'])) {
    $where[] = 'featured = 1';
}
if (!empty($_GET['search'])) {
    $where[]  = '(title LIKE ? OR city LIKE ? OR address LIKE ?)';
    $like     = '%' . $_GET['search'] . '%';
    $params[] = $like;
    $params[] = $like;
    $params[] = $like;
}

$sql = "SELECT * FROM properties";
if ($where) {
    $sql .= " WHERE " . implode(' AND ', $where);
}
$sql .= " ORDER BY featured DESC, created_at DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$properties = $stmt->fetchAll();

foreach ($properties as &$p) {
    $p['amenities'] = $p['amenities'] ? explode(',', $p['amenities']) : [];
}

echo json_encode(['success' => true, 'count' => count($properties), 'properties' => $properties]);