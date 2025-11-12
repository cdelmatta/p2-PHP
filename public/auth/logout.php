<?php
// Inicia a sessão se ainda não estiver ativa (para poder limpá-la)
if (session_status() !== PHP_SESSION_ACTIVE) session_start();

// Remove todas as variáveis da sessão
session_unset();

// Destroi a sessão atual (invalidando o ID e os dados no servidor)
session_destroy();

// Redireciona de volta para a tela de login com um flag (?saiu=1)
header('Location: /auth/login.php?saiu=1');
exit; // Garante que nada mais rode após o redirect
