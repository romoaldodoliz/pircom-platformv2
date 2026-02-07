<?php

$hostname = "localhost"; // Substitua pelo nome do host do seu banco de dados
$port = 8889; // Porta do MySQL no MAMP
$username = "root"; // Substitua pelo nome de usuário do seu banco de dados
$password = "root"; // Substitua pela senha do seu banco de dados
$database = "pircom"; // Substitua pelo nome do seu banco de dados

// Tentar estabelecer a conexão
$conn = new mysqli($hostname, $username, $password, $database, $port);

// Verificar se ocorreu algum erro na conexão
if ($conn->connect_error) {
    die("Erro na conexão com o banco de dados: " . $conn->connect_error);
} else {
    // echo "conectado com sucesso";
}

// Defina o conjunto de caracteres para UTF-8 (utf8mb4 para compatibilidade total)
$conn->set_charset("utf8mb4");
?>
