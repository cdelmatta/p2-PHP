<?php
require_once __DIR__ . '/../app/guards/auth_guard.php';
require_once __DIR__ . '/../app/config/database.php';

$pdo = Database::getInstance()->getConnection();

// corrigido: datetime(...) (ou só ORDER BY criado_em DESC)
$livros = $pdo->query("SELECT * FROM livros ORDER BY criado_em DESC")->fetchAll();

$ok   = $_GET['ok'] ?? '';
$erro = $_GET['erro'] ?? '';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Livros</title>
    <link rel="stylesheet" href="/assets/css/styles.css">
</head>

<body>
    <div class="container">
        <div class="topbar">
            <div>olá, <?= htmlspecialchars($_SESSION['user_nome']) ?></div>
            <div><a class="btn" href="/auth/logout.php">Sair</a></div>
        </div>

        <h1>Livros</h1>

        <?php if ($ok): ?>
            <div class="message success"><?= htmlspecialchars($ok) ?></div><?php endif; ?>
        <?php if ($erro): ?>
            <div class="message error"><?= htmlspecialchars($erro) ?></div><?php endif; ?>

        <p><a class="btn" href="/livros/create.php">Novo Livro</a></p>

        <table class="table">
            <thead>
                <tr>
                    <th>Título</th>
                    <th>Autor</th>
                    <th>Ano</th>
                    <th>ISBN</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($livros as $l): ?>
                    <tr>
                        <td><?= htmlspecialchars($l['titulo']) ?></td>
                        <td><?= htmlspecialchars($l['autor']) ?></td>
                        <td><?= (int) $l['ano'] ?></td>
                        <td><?= htmlspecialchars($l['isbn']) ?></td>
                        <td>
                            <<a class="link" href="/livros/edit.php?id=<?= $l['id'] ?>">Editar</a> |
                            <a class="link danger" href="/livros/delete.php?id=<?= $l['id'] ?>"
                                onclick="return confirm('você deseja Excluir?')">Excluir</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>

</html>