<?php
include('header.php');
include('config/conexao.php');

// Configurações de upload
$max_file_size = 10 * 1024 * 1024; // 10MB
$allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];

$error_message = '';
$success_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    // Validação CSRF (recomendado adicionar)
    
    // Sanitizar inputs
    $titulo = $conn->real_escape_string(trim($_POST["titulo"] ?? ''));
    $descricao = $conn->real_escape_string(trim($_POST["descricao"] ?? ''));
    $data = $conn->real_escape_string($_POST["data"] ?? '');
    
    // Validações básicas
    if (empty($titulo) || empty($data)) {
        $error_message = 'Título e data são obrigatórios.';
    } elseif (!isset($_FILES['imagem']) || $_FILES['imagem']['error'] !== UPLOAD_ERR_OK) {
        $error_message = 'Erro no envio da imagem.';
    } else {
        // Validar tamanho do arquivo
        if ($_FILES['imagem']['size'] > $max_file_size) {
            $error_message = 'A imagem deve ter no máximo 10MB.';
        } 
        // Validar tipo do arquivo
        elseif (!in_array($_FILES['imagem']['type'], $allowed_types)) {
            $error_message = 'Apenas imagens (JPG, PNG, GIF, WebP) são permitidas.';
        } else {
            // Processar imagem
            $tmp_name = $_FILES['imagem']['tmp_name'];
            
            // Otimizar imagem antes de salvar (opcional)
            $image_info = getimagesize($tmp_name);
            $imagem_bin = file_get_contents($tmp_name);
            
            // Inserir no banco de dados usando prepared statement
            $stmt = $conn->prepare("INSERT INTO noticias (titulo, foto, descricao, data) VALUES (?, ?, ?, ?)");
            $null = NULL;
            $stmt->bind_param("sbss", $titulo, $null, $descricao, $data);
            $stmt->send_long_data(1, $imagem_bin);
            
            if ($stmt->execute()) {
                $success_message = 'Notícia registrada com sucesso!';
                echo "<script>
                    setTimeout(function() {
                        window.location.href = 'noticias.php';
                    }, 1500);
                </script>";
            } else {
                $error_message = 'Erro na inserção: ' . $stmt->error;
            }
            $stmt->close();
        }
    }
}
?>

<!-- Content wrapper -->
<div class="content-wrapper">
    <!-- Content -->
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold py-3 mb-0">Adicionar Notícia</h4>
            <a href="noticias.php" class="btn btn-outline-secondary">
                <i class="bx bx-arrow-back me-1"></i>Voltar
            </a>
        </div>

        <!-- Mensagens -->
        <?php if ($success_message): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bx bx-check-circle me-2"></i>
                <?= htmlspecialchars($success_message) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php elseif ($error_message): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bx bx-error-circle me-2"></i>
                <?= htmlspecialchars($error_message) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="row justify-content-center">
            <div class="col-12 col-md-10 col-lg-8">
                <div class="card">
                    <div class="card-body">
                        <form action="<?= htmlspecialchars($_SERVER["PHP_SELF"]) ?>" method="POST" enctype="multipart/form-data" id="noticiaForm">
                            <div class="row g-3">
                                <div class="col-12">
                                    <label for="titulo" class="form-label">Título *</label>
                                    <input type="text" name="titulo" id="titulo" class="form-control" 
                                           placeholder="Digite o título da notícia" required 
                                           value="<?= htmlspecialchars($_POST['titulo'] ?? '') ?>">
                                </div>
                                
                                <div class="col-12">
                                    <label for="descricao" class="form-label">Descrição</label>
                                    <textarea class="form-control" name="descricao" id="descricao" 
                                              rows="4" placeholder="Digite a descrição da notícia"><?= htmlspecialchars($_POST['descricao'] ?? '') ?></textarea>
                                </div>
                                
                                <div class="col-12 col-md-6">
                                    <label for="data" class="form-label">Data *</label>
                                    <input type="date" name="data" id="data" class="form-control" required
                                           value="<?= htmlspecialchars($_POST['data'] ?? date('Y-m-d')) ?>">
                                </div>
                                
                                <div class="col-12 col-md-6">
                                    <label for="imagem" class="form-label">Imagem *</label>
                                    <div class="input-group">
                                        <input type="file" name="imagem" id="imagem" 
                                               class="form-control" accept="image/*" required>
                                        <button type="button" class="btn btn-outline-secondary" onclick="document.getElementById('imagem').value=''">
                                            <i class="bx bx-x"></i>
                                        </button>
                                    </div>
                                    <div class="form-text">
                                        Formatos: JPG, PNG, GIF, WebP. Máximo: 10MB
                                    </div>
                                </div>
                                
                                <!-- Preview da imagem -->
                                <div class="col-12">
                                    <div class="image-preview-container mt-2 d-none">
                                        <label class="form-label">Pré-visualização:</label>
                                        <div class="border rounded p-3 text-center">
                                            <img id="imagePreview" src="#" alt="Preview" 
                                                 class="img-fluid rounded max-h-300" style="max-height: 300px; display: none;">
                                            <p id="noPreview" class="text-muted mb-0">
                                                Nenhuma imagem selecionada
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-12 mt-4">
                                    <div class="d-flex gap-2">
                                        <button class="btn btn-dark" name="submit" type="submit">
                                            <i class="bx bx-save me-1"></i>Salvar Notícia
                                        </button>
                                        <button type="reset" class="btn btn-outline-secondary">
                                            <i class="bx bx-reset me-1"></i>Limpar
                                        </button>
                                    </div>
                                </div>
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

<style>
    /* Estilos responsivos */
    .max-h-300 {
        max-height: 300px;
        object-fit: contain;
    }
    
    @media (max-width: 576px) {
        .max-h-300 {
            max-height: 200px;
        }
    }
    
    /* Validação visual */
    .form-control:invalid {
        border-color: #dc3545;
    }
    
    .form-control:valid {
        border-color: #198754;
    }
</style>

<script>
    // Preview da imagem
    document.getElementById('imagem').addEventListener('change', function(e) {
        const previewContainer = document.querySelector('.image-preview-container');
        const preview = document.getElementById('imagePreview');
        const noPreview = document.getElementById('noPreview');
        const file = e.target.files[0];
        
        if (file) {
            // Verificar tamanho (10MB)
            if (file.size > 10 * 1024 * 1024) {
                alert('A imagem deve ter no máximo 10MB.');
                this.value = '';
                return;
            }
            
            // Verificar tipo
            if (!file.type.match('image.*')) {
                alert('Por favor, selecione uma imagem.');
                this.value = '';
                return;
            }
            
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.style.display = 'block';
                noPreview.style.display = 'none';
                previewContainer.classList.remove('d-none');
            }
            reader.readAsDataURL(file);
        } else {
            preview.style.display = 'none';
            noPreview.style.display = 'block';
        }
    });
    
    // Validação do formulário
    document.getElementById('noticiaForm').addEventListener('submit', function(e) {
        const imagem = document.getElementById('imagem');
        const data = document.getElementById('data');
        const titulo = document.getElementById('titulo');
        
        if (!titulo.value.trim()) {
            e.preventDefault();
            titulo.focus();
            alert('Por favor, preencha o título.');
            return false;
        }
        
        if (!data.value) {
            e.preventDefault();
            data.focus();
            alert('Por favor, selecione uma data.');
            return false;
        }
        
        if (!imagem.files.length) {
            e.preventDefault();
            imagem.focus();
            alert('Por favor, selecione uma imagem.');
            return false;
        }
        
        return true;
    });
</script>

<?php
// Fechar conexão
if (isset($conn) && $conn->ping()) {
    $conn->close();
}
include('footer.php');
?>