<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../db.php';

$months = [];
$labels = [];
$totals = [];

$now = new DateTimeImmutable('first day of this month');
for ($i = 11; $i >= 0; $i--) {
    $m = $now->modify("-$i months");
    $labels[] = $m->format('Y-m');
    $months[] = $m;
}

// ambil data aggregated
$start = $months[0]->format('Y-m-01 00:00:00');
$stmt = $pdo->prepare("SELECT DATE_FORMAT(created_at,'%Y-%m') as ym, COALESCE(SUM(total),0) as total FROM orders WHERE created_at >= :start GROUP BY ym");
$stmt->execute([':start'=>$start]);
$rows = $stmt->fetchAll();

$map = [];
foreach ($rows as $r) $map[$r['ym']] = (float)$r['total'];

foreach ($labels as $lab) {
    $totals[] = isset($map[$lab]) ? $map[$lab] : 0.0;
}

echo json_encode(['labels'=>$labels,'totals'=>$totals]);
