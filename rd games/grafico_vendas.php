<?php
session_start();
require 'conexao.php';

if (!isset($_SESSION['admin_id'])) {
    http_response_code(403);
    echo json_encode(['erro' => 'Não autorizado']);
    exit;
}

header('Content-Type: application/json; charset=utf-8');

$sql = "
    SELECT 
        DATE(data_venda) AS dia,
        SUM(valor) AS total
    FROM vendas
    GROUP BY DATE(data_venda)
    ORDER BY dia ASC
";

$res = $conn->query($sql);

$labels = [];
$valores = [];

while ($row = $res->fetch_assoc()) {
    $labels[] = $row['dia'];
    $valores[] = (float)$row['total'];
}

echo json_encode([
    'labels' => $labels,
    'valores' => $valores
]);
