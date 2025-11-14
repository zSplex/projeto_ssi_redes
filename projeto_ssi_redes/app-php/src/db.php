<?php
/**
 * Conexão com Banco de Dados - PDO MySQL
 * Projeto SSI/IP - InovaSoft
 */

require_once __DIR__ . '/config.php';

function getDB() {
    static $pdo = null;
    
    if ($pdo === null) {
        try {
            $dsn = sprintf(
                "mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4",
                DB_HOST,
                DB_PORT,
                DB_NAME
            );
            
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false, // Prepared statements reais
            ];
            
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
            
        } catch (PDOException $e) {
            // Em produção, não expor detalhes do erro
            if (APP_DEBUG) {
                die("Erro de conexão com o banco de dados: " . $e->getMessage());
            } else {
                die("Erro ao conectar ao banco de dados. Contate o administrador.");
            }
        }
    }
    
    return $pdo;
}

