<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../db.php';

$data = $_POST;
$name = trim($data['name'] ?? '');
$address = trim($data['address'] ?? '');
$shipping = isset($data['shipping']) && ($data['shipping'] === '1' || $data['shipping'] === 'on') ? 1 : 0;
$total = floatval($data['total'] ?? 0);

if ($name === '') {
    echo json_encode(['success'=>false,'message'=>'Nama diperlukan']);
    exit;
}

$tracking = $shipping ? 'TRK'.strtoupper(substr(uniqid(),0,10)) : null;
$status = 'processing';

$stmt = $pdo->prepare("INSERT INTO orders (customer_name,address,shipping,tracking_code,status,total) VALUES (:name,:address,:shipping,:tracking,:status,:total)");
$stmt->execute([
    ':name'=>$name,
    ':address'=>$address,
    ':shipping'=>$shipping,
    ':tracking'=>$tracking,
    ':status'=>$status,
    ':total'=>$total
]);

$id = $pdo->lastInsertId();
// ambil kembali order lengkap
$stmt2 = $pdo->prepare("SELECT id,customer_name,address,shipping,tracking_code,status,total,created_at,updated_at FROM orders WHERE id = :id LIMIT 1");
$stmt2->execute([':id'=>$id]);
$order = $stmt2->fetch();

echo json_encode(['success'=>true,'order'=>$order]);
