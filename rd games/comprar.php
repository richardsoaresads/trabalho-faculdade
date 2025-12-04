<?php
session_start();
require 'conexao.php';

if (!isset($_SESSION['cliente_id'])) {
    $_SESSION['last_msg'] = [
        'tipo'  => 'erro',
        'texto' => 'Você precisa estar logado para comprar.'
    ];
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['id_jogo'])) {
    $_SESSION['last_msg'] = [
        'tipo'  => 'erro',
        'texto' => 'Requisição inválida.'
    ];
    header("Location: loja.php");
    exit;
}

$id_jogo     = (int) $_POST['id_jogo'];
$id_cliente  = (int) $_SESSION['cliente_id'];

// Busca jogo para pegar preço e estoque
$sql = "SELECT id, preco, estoque FROM jogos WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_jogo);
$stmt->execute();
$result = $stmt->get_result();
$jogo = $result->fetch_assoc();

if (!$jogo) {
    $_SESSION['last_msg'] = [
        'tipo'  => 'erro',
        'texto' => 'Jogo não encontrado.'
    ];
    header("Location: loja.php");
    exit;
}

$estoque_atual = (int)$jogo['estoque'];

if ($estoque_atual <= 0) {
    $_SESSION['last_msg'] = [
        'tipo'  => 'erro',
        'texto' => 'Estoque esgotado para este jogo.'
    ];
    header("Location: loja.php");
    exit;
}

// valor da venda = preço do jogo (1 unidade)
$valor_venda = (float)$jogo['preco'];

// Transação simples: grava venda e baixa estoque
$conn->begin_transaction();

try {
    // Insere na tabela vendas
    $sqlVenda = "INSERT INTO vendas (id_cliente, id_jogo, valor, data_venda)
                 VALUES (?, ?, ?, NOW())";
    $stmtVenda = $conn->prepare($sqlVenda);
    $stmtVenda->bind_param("iid", $id_cliente, $id_jogo, $valor_venda);
    $stmtVenda->execute();

    // Atualiza estoque (-1)
    $sqlEstoque = "UPDATE jogos SET estoque = estoque - 1 WHERE id = ? AND estoque > 0";
    $stmtEstoque = $conn->prepare($sqlEstoque);
    $stmtEstoque->bind_param("i", $id_jogo);
    $stmtEstoque->execute();

    if ($stmtEstoque->affected_rows === 0) {
        // não conseguiu dar baixa no estoque
        $conn->rollback();
        $_SESSION['last_msg'] = [
            'tipo'  => 'erro',
            'texto' => 'Não foi possível atualizar o estoque.'
        ];
        header("Location: loja.php");
        exit;
    }

    $conn->commit();

    $_SESSION['last_msg'] = [
        'tipo'  => 'ok',
        'texto' => 'Compra realizada com sucesso!'
    ];
} catch (Exception $e) {
    $conn->rollback();
    $_SESSION['last_msg'] = [
        'tipo'  => 'erro',
        'texto' => 'Erro ao realizar a compra.'
    ];
}

header("Location: loja.php");
exit;
