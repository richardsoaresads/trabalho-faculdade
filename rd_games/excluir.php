<?php
include 'conexao.php';

$id = intval($_GET['id'] ?? 0);
if ($id <= 0) {
  header('Location: index.php');
  exit;
}

$sql = "DELETE FROM produtos WHERE id=$id";

if ($conn->query($sql) === TRUE) {
  header("Location: index.php?msg=delete_success");
  exit;
} else {
  echo "Erro: " . $conn->error;
}
?>