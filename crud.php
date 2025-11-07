<?php

// Inclui o arquivo de configuração e o arquivo da classe Database Singleton
require_once 'config.php';
require_once 'database.php';

// Obtém a instância da conexão PDO do Singleton
$db = Database::getInstance();
$pdo = $db->getConnection();

/**
 * Classe para realizar operações CRUD com a conexão PDO existente.
 * Esta classe é adaptada para usar o objeto PDO da classe Database Singleton.
 */
class CrudOperations
{
    private $conn;

    public function __construct(PDO $pdo_connection)
    {
        $this->conn = $pdo_connection;
    }

    // --- C - Create (Criar) ---
    public function create($table, $data)
    {
        try {
            // Verifica se as chaves 'nome', 'email' e 'senha' estão presentes
            if (!isset($data['nome'], $data['email'], $data['senha'])) {
                throw new Exception("Dados incompletos para criação.");
            }

            $fields = array_keys($data);
            $values = array_values($data);
            $placeholders = str_repeat("?,", count($fields) - 1) . "?";
            $fields_string = implode(", ", $fields);

            // Hash da senha antes de inserir (boa prática)
            $data['senha'] = password_hash($data['senha'], PASSWORD_DEFAULT);
            $values[array_search('senha', $fields)] = $data['senha'];

            $sql = "INSERT INTO $table ($fields_string) VALUES ($placeholders)";

            $stmt = $this->conn->prepare($sql);
            $result = $stmt->execute($values);

            if ($result) {
                return [
                    'success' => true,
                    'id' => $this->conn->lastInsertId(),
                    'message' => 'Registro criado com sucesso!'
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Erro ao criar o registro'
                ];
            }
        } catch (PDOException $e) {
            return [
                'success' => false,
                'message' => 'Erro no banco: ' . $e->getMessage()
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    // --- R - Read (Ler) ---
    public function read($table, $conditions = [], $limit = null, $offset = null)
    {
        try {
            $sql = "SELECT * FROM $table";
            $params = [];

            if (!empty($conditions)) {
                $where_conditions = [];
                foreach ($conditions as $field => $value) {
                    $where_conditions[] = "$field = ?";
                    $params[] = $value;
                }
                $sql .= " WHERE " . implode(" AND ", $where_conditions);
            }

            // A sintaxe de LIMIT e OFFSET é a mesma no SQLite e MySQL
            if ($limit !== null) {
                $sql .= " LIMIT $limit";
                if ($offset !== null) {
                    $sql .= " OFFSET $offset";
                }
            }

            $stmt = $this->conn->prepare($sql);
            $stmt->execute($params);

            return [
                'success' => true,
                'data' => $stmt->fetchAll(PDO::FETCH_ASSOC),
                'count' => $stmt->rowCount()
            ];
        } catch (PDOException $e) {
            return [
                'success' => false,
                'message' => 'Erro no banco: ' . $e->getMessage()
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    // --- U - Update (Atualizar) ---
    public function update($table, $data, $conditions = [])
    {
        try {
            if (empty($data) || empty($conditions)) {
                throw new Exception("Dados ou condições de atualização não podem ser vazios.");
            }

            $sql = "UPDATE $table SET ";
            $params = [];

            $set_fields = [];
            foreach ($data as $field => $value) {
                // Se estiver atualizando a senha, faça o hash
                if ($field === 'senha') {
                    $value = password_hash($value, PASSWORD_DEFAULT);
                }
                $set_fields[] = "$field = ?";
                $params[] = $value;
            }
            $sql .= implode(", ", $set_fields);

            $where_conditions = [];
            foreach ($conditions as $field => $value) {
                $where_conditions[] = "$field = ?";
                $params[] = $value;
            }
            $sql .= " WHERE " . implode(" AND ", $where_conditions);

            $stmt = $this->conn->prepare($sql);
            $stmt->execute($params);

            return [
                'success' => true,
                'affected_rows' => $stmt->rowCount(),
                'message' => $stmt->rowCount() > 0 ? 'Registro(s) alterados(s) com sucesso!' : 'Nenhum registro foi alterado'
            ];
        } catch (PDOException $e) {
            return [
                'success' => false,
                'message' => 'Erro no banco: ' . $e->getMessage()
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    // --- D - Delete (Deletar) ---
    public function delete($table, $conditions = [])
    {
        try {
            if (empty($conditions)) {
                throw new Exception("Condições de deleção não podem ser vazias para evitar deletar todos os registros.");
            }

            $sql = "DELETE FROM $table";
            $params = [];

            $where_conditions = [];
            foreach ($conditions as $field => $value) {
                $where_conditions[] = "$field = ?";
                $params[] = $value;
            }
            $sql .= " WHERE " . implode(" AND ", $where_conditions);

            $stmt = $this->conn->prepare($sql);
            $stmt->execute($params);

            return [
                'success' => true,
                'affected_rows' => $stmt->rowCount(),
                'message' => $stmt->rowCount() > 0 ? 'Registro(s) deletado(s) com sucesso!' : 'Nenhum registro foi deletado'
            ];
        } catch (PDOException $e) {
            return [
                'success' => false,
                'message' => 'Erro no banco: ' . $e->getMessage()
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }
}

// Cria uma instância da classe de operações CRUD
$crud = new CrudOperations($pdo);
$table = 'users';

echo "<h1>Teste de Operações CRUD (SQLite)</h1>";
echo "---";

// 1. C - Create (Criar)
echo "<h2>1. C - Criar Registro</h2>";
$user_data_1 = ['nome' => 'João Silva', 'email' => 'joao.silva@exemplo.com', 'senha' => 'senha123'];
$result_create_1 = $crud->create($table, $user_data_1);
echo "Resultado 1: " . json_encode($result_create_1, JSON_PRETTY_PRINT) . "<br><br>";

$user_data_2 = ['nome' => 'Maria Souza', 'email' => 'maria.souza@exemplo.com', 'senha' => 'abc456'];
$result_create_2 = $crud->create($table, $user_data_2);
echo "Resultado 2: " . json_encode($result_create_2, JSON_PRETTY_PRINT) . "<br><br>";

// 2. R - Read (Ler Todos)
echo "<h2>2. R - Ler Todos os Registros</h2>";
$result_read_all = $crud->read($table);
echo "Registros encontrados ({$result_read_all['count']}): <pre>" . json_encode($result_read_all['data'], JSON_PRETTY_PRINT) . "</pre><br>";

// 3. U - Update (Atualizar)
echo "<h2>3. U - Atualizar Registro</h2>";
// Vamos tentar atualizar o e-mail do primeiro usuário que criamos (se tiver tido sucesso)
$user_id_to_update = isset($result_create_1['id']) ? $result_create_1['id'] : 1; 
$update_data = ['email' => 'joao.novo@email.com'];
$update_conditions = ['id' => $user_id_to_update];

$result_update = $crud->update($table, $update_data, $update_conditions);
echo "Resultado: " . json_encode($result_update, JSON_PRETTY_PRINT) . "<br><br>";

// 4. R - Read (Ler o registro atualizado para confirmar)
echo "<h2>4. R - Ler Registro Atualizado (ID {$user_id_to_update})</h2>";
$result_read_updated = $crud->read($table, ['id' => $user_id_to_update]);
echo "Registro encontrado: <pre>" . json_encode($result_read_updated['data'], JSON_PRETTY_PRINT) . "</pre><br>";

// 5. D - Delete (Deletar)
echo "<h2>5. D - Deletar Registro</h2>";
// Deletar o segundo registro que criamos
$user_id_to_delete = isset($result_create_2['id']) ? $result_create_2['id'] : 2; 
$delete_conditions = ['id' => $user_id_to_delete];

$result_delete = $crud->delete($table, $delete_conditions);
echo "Resultado: " . json_encode($result_delete, JSON_PRETTY_PRINT) . "<br><br>";

// 6. R - Read (Ler todos após a deleção para confirmar)
echo "<h2>6. R - Ler Todos os Registros (Após Deleção)</h2>";
$result_read_final = $crud->read($table);
echo "Registros encontrados ({$result_read_final['count']}): <pre>" . json_encode($result_read_final['data'], JSON_PRETTY_PRINT) . "</pre><br>";

?>