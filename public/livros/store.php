<?php
// Garante que só usuários logados possam criar livros
require_once __DIR__ . '/../../app/guards/auth_guard.php';

// Abre a conexão PDO (via Singleton Database)
require_once __DIR__ . '/../../app/config/database.php';
$pdo = Database::getInstance()->getConnection();

// Só processa se o formulário veio por POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Lê e higieniza os campos do formulário
    $titulo = trim($_POST['titulo'] ?? '');
    $autor  = trim($_POST['autor']  ?? '');
    $ano    = (int) ($_POST['ano']  ?? 0);
    $isbn   = trim($_POST['isbn']   ?? '');

    // Validação simples: exige título, autor e ano válido (>0)
    if ($titulo && $autor && $ano > 0) {
        // Prepara INSERT com placeholders (evita SQL injection)
        $st = $pdo->prepare("INSERT INTO livros (titulo,autor,ano,isbn) VALUES (?,?,?,?)");

        // Executa o INSERT; se ISBN estiver vazio, envia NULL
        $st->execute([$titulo, $autor, $ano, $isbn ?: null]);

        // Volta para a listagem com flag de sucesso
        header('Location: /index.php?ok=criado');
        exit;
    }
}

// Se não for POST ou se falhar validação, retorna para o form com erro
header('Location: /livros/create.php?erro=validacao');
