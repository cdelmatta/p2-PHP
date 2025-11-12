<?php
// Se a sessão ainda não foi iniciada, inicia agora.
// (Necessário para ler/escrever em $_SESSION)
if (session_status() != PHP_SESSION_ACTIVE) session_start();

// Verifica se NÃO existe usuário logado (sem user_id na sessão)
if (empty($_SESSION['user_id'])) {
    // Redireciona para a página de login informando o motivo (?erro=auth)
    // OBS: como seu docroot é /public, a URL começa em /auth/...
    header('Location: /auth/login.php?erro=auth');
    exit; // garante que nada mais do arquivo rode após o redirecionamento
}
