<?php
// Caminhos base
define('BASE_DIR', dirname(__DIR__, 2));
define('APP_DIR',  BASE_DIR . '/app');
define('PUBLIC_DIR', BASE_DIR . '/public');
define('STORAGE_DIR', BASE_DIR . '/storage');

// Banco (SQLite) salvo em /storage
if (!is_dir(STORAGE_DIR)) { @mkdir(STORAGE_DIR, 0777, true); }

define('DB_PATH', STORAGE_DIR . '/database.sqlite');
define('DB_DSN', 'sqlite:' . DB_PATH);
