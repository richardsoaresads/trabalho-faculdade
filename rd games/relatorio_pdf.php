<?php
session_start();
require 'conexao.php';

if (!isset($_SESSION['admin_id'])) {
    http_response_code(403);
    echo json_encode(['erro' => 'Não autorizado']);
    exit;
}

header('Content-Type: application/json; charset=utf-8');

// total de clientes
$sqlClientes = "SELECT COUNT(*) AS total_clientes FROM clientes";
$resClientes = $conn->query($sqlClientes);
$totalClientes = $resClientes->fetch_assoc()['total_clientes'] ?? 0;

// total de vendas e arrecadado
$sqlVendas = "
    SELECT 
        COUNT(*) AS total_vendas,
        COALESCE(SUM(valor), 0) AS total_arrecadado
    FROM vendas
";
$resVendas = $conn->query($sqlVendas);
$dadosVendas = $resVendas->fetch_assoc();
$totalVendas = $dadosVendas['total_vendas'] ?? 0;
$totalArrecadado = $dadosVendas['total_arrecadado'] ?? 0;

// lista de clientes + total gasto
$sqlLista = "
    SELECT 
        c.id,
        c.nome,
        c.email,
        COALESCE(SUM(v.valor), 0) AS total_gasto
    FROM clientes c
    LEFT JOIN vendas v ON v.id_cliente = c.id
    GROUP BY c.id, c.nome, c.email
    ORDER BY c.nome ASC
";
$resLista = $conn->query($sqlLista);

$clientes = [];
while ($row = $resLista->fetch_assoc()) {
    $row['total_gasto'] = (float)$row['total_gasto'];
    $row['total_gasto_formatado'] = number_format($row['total_gasto'], 2, ',', '.');
    $clientes[] = $row;
}

echo json_encode([
    'total_clientes' => (int)$totalClientes,
    'total_vendas' => (int)$totalVendas,
    'total_arrecadado' => (float)$totalArrecadado,
    'total_arrecadado_formatado' => number_format($totalArrecadado, 2, ',', '.'),
    'clientes' => $clientes
]);
