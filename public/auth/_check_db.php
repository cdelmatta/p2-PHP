<?php
// Carrega a classe Database (Singleton) e as configs (DB_DSN/DB_PATH)
require_once __DIR__ . '/../../app/config/database.php';

// Pega a conexão PDO ativa
$pdo = Database::getInstance()->getConnection();

// Busca o primeiro usuário da tabela (id, nome, email, senha hash)
$row = $pdo->query("SELECT id, nome, email, senha FROM users ORDER BY id LIMIT 1")->fetch();

// Mostra informações úteis para diagnóstico
echo "<pre>";
echo "DB_PATH em uso: " . DB_PATH . PHP_EOL;   // caminho do arquivo SQLite que está sendo usado
echo "Primeiro usuário:" . PHP_EOL;
var_dump($row);                                // imprime o registro retornado (ou NULL)
echo "</pre>";

// Testa se a senha padrão 'admin123' confere com o hash do usuário retornado
echo "password_verify('admin123'): ";
var_dump(password_verify('admin123', $row['senha']));
