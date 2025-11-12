<?php
// Define o caminho da pasta RAIZ do projeto (sobe duas pastas a partir de /app/config)
define('BASE_DIR', dirname(__DIR__, 2));

// Define atalhos de caminho para as pastas principais
define('APP_DIR',     BASE_DIR . '/app');     // pasta com configs, guards, libs
define('PUBLIC_DIR',  BASE_DIR . '/public');  // pasta pública (arquivos acessados pelo navegador)
define('STORAGE_DIR', BASE_DIR . '/storage'); // onde ficam arquivos não públicos (ex.: banco SQLite)

// Garante que a pasta /storage exista (e tenta criá-la com permissão 0777, se não existir)
// O @ suprime warnings caso a pasta já exista ou não possa ser criada
if (!is_dir(STORAGE_DIR)) { @mkdir(STORAGE_DIR, 0777, true); }

// Monta o caminho completo do arquivo do banco SQLite
define('DB_PATH', STORAGE_DIR . '/database.sqlite');

// DSN (Data Source Name) usado pelo PDO para conectar no SQLite apontando para DB_PATH
define('DB_DSN', 'sqlite:' . DB_PATH);
