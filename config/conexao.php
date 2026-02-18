<?php
// Configuração da conexão com o banco de dados
$hostname = "localhost:8889"; // Substitua pelo nome do host do seu banco de dados
$username = "root"; // Substitua pelo nome de usuário do seu banco de dados
$password = "root"; // Substitua pela senha do seu banco de dados
$database = "pircom"; // Substitua pelo nome do seu banco de dados



// Tentar estabelecer a conexão
$conn = new mysqli($hostname, $username, $password, $database);

// Verificar se ocorreu algum erro na conexão
if ($conn->connect_error) {
    die("Erro na conexão com o banco de dados: " . $conn->connect_error);
}else{
    // echo "conectado com sucesso";
}
// Defina o conjunto de caracteres para UTF-8 (opcional)
$conn->set_charset("utf8");
?>