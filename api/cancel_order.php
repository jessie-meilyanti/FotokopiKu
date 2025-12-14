<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../db.php';

$input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
$order_id = $input['order_id'] ?? null;

if (!$order_id) {
    echo json_encode(['success'=>false,'message'=>'order_id diperlukan']);
    exit;
}

// cek status saat ini
$stmt = $pdo->prepare("SELECT status FROM orders WHERE id = :id LIMIT 1");
$stmt->execute([':id'=>$order_id]);
$row = $stmt->fetch();
if (!$row) {
    echo json_encode(['success'=>false,'message'=>'Order tidak ditemukan']);
    exit;
}
$cur = $row['status'];
if (in_array($cur, ['completed','cancelled'])) {
    echo json_encode(['success'=>false,'message'=>'Order tidak bisa dibatalkan (status: '.$cur.')']);
    exit;
}

$upd = $pdo->prepare("UPDATE orders SET status = 'cancelled', updated_at = NOW() WHERE id = :id");
$upd->execute([':id'=>$order_id]);

echo json_encode(['success'=>true,'message'=>'Order dibatalkan','order_id'=>$order_id]);
