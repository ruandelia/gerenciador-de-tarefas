<?php

$host = 'localhost';
$user = 'root';
$pass = '';
$db = 'tarefas';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;charset=$charset";
$option = [
    // Define como o PDO lida com erros
    // PDO::ERRMODE_EXCEPTION faz com que os erros disparem exceções,
    //permitindo tratar problemas com try/catch
    // Evita erros silenciosos e facilita o debug 
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,

    // Define o formato padrão de retorno dos dados do banco
    // PDO::FETCH_ASSOC retorna os dados em um Array associativo
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
    // conexão ao servidor e garantir o DB exista 
    $pdo = new PDO($dsn,$user,$pass,$option);

    // Aqui vamos criar nosso banco se ele não existir
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$db` CHARACTER SET `$charset` COLLATE utf8mb4_general_ci");
    $pdo->exec("USE `$db`");

    // cria a tabela se não existir
    $pdo->exec("CREATE TABLE IF NOT EXISTS tarefas (
        id INT AUTO_INCREMENT PRIMARY KEY,
        titulo VARCHAR(255) NOT NULL, 
        status ENUM('pendente','feito') NOT NULL DEFAULT 'pendente',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP

    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    $conn = $pdo;
} catch (PDOException $e) {
    //Lançar execeção para que api.php capture e retorne Json de erro
    throw $e;
}


?>