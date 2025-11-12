<?php
require_once __DIR__ . '/../../app/guards/auth_guard.php';
require_once __DIR__ . '/../../app/config/database.php';

$pdo = Database::getInstance()->getConnection();
$id = (int) ($_GET['id'] ?? 0);

$st = $pdo->prepare("SELECT * FROM livros WHERE id = ?");
$st->execute([$id]);
$livro = $st->fetch();
if (!$livro) {
    header('Location: /public/index.php?erro=nao_encontrado');
    exit;
}
?>
<!doctype html>
<html lang="pt-br">

<head>
    <meta charset="utf-8">
    <title>Editar Livro</title>
    <link rel="stylesheet" href="/assets/css/styles.css">
</head>

<body>
    <div class="page-card">
        <h1>Editar Livro</h1>
        <form method="POST" action="/livros/update.php">
            <input type="hidden" name="id" value="<?= $livro['id'] ?>">
            <label>Título <input type="text" name="titulo" value="<?= htmlspecialchars($livro['titulo']) ?>"
                    required></label>
            <label>Autor <input type="text" name="autor" value="<?= htmlspecialchars($livro['autor']) ?>"
                    required></label>
            <label>Ano <input type="number" name="ano" value="<?= (int) $livro['ano'] ?>" required></label>
            <label>ISBN <input type="text" name="isbn" value="<?= htmlspecialchars($livro['isbn'] ?? '') ?>"></label>
            <button class="btn" type="submit">Atualizar</button>
            <a class="btn secondary" href="/index.php">Voltar</a>
        </form>
    </div>
</body>

</html>