<?php
// Inicia a sessão (necessário se quiser, por ex., logar após cadastro ou exibir msgs por sessão)
if (session_status() !== PHP_SESSION_ACTIVE) session_start();

// Carrega o Database (Singleton) e abre a conexão PDO
require_once __DIR__ . '/../../app/config/database.php';
$pdo = Database::getInstance()->getConnection();

// Mensagem de erro para o formulário
$msg = '';

// Se o formulário foi enviado via POST, processa os dados
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Lê e trata os campos (trim para remover espaços; validação depois)
    $nome  = trim($_POST['nome']  ?? '');
    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['senha']      ?? '';

    // Validações básicas: nome não vazio, email válido, senha min 6
    if ($nome && filter_var($email, FILTER_VALIDATE_EMAIL) && strlen($senha) >= 6) {
        try {
            // Gera hash seguro da senha
            $hash = password_hash($senha, PASSWORD_DEFAULT);

            // Insere o novo usuário
            $st = $pdo->prepare('INSERT INTO users (nome,email,senha) VALUES (?,?,?)');
            $st->execute([$nome, $email, $hash]);

            // Redireciona para o login com flag de sucesso (?ok=cadastrado)
            header('Location: /auth/login.php?ok=cadastrado');
            exit;

        } catch (Throwable $e) {
            // Captura erros (ex.: email duplicado por UNIQUE) e exibe mensagem
            $msg = 'Erro: ' . $e->getMessage();
        }
    } else {
        // Se falhar na validação, mostra erro genérico
        $msg = 'Dados inválidos.';
    }
}
?>
<!doctype html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <title>Cadastro</title>
    <!-- CSS global -->
    <link rel="stylesheet" href="/assets/css/styles.css">
</head>
<body>
<div class="auth-card">
    <h1>Criar conta</h1>

    <!-- Exibe mensagem de erro (se houver) -->
    <?php if ($msg): ?>
      <div class="message error"><?= htmlspecialchars($msg) ?></div>
    <?php endif; ?>

    <!-- Formulário de cadastro (POST para esta própria página) -->
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

    <!-- Ajuste do link: sem /public no caminho quando docroot = /public -->
    <p class="muted">Já tem conta? <a href="/auth/login.php">Entrar</a></p>
</div>
</body>
</html>
