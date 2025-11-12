<?php
// Restringe o acesso: só usuários logados podem atualizar registros
require_once __DIR__ . '/../../app/guards/auth_guard.php';

// Abre a conexão PDO via Singleton Database
require_once __DIR__ . '/../../app/config/database.php';
$pdo = Database::getInstance()->getConnection();

// Só processa se o formulário foi enviado por POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Lê e higieniza os campos recebidos
    $id     = (int) ($_POST['id']     ?? 0);   // id do livro (oculto no form)
    $titulo = trim($_POST['titulo']   ?? '');  // título obrigatório
    $autor  = trim($_POST['autor']    ?? '');  // autor obrigatório
    $ano    = (int) ($_POST['ano']    ?? 0);   // ano obrigatório (>0)
    $isbn   = trim($_POST['isbn']     ?? '');  // ISBN opcional

    // Validação mínima: precisa de id válido e campos obrigatórios preenchidos
    if ($id && $titulo && $autor && $ano > 0) {
        // Atualiza o registro com prepared statement (evita SQL injection)
        $st = $pdo->prepare("UPDATE livros SET titulo=?, autor=?, ano=?, isbn=? WHERE id=?");
        $st->execute([$titulo, $autor, $ano, $isbn ?: null, $id]);

        // Redireciona para a listagem com flag de sucesso
        header('Location: /index.php?ok=atualizado');
        exit;
    } else {
        // Se falhou a validação, volta para o form de edição do mesmo id com erro
        header('Location: /livros/edit.php?id=' . $id . '&erro=validacao');
        exit;
    }
}

// Se não for POST, rejeita a requisição e volta para a listagem com erro
header('Location: /index.php?erro=request');
