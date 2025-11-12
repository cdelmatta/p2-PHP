<?php
// Exige que o usuário esteja logado antes de permitir a exclusão
require_once __DIR__ . '/../../app/guards/auth_guard.php';

// Abre a conexão com o banco (PDO via Singleton)
require_once __DIR__ . '/../../app/config/database.php';
$pdo = Database::getInstance()->getConnection();

// Lê o ID vindo pela URL (?id=123) e força para inteiro
$id = (int) ($_GET['id'] ?? 0);

// Se o ID for válido (> 0), tenta excluir
if ($id > 0) {
    // Prepara e executa a exclusão segura (evita SQL injection)
    $st = $pdo->prepare("DELETE FROM livros WHERE id = ?");
    $st->execute([$id]);

    // Redireciona de volta para a listagem com mensagem de sucesso
    header('Location: /index.php?ok=excluido');
    exit;
}

// Se o ID não for válido, volta com erro
header('Location: /index.php?erro=id_invalido');
