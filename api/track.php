<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../db.php';

$tracking = $_GET['tracking_code'] ?? null;
$order_id = $_GET['order_id'] ?? null;

if (!$tracking && !$order_id) {
    echo json_encode(['success'=>false,'message'=>'tracking_code atau order_id diperlukan']);
    exit;
}

if ($tracking) {
    $stmt = $pdo->prepare("SELECT id,customer_name,shipping,tracking_code,status,total,created_at,updated_at FROM orders WHERE tracking_code = :t LIMIT 1");
    $stmt->execute([':t'=>$tracking]);
} else {
    $stmt = $pdo->prepare("SELECT id,customer_name,shipping,tracking_code,status,total,created_at,updated_at FROM orders WHERE id = :id LIMIT 1");
    $stmt->execute([':id'=>$order_id]);
}

$order = $stmt->fetch();
if (!$order) {
    echo json_encode(['success'=>false,'message'=>'Order tidak ditemukan']);
    exit;
}
echo json_encode(['success'=>true,'order'=>$order]);
