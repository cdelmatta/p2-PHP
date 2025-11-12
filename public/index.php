<?php
// Impede acesso de usuários não autenticados (redireciona para login se necessário)
require_once __DIR__ . '/../app/guards/auth_guard.php';

// Abre a conexão com o banco via Singleton (retorna um PDO ativo)
require_once __DIR__ . '/../app/config/database.php';
$pdo = Database::getInstance()->getConnection();

// Carrega todos os livros do banco, ordenando do mais recente para o mais antigo
$livros = $pdo->query("SELECT * FROM livros ORDER BY criado_em DESC")->fetchAll();

// Flags de feedback passadas pela query string (ex.: ?ok=criado, ?erro=validacao)
$ok   = $_GET['ok']   ?? '';
$erro = $_GET['erro'] ?? '';
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <!-- Ajusta layout para telas pequenas -->
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Livros</title>
  <!-- CSS global do projeto -->
  <link rel="stylesheet" href="/assets/css/styles.css">
</head>
<body>
  <div class="container">
    <!-- Barra superior com saudação e botão de sair -->
    <div class="topbar">
      <div>olá, <?= htmlspecialchars($_SESSION['user_nome']) ?></div>
      <div><a class="btn" href="/auth/logout.php">Sair</a></div>
    </div>

    <!-- Título da página -->
    <h1>Livros</h1>

    <!-- Caixa de mensagem de sucesso, quando houver -->
    <?php if ($ok): ?>
      <div class="message success"><?= htmlspecialchars($ok) ?></div>
    <?php endif; ?>

    <!-- Caixa de mensagem de erro, quando houver -->
    <?php if ($erro): ?>
      <div class="message error"><?= htmlspecialchars($erro) ?></div>
    <?php endif; ?>

    <!-- Ação para criar um novo livro -->
    <p><a class="btn" href="/livros/create.php">Novo Livro</a></p>

    <!-- Tabela de listagem dos livros -->
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
            <!-- htmlspecialchars evita XSS ao imprimir conteúdo vindo do banco -->
            <td><?= htmlspecialchars($l['titulo']) ?></td>
            <td><?= htmlspecialchars($l['autor']) ?></td>
            <td><?= (int) $l['ano'] ?></td>
            <!-- Usa valor vazio quando não houver ISBN -->
            <td><?= htmlspecialchars($l['isbn'] ?? '') ?></td>
            <td>
              <!-- Link para editar o registro -->
              <a class="link" href="/livros/edit.php?id=<?= $l['id'] ?>">Editar</a> |
              <!-- Link para excluir (com confirmação no cliente) -->
              <a class="link danger"
                 href="/livros/delete.php?id=<?= $l['id'] ?>"
                 onclick="return confirm('você deseja Excluir?')">Excluir</a>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</body>
</html>
