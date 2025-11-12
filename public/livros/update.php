<?php
require_once __DIR__ . '/../../app/guards/auth_guard.php';
require_once __DIR__ . '/../../app/config/database.php';

$pdo = Database::getInstance()->getConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int) ($_POST['id'] ?? 0);
    $titulo = trim($_POST['titulo'] ?? '');
    $autor = trim($_POST['autor'] ?? '');
    $ano = (int) ($_POST['ano'] ?? 0);
    $isbn = trim($_POST['isbn'] ?? '');

    if ($id && $titulo && $autor && $ano > 0) {
        $st = $pdo->prepare("UPDATE livros SET titulo=?, autor=?, ano=?, isbn=? WHERE id=?");
        $st->execute([$titulo, $autor, $ano, $isbn ?: null, $id]);
        header('Location: /index.php?ok=atualizado');
        exit;
    } else {
        header('Location: /livros/edit.php?id='.$id.'&erro=validacao');
        exit;
    }
}
header('Location: /index.php?erro=request');