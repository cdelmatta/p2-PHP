<?php
require_once __DIR__ . '/../../app/config/database.php';
$pdo = Database::getInstance()->getConnection();

$row = $pdo->query("SELECT id, nome, email, senha FROM users ORDER BY id LIMIT 1")->fetch();
echo "<pre>";
echo "DB_PATH em uso: " . DB_PATH . PHP_EOL;
echo "Primeiro usuário:" . PHP_EOL;
var_dump($row);
echo "</pre>";
echo "password_verify('admin123'): ";
var_dump(password_verify('admin123', $row['senha']));
