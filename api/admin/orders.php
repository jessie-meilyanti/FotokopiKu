<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../db.php';

// optional ?limit=
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 100;
$limit = max(10, min(500, $limit));

$stmt = $pdo->prepare("SELECT id,customer_name,shipping,tracking_code,status,total,created_at FROM orders ORDER BY created_at DESC LIMIT :lim");
$stmt->bindValue(':lim', $limit, PDO::PARAM_INT);
$stmt->execute();
$rows = $stmt->fetchAll();

echo json_encode(['success'=>true,'orders'=>$rows]);
