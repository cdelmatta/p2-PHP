<?php
require_once __DIR__ . '/../../app/guards/auth_guard.php';
require_once __DIR__ . '/../../app/config/database.php';

$pdo = Database::getInstance()->getConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = trim($_POST['titulo'] ?? '');
    $autor = trim($_POST['autor'] ?? '');
    $ano = (int) ($_POST['ano'] ?? 0);
    $isbn = trim($_POST['isbn'] ?? '');

    if ($titulo && $autor && $ano > 0) {
        $st = $pdo->prepare("INSERT INTO livros (titulo,autor,ano,isbn) VALUES (?,?,?,?)");
        $st->execute([$titulo, $autor, $ano, $isbn ?: null]);
        header('Location: /index.php?ok=criado');
        exit;
    }
}

header('Location: /livros/create.php?erro=validacao');