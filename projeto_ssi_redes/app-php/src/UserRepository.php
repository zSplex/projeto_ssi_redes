<?php
/**
 * Repositório de Usuários - Operações com a tabela users
 * Projeto SSI/IP - InovaSoft
 */

require_once __DIR__ . '/db.php';

class UserRepository {
    private $pdo;
    
    public function __construct() {
        $this->pdo = getDB();
    }
    
    /**
     * Buscar usuário por username
     */
    public function findByUsername($username) {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE username = ? LIMIT 1");
        $stmt->execute([$username]);
        return $stmt->fetch();
    }
    
    /**
     * Buscar usuário por ID
     */
    public function findById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE id = ? LIMIT 1");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    /**
     * Listar todos os usuários
     */
    public function all() {
        $stmt = $this->pdo->query("SELECT id, name, username, group_name, is_admin, created_at FROM users ORDER BY name");
        return $stmt->fetchAll();
    }
    
    /**
     * Criar novo usuário
     */
    public function create($data) {
        $sql = "INSERT INTO users (name, username, password_hash, group_name, is_admin) 
                VALUES (:name, :username, :password_hash, :group_name, :is_admin)";
        
        $stmt = $this->pdo->prepare($sql);
        
        return $stmt->execute([
            'name' => $data['name'],
            'username' => $data['username'],
            'password_hash' => password_hash($data['password'], PASSWORD_DEFAULT),
            'group_name' => $data['group_name'],
            'is_admin' => $data['is_admin'] ?? 0
        ]);
    }
    
    /**
     * Atualizar usuário
     */
    public function update($id, $data) {
        // Se senha foi fornecida, atualiza também
        if (!empty($data['password'])) {
            $sql = "UPDATE users SET name = :name, username = :username, password_hash = :password_hash, 
                    group_name = :group_name, is_admin = :is_admin WHERE id = :id";
            
            return $this->pdo->prepare($sql)->execute([
                'id' => $id,
                'name' => $data['name'],
                'username' => $data['username'],
                'password_hash' => password_hash($data['password'], PASSWORD_DEFAULT),
                'group_name' => $data['group_name'],
                'is_admin' => $data['is_admin'] ?? 0
            ]);
        } else {
            // Atualizar sem mudar senha
            $sql = "UPDATE users SET name = :name, username = :username, 
                    group_name = :group_name, is_admin = :is_admin WHERE id = :id";
            
            return $this->pdo->prepare($sql)->execute([
                'id' => $id,
                'name' => $data['name'],
                'username' => $data['username'],
                'group_name' => $data['group_name'],
                'is_admin' => $data['is_admin'] ?? 0
            ]);
        }
    }
    
    /**
     * Excluir usuário
     */
    public function delete($id) {
        $stmt = $this->pdo->prepare("DELETE FROM users WHERE id = ?");
        return $stmt->execute([$id]);
    }
    
    /**
     * Verificar se username já existe
     */
    public function usernameExists($username, $excludeId = null) {
        if ($excludeId) {
            $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM users WHERE username = ? AND id != ?");
            $stmt->execute([$username, $excludeId]);
        } else {
            $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM users WHERE username = ?");
            $stmt->execute([$username]);
        }
        
        return $stmt->fetchColumn() > 0;
    }
    
    /**
     * Contar usuários por grupo
     */
    public function countByGroup() {
        $stmt = $this->pdo->query("SELECT group_name, COUNT(*) as total FROM users GROUP BY group_name");
        return $stmt->fetchAll();
    }
}

