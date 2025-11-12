<?php
if (session_status() !== PHP_SESSION_ACTIVE)session_start();
require_once __DIR__ . '/../../app/config/database.php';

$pdo = Database::getInstance()->getConnection();
$msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';

    if ($nome && filter_var($email, FILTER_VALIDATE_EMAIL) && strlen($senha) >= 6) {
        try {
            $hash = password_hash($senha, PASSWORD_DEFAULT);
            $st = $pdo->prepare('INSERT INTO users (nome,email,senha) VALUES (?,?,?)');
            $st->execute([$nome, $email, $hash]);
            header('Location: /auth/login.php?ok=cadastrado');
            exit;
        } catch (Throwable $e) {
            $msg = 'Erro: ' . $e->getMessage();
        }
    } else {
        $msg = 'Dados inválidos.';
    }
}

?>
<!doctype html>
<html lang="pt-br">

<head>
    <meta charset="utf-8">
    <title>Cadastro</title>
    <link rel="stylesheet" href="/assets/css/styles.css">
</head>

<body>
    <div class="auth-card">
        <h1>Criar conta</h1>
        <?php if ($msg): ?>
            <div class="message error"><?= htmlspecialchars($msg) ?></div><?php endif; ?>
        <form method="POST">
            <label>Nome
                <input type="text" name="nome" required>
            </label>
            <label>Email
                <input type="email" name="email" required>
            </label>
            <label>Senha (min 6)
                <input type="password" name="senha" required>
            </label>
            <button class="btn" type="submit">Cadastrar</button>
        </form>
        <p class="muted">Já tem conta? <a href="/public/auth/login.php">Entrar</a></p>
    </div>
</body>

</html>