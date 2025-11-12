<?php
if (session_status() !== PHP_SESSION_ACTIVE) session_start();
require_once __DIR__ . '/../../app/config/database.php';

$pdo = Database::getInstance()->getConnection();

$msg  = '';
$erro = $_GET['erro'] ?? '';
$ok   = $_GET['ok']   ?? '';
$isPost = ($_SERVER['REQUEST_METHOD'] === 'POST');

// se já estiver logado, manda pra home
if (!empty($_SESSION['user_id'])) {
  header('Location: /index.php');
  exit;
}

if ($isPost) {
  $email = strtolower(trim($_POST['email'] ?? ''));
  $senha = $_POST['senha'] ?? '';

  $st = $pdo->prepare('SELECT id, nome, senha FROM users WHERE email = ? LIMIT 1');
  $st->execute([$email]);
  $u = $st->fetch(); // <<< usa $u (não $su)

  if ($u && password_verify($senha, $u['senha'])) {
    $_SESSION['user_id']   = $u['id'];
    $_SESSION['user_nome'] = $u['nome'];
    header('Location: /index.php');
    exit;
  } else {
    $msg = 'Credenciais inválidas';
  }
}
?>
<!doctype html>
<html lang="pt-br">
<head>
  <meta charset="utf-8">
  <title>Login</title>
  <link rel="stylesheet" href="/assets/css/styles.css">
</head>
<body>
<div class="auth-card">
  <h1>Login</h1>

  <?php if ($erro === 'auth'): ?>
    <div class="message error">Faça login para continuar.</div>
  <?php endif; ?>

  <?php if ($ok === 'cadastrado'): ?>
    <div class="message success">Cadastro realizado! Entre com suas credenciais.</div>
  <?php endif; ?>

  <?php if ($isPost && $msg): ?>
    <div class="message error"><?= htmlspecialchars($msg) ?></div>
  <?php endif; ?>

  <form method="POST">
    <label>Email
      <input type="email" name="email" required>
    </label>
    <label>Senha
      <input type="password" name="senha" required>
    </label>
    <button class="btn" type="submit">Entrar</button>
  </form>
  <p class="muted">Não tem conta? <a href="/auth/register.php">Cadastre-se</a></p>
</div>
</body>
</html>
