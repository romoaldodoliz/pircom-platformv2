<?php
/**
 * Pircom/admin/helpers/upload.php
 *
 * Estrutura do projecto:
 *
 *   Pircom/                          ← RAIZ
 *       uploads/
 *           movimentos/              ← ficheiros guardados aqui
 *           eventos/
 *           ...
 *       admin/
 *           helpers/
 *               upload.php           ← ESTE FICHEIRO
 *
 * Caminho guardado no DB : "uploads/movimentos/abc123_1234567890.jpg"
 * Caminho absoluto disco  : /var/www/.../Pircom/uploads/movimentos/abc123_1234567890.jpg
 * Tag <img> no admin/     : ../uploads/movimentos/abc123_1234567890.jpg
 */

class ImageUploader {

    private array $allowed_types = [
        'image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'
    ];
    private int $max_size = 10485760; // 10 MB

    // ── RAIZ DO PROJECTO ─────────────────────────────────────────────────
    //
    //  __FILE__                               = .../Pircom/admin/helpers/upload.php
    //  dirname(__FILE__)                      = .../Pircom/admin/helpers
    //  dirname(dirname(__FILE__))             = .../Pircom/admin
    //  dirname(dirname(dirname(__FILE__)))    = .../Pircom          ← RAIZ
    //
    private function root(): string {
        return dirname(dirname(dirname(__FILE__)));
        // Para forçar um caminho fixo, descomenta a linha abaixo e comenta a de cima:
        // return '/var/www/html/Pircom';
    }

    // ── CAMINHO ABSOLUTO DA PASTA DE UPLOAD ──────────────────────────────
    // Ex: /var/www/.../Pircom/uploads/movimentos/
    private function dir(string $subdir): string {
        return $this->root()
            . DIRECTORY_SEPARATOR . 'uploads'
            . DIRECTORY_SEPARATOR . trim($subdir, '/\\')
            . DIRECTORY_SEPARATOR;
    }

    // ── CAMINHO RELATIVO GUARDADO NO DB ──────────────────────────────────
    // Ex: uploads/movimentos/abc123_1234567890.jpg
    private function dbPath(string $subdir, string $filename): string {
        return 'uploads/' . trim($subdir, '/\\') . '/' . $filename;
    }

    // ─────────────────────────────────────────────────────────────────────
    // UPLOAD ÚNICO
    // ─────────────────────────────────────────────────────────────────────
    /**
     * @param  array  $file       Elemento de $_FILES  (ex: $_FILES['imagem_principal'])
     * @param  string $subdir     Subpasta em uploads/ (ex: 'movimentos')
     * @param  int    $max_width  Largura máxima px
     * @param  int    $max_height Altura máxima px
     * @return array  ['success'=>bool, 'message'=>string, 'path'=>string, 'filename'=>string]
     */
    public function uploadImage(array $file, string $subdir, int $max_width = 1200, int $max_height = 1200): array {

        // 1. Sem ficheiro
        if (empty($file) || ($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
            return $this->fail('Nenhum ficheiro enviado.');
        }

        // 2. Erro de upload
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return $this->fail($this->errMsg($file['error']));
        }

        // 3. Tamanho
        if ($file['size'] > $this->max_size) {
            return $this->fail('Ficheiro demasiado grande. Máximo: ' . ($this->max_size / 1048576) . 'MB.');
        }

        // 4. Tipo MIME real (não confiar no $_FILES['type'])
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime  = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);
        if (!in_array($mime, $this->allowed_types)) {
            return $this->fail("Tipo não permitido ({$mime}). Aceites: JPG, PNG, GIF, WebP.");
        }

        // 5. Criar pasta se não existir
        $uploadDir = $this->dir($subdir);
        if (!is_dir($uploadDir)) {
            if (!mkdir($uploadDir, 0755, true)) {
                return $this->fail("Não foi possível criar a pasta:\n{$uploadDir}");
            }
        }
        if (!is_writable($uploadDir)) {
            return $this->fail("Sem permissão de escrita em:\n{$uploadDir}\n→ execute: chmod -R 755 {$uploadDir}");
        }

        // 6. Nome de ficheiro único
        $ext      = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION) ?: 'jpg');
        $filename = uniqid() . '_' . time() . '.' . $ext;
        $dest     = $uploadDir . $filename;

        // 7. Redimensionar e gravar
        if (!$this->resize($file['tmp_name'], $dest, $max_width, $max_height, $mime)) {
            return $this->fail('Erro ao processar imagem. Verifica se a extensão GD está activa.');
        }

        return [
            'success'  => true,
            'message'  => 'Upload realizado com sucesso.',
            'path'     => $this->dbPath($subdir, $filename), // guardado no DB
            'filename' => $filename,
        ];
    }

    // ─────────────────────────────────────────────────────────────────────
    // UPLOAD MÚLTIPLO
    // ─────────────────────────────────────────────────────────────────────
    /**
     * Aceita directamente o array $_FILES['campo'] com múltiplos ficheiros.
     */
    public function uploadMultiple(array $files, string $subdir, int $max_width = 1200, int $max_height = 1200): array {
        $results = [];
        $count   = count($files['name'] ?? []);
        for ($i = 0; $i < $count; $i++) {
            $results[] = $this->uploadImage([
                'name'     => $files['name'][$i],
                'type'     => $files['type'][$i],
                'tmp_name' => $files['tmp_name'][$i],
                'error'    => $files['error'][$i],
                'size'     => $files['size'][$i],
            ], $subdir, $max_width, $max_height);
        }
        return $results;
    }

    // ─────────────────────────────────────────────────────────────────────
    // APAGAR IMAGEM
    // ─────────────────────────────────────────────────────────────────────
    /**
     * Apaga o ficheiro físico usando o caminho relativo guardado no DB.
     *
     * Exemplo:
     *   $path = "uploads/movimentos/abc123.jpg"   (valor do DB)
     *   Apaga: /var/www/.../Pircom/uploads/movimentos/abc123.jpg
     */
    public function deleteImage(string $db_path): bool {
        if (empty(trim($db_path))) return false;

        $abs = $this->root()
             . DIRECTORY_SEPARATOR
             . ltrim(str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $db_path), DIRECTORY_SEPARATOR);

        if (file_exists($abs)) {
            return (bool) @unlink($abs);
        }
        return false;
    }

    // ─────────────────────────────────────────────────────────────────────
    // DEBUG
    // ─────────────────────────────────────────────────────────────────────
    /**
     * Informação de diagnóstico — chamar apenas em desenvolvimento.
     * Remover (ou proteger com autenticação) em produção.
     */
    public function debugInfo(string $subdir = 'movimentos'): array {
        $root = $this->root();
        $dir  = $this->dir($subdir);
        return [
            'project_root'  => $root,
            'upload_dir'    => $dir,
            'dir_exists'    => is_dir($dir),
            'dir_writable'  => is_dir($dir) && is_writable($dir),
            'document_root' => $_SERVER['DOCUMENT_ROOT'] ?? 'N/D',
            'this_file'     => __FILE__,
            'gd_enabled'    => extension_loaded('gd'),
            'finfo_enabled' => extension_loaded('fileinfo'),
            'sample_db'     => $this->dbPath($subdir, 'exemplo.jpg'),
            'sample_abs'    => $dir . 'exemplo.jpg',
            'sample_img_src'=> '../' . $this->dbPath($subdir, 'exemplo.jpg'),
        ];
    }

    // ─────────────────────────────────────────────────────────────────────
    // MÉTODOS PRIVADOS
    // ─────────────────────────────────────────────────────────────────────

    private function fail(string $msg): array {
        return ['success' => false, 'message' => $msg, 'path' => '', 'filename' => ''];
    }

    private function errMsg(int $code): string {
        return [
            UPLOAD_ERR_INI_SIZE   => 'Ficheiro excede upload_max_filesize no php.ini.',
            UPLOAD_ERR_FORM_SIZE  => 'Ficheiro excede MAX_FILE_SIZE do formulário.',
            UPLOAD_ERR_PARTIAL    => 'Upload incompleto. Tente novamente.',
            UPLOAD_ERR_NO_TMP_DIR => 'Pasta temporária não encontrada.',
            UPLOAD_ERR_CANT_WRITE => 'Falha ao escrever no disco.',
            UPLOAD_ERR_EXTENSION  => 'Upload bloqueado por extensão PHP.',
        ][$code] ?? "Erro de upload (código {$code}).";
    }

    private function resize(string $src, string $dst, int $maxW, int $maxH, string $mime): bool {
        $info = @getimagesize($src);
        if (!$info) return false;

        [$origW, $origH] = $info;
        $ratio = min($maxW / $origW, $maxH / $origH);

        // Imagem já cabe — mover directamente sem reprocessar
        if ($ratio >= 1) {
            return move_uploaded_file($src, $dst);
        }

        $newW = (int)($origW * $ratio);
        $newH = (int)($origH * $ratio);

        $srcImg = match ($mime) {
            'image/jpeg', 'image/jpg' => @imagecreatefromjpeg($src),
            'image/png'               => @imagecreatefrompng($src),
            'image/gif'               => @imagecreatefromgif($src),
            'image/webp'              => @imagecreatefromwebp($src),
            default                   => false,
        };
        if (!$srcImg) return false;

        $dstImg = imagecreatetruecolor($newW, $newH);

        // Preservar canal alpha (PNG / WebP / GIF)
        if (in_array($mime, ['image/png', 'image/gif', 'image/webp'])) {
            imagealphablending($dstImg, false);
            imagesavealpha($dstImg, true);
            imagefilledrectangle($dstImg, 0, 0, $newW, $newH,
                imagecolorallocatealpha($dstImg, 255, 255, 255, 127));
        }

        imagecopyresampled($dstImg, $srcImg, 0, 0, 0, 0, $newW, $newH, $origW, $origH);

        $ok = match ($mime) {
            'image/jpeg', 'image/jpg' => imagejpeg($dstImg, $dst, 85),
            'image/png'               => imagepng($dstImg, $dst, 8),
            'image/gif'               => imagegif($dstImg, $dst),
            'image/webp'              => imagewebp($dstImg, $dst, 85),
            default                   => false,
        };

        imagedestroy($srcImg);
        imagedestroy($dstImg);
        return $ok;
    }
}

// ── FUNÇÕES UTILITÁRIAS ───────────────────────────────────────────────────

function sanitize_input(string $data): string {
    return htmlspecialchars(stripslashes(trim($data)), ENT_QUOTES, 'UTF-8');
}

function validate_email(string $email): bool {
    return (bool) filter_var($email, FILTER_VALIDATE_EMAIL);
}

function generate_csrf_token(): string {
    if (session_status() === PHP_SESSION_NONE) session_start();
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function validate_csrf_token(string $token): bool {
    if (session_status() === PHP_SESSION_NONE) session_start();
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}