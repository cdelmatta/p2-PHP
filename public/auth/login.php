<?php
// Inicia a sessão se ainda não estiver ativa (necessário para usar $_SESSION)
if (session_status() !== PHP_SESSION_ACTIVE) session_start();

// Carrega a conexão (Database Singleton → PDO) e constantes
require_once __DIR__ . '/../../app/config/database.php';

// Obtém a conexão PDO para consultar o banco
$pdo = Database::getInstance()->getConnection();

// Variáveis de apoio para mensagens de erro/ok e flag de POST
$msg  = '';
$erro = $_GET['erro'] ?? '';   // ex.: ?erro=auth quando o guard bloqueia o acesso
$ok   = $_GET['ok']   ?? '';   // ex.: ?ok=cadastrado após registro
$isPost = ($_SERVER['REQUEST_METHOD'] === 'POST'); // checa se o formulário foi enviado via POST

// Se já existe usuário logado na sessão, redireciona direto para a home protegida
if (!empty($_SESSION['user_id'])) {
  header('Location: /index.php');
  exit;
}

// Se o formulário foi enviado (POST), tenta autenticar
if ($isPost) {
  // Normaliza o email (trim para remover espaços e lowercase)
  $email = strtolower(trim($_POST['email'] ?? ''));
  $senha = $_POST['senha'] ?? '';

  // Busca o usuário pelo email (retorna id, nome e hash da senha)
  $st = $pdo->prepare('SELECT id, nome, senha FROM users WHERE email = ? LIMIT 1');
  $st->execute([$email]);
  $u = $st->fetch();

  // Se encontrou o usuário e o hash confere, cria a sessão e manda para a home
  if ($u && password_verify($senha, $u['senha'])) {
    $_SESSION['user_id']   = $u['id'];   // identifica o usuário logado
    $_SESSION['user_nome'] = $u['nome']; // opcional: nome para exibir no topo
    header('Location: /index.php');
    exit;
  } else {
    // Caso email/senha não confiram, prepara mensagem de erro para exibir no HTML
    $msg = 'Credenciais inválidas';
  }
}
?>
<!doctype html>
<html lang="pt-br">
<head>
  <meta charset="utf-8">
  <title>Login</title>
  <!-- CSS global do app -->
  <link rel="stylesheet" href="/assets/css/styles.css">
</head>
<body>
<div class="auth-card">
  <h1>Login</h1>

  <!-- Mensagem quando o guard bloqueia acesso a uma página protegida -->
  <?php if ($erro === 'auth'): ?>
    <div class="message error">Faça login para continuar.</div>
  <?php endif; ?>

  <!-- Mensagem de sucesso após cadastro -->
  <?php if ($ok === 'cadastrado'): ?>
    <div class="message success">Cadastro realizado! Entre com suas credenciais.</div>
  <?php endif; ?>

  <!-- Exibe erro de login somente após tentativa (POST) com falha -->
  <?php if ($isPost && $msg): ?>
    <div class="message error"><?= htmlspecialchars($msg) ?></div>
  <?php endif; ?>

  <!-- Formulário de login (envia via POST para esta mesma página) -->
  <form method="POST">
    <label>Email
      <input type="email" name="email" required>
    </label>
    <label>Senha
      <input type="password" name="senha" required>
    </label>
    <button class="btn" type="submit">Entrar</button>
  </form>

  <!-- Link para a página de cadastro -->
  <p class="muted">Não tem conta? <a href="/auth/register.php">Cadastre-se</a></p>
</div>
</body>
</html>
