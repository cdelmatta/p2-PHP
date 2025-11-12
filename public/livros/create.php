<?php require_once __DIR__ . '/../../app/guards/auth_guard.php'; ?>

<!doctype html>
<html lang="pt-br">

<head>
    <meta charset="utf-8">
    <title>Novo Livro</title>
    <link rel="stylesheet" href="/assets/css/styles.css">
</head>

<body>
    <div class="page-card">
        <h1>Novo Livro</h1>
        <form method="POST" action="/livros/store.php">
            <label>Título <input type="text" name="titulo" required></label>
            <label>Autor <input type="text" name="autor" required></label>
            <label>Ano <input type="number" name="ano" required></label>
            <label>ISBN <input type="text" name="isbn"></label>
            <button class="btn" type="submit">Salvar</button>
            <a class="btn secondary" href="/index.php">Voltar</a>
        </form>
    </div>
</body>

</html>