<?php
session_start();
include('header.php');
include('config/conexao.php');

// Verificar se o usuário está autenticado (se necessário)
// if (!isset($_SESSION['user_id'])) {
//     header('Location: login.php');
//     exit;
// }

$success = '';
$error = '';

// Processar submissão do formulário
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    // Sanitizar e validar inputs
    $titulo = trim($_POST["titulo"] ?? '');
    $descricao = trim($_POST["descricao"] ?? '');
    $tipo = $_POST["tipo"] ?? '';
    
    // Validações básicas
    if (empty($titulo) || empty($tipo)) {
        $error = "Título e Tipo são obrigatórios!";
    } else {
        // Preparar valores com base no tipo
        if ($tipo === 'video') {
            // Processar vídeo (YouTube link)
            $link = trim($_POST["youtube_link"] ?? '');
            
            // Validar link do YouTube
            if (empty($link) || !filter_var($link, FILTER_VALIDATE_URL)) {
                $error = "Por favor, insira um link válido do YouTube!";
            } else {
                // Extrair ID do vídeo do YouTube se necessário
                $youtube_id = extractYouTubeId($link);
                
                // Usar prepared statement para segurança
                $sql = "INSERT INTO galeria (titulo, descricao, link, foto, tipo, created_date) 
                        VALUES (?, ?, ?, '', 'video', CURDATE())";
                
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("sss", $titulo, $descricao, $link);
                
                if ($stmt->execute()) {
                    $success = "Vídeo adicionado com sucesso!";
                    // Limpar formulário após sucesso
                    $titulo = $descricao = $link = '';
                } else {
                    $error = "Erro ao adicionar vídeo: " . $stmt->error;
                }
                $stmt->close();
            }
        } elseif ($tipo === 'imagem') {
            // Processar imagem (upload de arquivo)
            $link = ''; // Para imagem, o link não é usado
            $foto_data = null;
            
            // Processar upload da imagem
            if (isset($_FILES['imagem_upload']) && $_FILES['imagem_upload']['error'] === UPLOAD_ERR_OK) {
                $upload_result = processImageUpload($_FILES['imagem_upload']);
                
                if ($upload_result['success']) {
                    $foto_data = $upload_result['data'];
                    
                    // Usar prepared statement para segurança
                    $sql = "INSERT INTO galeria (titulo, descricao, link, foto, tipo, created_date) 
                            VALUES (?, ?, ?, ?, 'imagem', CURDATE())";
                    
                    $stmt = $conn->prepare($sql);
                    $null = null;
                    $stmt->bind_param("sssb", $titulo, $descricao, $link, $null);
                    $stmt->send_long_data(3, $foto_data);
                    
                    if ($stmt->execute()) {
                        $success = "Imagem carregada com sucesso!";
                        // Limpar formulário após sucesso
                        $titulo = $descricao = '';
                    } else {
                        $error = "Erro ao carregar imagem: " . $stmt->error;
                    }
                    $stmt->close();
                } else {
                    $error = $upload_result['error'];
                }
            } else {
                $error = "Por favor, selecione uma imagem para upload!";
            }
        }
    }
}

// Função para extrair ID do vídeo do YouTube
function extractYouTubeId($url) {
    $pattern = '/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/';
    preg_match($pattern, $url, $matches);
    return $matches[1] ?? $url;
}

// Função para processar upload de imagem
function processImageUpload($file) {
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    $max_size = 5 * 1024 * 1024; // 5MB
    
    // Verificar tipo de arquivo
    if (!in_array($file['type'], $allowed_types)) {
        return ['success' => false, 'error' => 'Tipo de arquivo não permitido. Use JPEG, PNG, GIF ou WebP.'];
    }
    
    // Verificar tamanho do arquivo
    if ($file['size'] > $max_size) {
        return ['success' => false, 'error' => 'Arquivo muito grande. Máximo 5MB.'];
    }
    
    // Ler arquivo como dados binários
    $image_data = file_get_contents($file['tmp_name']);
    if ($image_data === false) {
        return ['success' => false, 'error' => 'Erro ao ler o arquivo.'];
    }
    
    // Opcional: Redimensionar imagem se necessário
    // $image_data = resizeImageIfNeeded($image_data, 1920, 1080);
    
    return ['success' => true, 'data' => $image_data];
}

$conn->close();
?>

<!-- Content wrapper -->
<div class="content-wrapper">
    <!-- Content -->
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4">
            <span class="text-muted fw-light">Galeria /</span> Adicionar Conteúdo
        </h4>
        
        <!-- Mensagens de feedback -->
        <?php if ($success): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php echo htmlspecialchars($success); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php echo htmlspecialchars($error); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        
        <div class="row">
            <div class="col-md-12">
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5>Novo Item na Galeria</h5>
                        <small class="text-muted">Escolha entre adicionar vídeo ou imagem</small>
                    </div>
                    <div class="card-body">
                        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST" enctype="multipart/form-data">
                            <!-- Tipo de conteúdo -->
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <label class="form-label">Tipo de Conteúdo *</label>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="radio" name="tipo" id="tipo_video" value="video" required>
                                        <label class="form-check-label" for="tipo_video">
                                            <i class="bx bx-video me-1"></i> Vídeo do YouTube
                                        </label>
                                        <small class="form-text text-muted d-block">Cole o link do YouTube</small>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="tipo" id="tipo_imagem" value="imagem" required>
                                        <label class="form-check-label" for="tipo_imagem">
                                            <i class="bx bx-image me-1"></i> Imagem (Upload)
                                        </label>
                                        <small class="form-text text-muted d-block">Faça upload de uma imagem</small>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Campos comuns -->
                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <label for="titulo" class="form-label">Título *</label>
                                    <input type="text" name="titulo" id="titulo" class="form-control" 
                                           value="<?php echo htmlspecialchars($titulo ?? ''); ?>" 
                                           placeholder="Digite o título" required>
                                </div>
                                
                                <div class="col-md-12 mb-3">
                                    <label for="descricao" class="form-label">Descrição</label>
                                    <textarea class="form-control" name="descricao" id="descricao" rows="3" 
                                              placeholder="Descrição opcional"><?php echo htmlspecialchars($descricao ?? ''); ?></textarea>
                                </div>
                                
                                <!-- Campo para Vídeo (YouTube) -->
                                <div class="col-md-12 mb-3 video-field" style="display: none;">
                                    <label for="youtube_link" class="form-label">Link do YouTube *</label>
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="bx bx-link"></i>
                                        </span>
                                        <input type="url" name="youtube_link" id="youtube_link" 
                                               class="form-control" 
                                               placeholder="https://www.youtube.com/watch?v=..." 
                                               value="<?php echo htmlspecialchars($link ?? ''); ?>">
                                    </div>
                                    <small class="form-text text-muted">
                                        Exemplo: https://www.youtube.com/watch?v=ID_DO_VIDEO
                                    </small>
                                    <div class="mt-2" id="youtube_preview"></div>
                                </div>
                                
                                <!-- Campo para Imagem (Upload) -->
                                <div class="col-md-12 mb-3 image-field" style="display: none;">
                                    <label for="imagem_upload" class="form-label">Upload de Imagem *</label>
                                    <div class="input-group">
                                        <input type="file" name="imagem_upload" id="imagem_upload" 
                                               class="form-control" accept="image/*">
                                    </div>
                                    <small class="form-text text-muted">
                                        Formatos permitidos: JPEG, PNG, GIF, WebP. Máximo: 5MB
                                    </small>
                                    <div class="mt-2" id="image_preview"></div>
                                </div>
                            </div>
                            
                            <div class="mt-4">
                                <button class="btn btn-primary me-2" name="submit" type="submit">
                                    <i class="bx bx-upload me-1"></i> Adicionar à Galeria
                                </button>
                                <a href="galeria.php" class="btn btn-outline-secondary">
                                    <i class="bx bx-x me-1"></i> Cancelar
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- / Content -->

    <!-- Footer -->
    <?php include('footerprincipal.php'); ?>
    <!-- / Footer -->
    <div class="content-backdrop fade"></div>
</div>
<!-- Content wrapper -->

<script>
// Mostrar/ocultar campos baseado no tipo selecionado
document.querySelectorAll('input[name="tipo"]').forEach(radio => {
    radio.addEventListener('change', function() {
        const tipo = this.value;
        
        // Esconder todos os campos específicos
        document.querySelectorAll('.video-field, .image-field').forEach(field => {
            field.style.display = 'none';
            // Limpar campos quando ocultados
            if (field.classList.contains('video-field')) {
                document.getElementById('youtube_link').value = '';
                document.getElementById('youtube_preview').innerHTML = '';
            } else if (field.classList.contains('image-field')) {
                document.getElementById('imagem_upload').value = '';
                document.getElementById('image_preview').innerHTML = '';
            }
        });
        
        // Mostrar campo correspondente
        if (tipo === 'video') {
            document.querySelector('.video-field').style.display = 'block';
        } else if (tipo === 'imagem') {
            document.querySelector('.image-field').style.display = 'block';
        }
    });
});

// Preview do link do YouTube
document.getElementById('youtube_link').addEventListener('input', function() {
    const link = this.value.trim();
    const previewDiv = document.getElementById('youtube_preview');
    
    if (link) {
        const videoId = extractYouTubeId(link);
        if (videoId) {
            previewDiv.innerHTML = `
                <div class="alert alert-info p-2">
                    <small><i class="bx bx-check-circle"></i> Link válido do YouTube detectado</small>
                    <div class="mt-1">
                        <img src="https://img.youtube.com/vi/${videoId}/hqdefault.jpg" 
                             alt="Thumbnail do vídeo" 
                             class="img-thumbnail" style="max-width: 120px;">
                    </div>
                </div>`;
        }
    } else {
        previewDiv.innerHTML = '';
    }
});

// Preview da imagem antes do upload
document.getElementById('imagem_upload').addEventListener('change', function(e) {
    const file = e.target.files[0];
    const previewDiv = document.getElementById('image_preview');
    
    if (file && file.type.startsWith('image/')) {
        const reader = new FileReader();
        reader.onload = function(e) {
            previewDiv.innerHTML = `
                <div class="alert alert-info p-2">
                    <small><i class="bx bx-check-circle"></i> Imagem selecionada: ${file.name}</small>
                    <div class="mt-1">
                        <img src="${e.target.result}" 
                             alt="Preview da imagem" 
                             class="img-thumbnail" style="max-width: 150px; max-height: 100px;">
                        <div><small>Tamanho: ${(file.size / 1024).toFixed(1)} KB</small></div>
                    </div>
                </div>`;
        };
        reader.readAsDataURL(file);
    } else {
        previewDiv.innerHTML = '<div class="alert alert-warning p-2">Por favor, selecione um arquivo de imagem válido.</div>';
    }
});

// Função para extrair ID do YouTube (simplificada)
function extractYouTubeId(url) {
    const regExp = /^.*(youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=|&v=)([^#&?]*).*/;
    const match = url.match(regExp);
    return (match && match[2].length === 11) ? match[2] : null;
}
</script>

<?php include('footer.php'); ?>