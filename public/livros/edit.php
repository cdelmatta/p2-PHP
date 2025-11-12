<?php
// Garante que só usuários logados acessem esta página
require_once __DIR__ . '/../../app/guards/auth_guard.php';

// Abre a conexão PDO (Database Singleton)
require_once __DIR__ . '/../../app/config/database.php';
$pdo = Database::getInstance()->getConnection();

// Pega o ID pela query string (?id=123) e força para inteiro
$id = (int) ($_GET['id'] ?? 0);

// Busca o livro pelo ID (usa prepared statement para segurança)
$st = $pdo->prepare("SELECT * FROM livros WHERE id = ?");
$st->execute([$id]);
$livro = $st->fetch();

// Se não encontrou, volta para a listagem com erro
if (!$livro) {
    // CORRIGIDO: sem /public no caminho quando docroot é /public
    header('Location: /index.php?erro=nao_encontrado');
    exit;
}
?>
<!doctype html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <title>Editar Livro</title>
    <!-- CSS global -->
    <link rel="stylesheet" href="/assets/css/styles.css">
</head>
<body>
    <div class="page-card">
        <h1>Editar Livro</h1>

        <!-- Formulário de edição: envia por POST para update.php -->
        <form method="POST" action="/livros/update.php">
            <!-- Campo oculto com o ID do registro -->
            <input type="hidden" name="id" value="<?= $livro['id'] ?>">

            <!-- Campos com valores atuais do livro; htmlspecialchars evita XSS ao re-renderizar -->
            <label>Título
                <input type="text" name="titulo" value="<?= htmlspecialchars($livro['titulo']) ?>" required>
            </label>

            <label>Autor
                <input type="text" name="autor" value="<?= htmlspecialchars($livro['autor']) ?>" required>
            </label>

            <label>Ano
                <input type="number" name="ano" value="<?= (int) $livro['ano'] ?>" required>
            </label>

            <label>ISBN
                <input type="text" name="isbn" value="<?= htmlspecialchars($livro['isbn'] ?? '') ?>">
            </label>

            <button class="btn" type="submit">Atualizar</button>
            <a class="btn secondary" href="/index.php">Voltar</a>
        </form>
    </div>
</body>
</html>
