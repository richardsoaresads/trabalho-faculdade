<?php
session_start();
require 'conexao.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

$id = (int)($_GET['id'] ?? 0);
if ($id > 0) {
    // busca para apagar imagem também, se quiser
    $stmt = $conn->prepare("SELECT imagem FROM jogos WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $res = $stmt->get_result();
    $jogo = $res->fetch_assoc();

    // apaga do banco
    $stmt2 = $conn->prepare("DELETE FROM jogos WHERE id = ?");
    $stmt2->bind_param("i", $id);
    $stmt2->execute();

    // apaga imagem física
    if ($jogo && !empty($jogo['imagem'])) {
        $uploadDir = __DIR__ . '/assets/uploads';
        @unlink($uploadDir . '/' . $jogo['imagem']);
    }
}

header("Location: admin_dashboard.php");
exit;
