<?php
require_once __DIR__ . '/../../app/guards/auth_guard.php';
require_once __DIR__ . '/../../app/config/database.php';

$pdo = Database::getInstance()->getConnection();
$id = (int) ($_GET['id'] ?? 0);

if ($id > 0) {
    $st = $pdo->prepare("DELETE FROM livros WHERE id = ?");
    $st->execute([$id]);
    header('Location: /index.php?ok=excluido');
    exit;
}
header('Location: /index.php?erro=id_invalido');
