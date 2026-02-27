<?php
/**
 * get_hero_image.php
 * Serve a imagem BLOB da tabela homepagehero como resposta HTTP.
 * Uso: <img src="get_hero_image.php?id=3">
 */
include('config/conexao.php');

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id <= 0) { http_response_code(400); exit; }

$stmt = $conn->prepare("SELECT foto FROM homepagehero WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows === 0) { http_response_code(404); exit; }

$stmt->bind_result($foto);
$stmt->fetch();
$stmt->close();
$conn->close();

if (empty($foto)) { http_response_code(404); exit; }

$finfo = new finfo(FILEINFO_MIME_TYPE);
$mime  = $finfo->buffer($foto) ?: 'image/jpeg';

header('Content-Type: '   . $mime);
header('Cache-Control: public, max-age=3600');
header('Content-Length: ' . strlen($foto));
echo $foto;
exit;