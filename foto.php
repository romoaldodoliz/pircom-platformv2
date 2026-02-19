<?php
include 'config/conexao.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if (!$id) { http_response_code(404); exit; }

$result = $conn->query("SELECT foto FROM team_members WHERE id = $id LIMIT 1");
if (!$result || $result->num_rows === 0) { http_response_code(404); exit; }

$row = $result->fetch_assoc();
if (empty($row['foto'])) { http_response_code(404); exit; }

$bin  = $row['foto'];
$type = 'image/jpeg';
if (substr($bin, 0, 4) === "\x89PNG")  $type = 'image/png';
elseif (substr($bin, 0, 4) === 'GIF8') $type = 'image/gif';
elseif (substr($bin, 0, 4) === 'RIFF') $type = 'image/webp';

header("Content-Type: $type");
header("Cache-Control: public, max-age=86400");
header("Content-Length: " . strlen($bin));
echo $bin;
$conn->close();
exit;