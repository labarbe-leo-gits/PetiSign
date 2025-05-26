<?php
include_once 'database/database.php';

header('Cache-Control: no-cache, must-revalidate');
header('Content-Type: application/json');

$query = isset($_GET['query']) ? trim($_GET['query']) : '';
$category = isset($_GET['category']) ? $_GET['category'] : '';

$sql = "SELECT * FROM PETITION WHERE 1=1";

if (!empty($query)) {
    $sql .= " AND (title LIKE :query OR description LIKE :query)";
}

if (!empty($category) && $category != 'all') {
    $sql .= " AND category = :category";
}

$stmt = $pdo->prepare($sql);

if (!empty($query)) {
    $searchParam = "%" . $query . "%";
    $stmt->bindParam(':query', $searchParam);
}

if (!empty($category) && $category != 'all') {
    $stmt->bindParam(':category', $category);
}

$stmt->execute();
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($results);

?>