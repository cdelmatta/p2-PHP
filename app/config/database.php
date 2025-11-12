<?php
// Carrega as constantes de caminho/DSN (DB_DSN, DB_PATH, etc.)
require_once __DIR__ . '/config.php';

class Database
{
    // Instância única (Singleton) da classe Database
    private static $instance = null;

    // Handler do PDO que será reutilizado
    private $pdo;

    // Construtor privado: impede new Database() fora da classe (padrão Singleton)
    private function __construct()
    {
        try {
            // Abre conexão PDO usando o DSN do SQLite (ex.: sqlite:/.../storage/database.sqlite)
            $this->pdo = new PDO(DB_DSN);

            // Lança exceções em erros de SQL (facilita debug)
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Ao buscar registros, retorna arrays associativos por padrão
            $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

            // Cria as tabelas necessárias (users, livros) e o admin padrão, se não existirem
            $this->createTables();

        } catch (PDOException $e) {
            // Encerra a execução com a mensagem de erro de conexão
            die("Erro na conexão: " . $e->getMessage());
        }
    }

    // Retorna o objeto PDO para ser usado nas queries
    public function getConnection() { return $this->pdo; }

    // Ponto de acesso ao Singleton: garante uma única instância da classe
    public static function getInstance()
    {
        if (self::$instance === null) self::$instance = new self();
        return self::$instance;
    }

    // Cria/garante a existência das tabelas e insere um usuário admin padrão
    private function createTables()
    {
        // --- Tabela de usuários ---
        // id autoincrement, nome/email/senha obrigatórios, email único
        $this->pdo->exec("
            CREATE TABLE IF NOT EXISTS users (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                nome  TEXT NOT NULL,
                email TEXT NOT NULL UNIQUE,
                senha TEXT NOT NULL
            );
        ");

        // --- Tabela de livros (CRUD principal) ---
        // Campos básicos + 'criado_em' com valor padrão datetime('now') do SQLite
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

        // --- Usuário admin padrão ---
        // Se a tabela 'users' estiver vazia, cria o admin: admin@local / admin123
        $row = $this->pdo->query("SELECT COUNT(*) AS c FROM users")->fetch();
        if ((int)$row['c'] === 0) {
            // Gera hash seguro da senha padrão
            $hash = password_hash('admin123', PASSWORD_DEFAULT);

            // Insere usuário inicial Administrador
            $st = $this->pdo->prepare("INSERT INTO users (nome,email,senha) VALUES (?,?,?)");
            $st->execute(['Administrador', 'admin@local', $hash]);
        }
    }
}

// Inicializa a conexão (opcional: garante que a criação de tabelas rode ao carregar este arquivo)
$db = Database::getInstance();
