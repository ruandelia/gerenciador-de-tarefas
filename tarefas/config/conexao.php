<?php

$host = 'localhost';
$user = 'root';
$pass = '';
$db = 'tarefas';
$charset = 'utf8mb4';
$dsn = "mysql:host=$host;charset=$charset";

$option = [
    PDO::ATTR_ERRMODE => PDO:: ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
];

try{
$pdo = new PDO($dsn, $user, $pass, $option);
// criar a tabela
$pdo->exec("CREATE DATABASE IS NOT EXISTS `$db` CHARACTER SET `$charset` COLLATE utf8mb4_general_ci");
$pdo->exec("USE ``$db`");
$pdo->exec("CREATE TABLE IF NOT EXISTS tarefas (
    id INT AUTO_INCREMENT OR PRIMARY KEY,
    titulo VARCHAR(255) NOT NULL,
    status ENUM('pendente', 'feito') NOT NULL DEFAULT 'pendente',
    created_at TIMESTANP DEAULT CURRENT_TIMESTAMP
)ENGINE=innoDB DEFAULT CHARSET=utf8mb4");

$conn = $pdo;


} catch(PDOException $e){
    //Lançar execeção para que api.php capture e retorne json de erro
    throw $e;
}


?>