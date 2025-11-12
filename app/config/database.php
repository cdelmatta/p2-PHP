<?php
require_once __DIR__ . '/config.php';

class Database
{
    private static $instance = null;
    private $pdo;

    private function __construct()
    {
        try {
            $this->pdo = new PDO(DB_DSN);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            $this->createTables();
        } catch (PDOException $e) {
            die("Erro na conexão: " . $e->getMessage());
        }
    }

    public function getConnection() { return $this->pdo; }

    public static function getInstance()
    {
        if (self::$instance === null) self::$instance = new self();
        return self::$instance;
    }

    private function createTables()
    {
        // Tabela de usuários
        $this->pdo->exec("
            CREATE TABLE IF NOT EXISTS users (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                nome  TEXT NOT NULL,
                email TEXT NOT NULL UNIQUE,
                senha TEXT NOT NULL
            );
        ");

        // Tabela de livros (CRUD principal)
        $this->pdo->exec("
            CREATE TABLE IF NOT EXISTS livros (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                titulo TEXT NOT NULL,
                autor  TEXT NOT NULL,
                ano    INTEGER NOT NULL,
                isbn   TEXT UNIQUE,
                criado_em TEXT DEFAULT (datetime('now'))
            );
        ");

        // Usuário admin padrão (criado uma única vez)
        $row = $this->pdo->query("SELECT COUNT(*) AS c FROM users")->fetch();
        if ((int)$row['c'] === 0) {
            $hash = password_hash('admin123', PASSWORD_DEFAULT);
            $st = $this->pdo->prepare("INSERT INTO users (nome,email,senha) VALUES (?,?,?)");
            $st->execute(['Administrador', 'admin@local', $hash]);
        }
    }
}

$db = Database::getInstance();
