<?php
// ══════════════════════════════════════════════
// TODA A LÓGICA PHP ANTES DE QUALQUER OUTPUT
// (header.php já envia HTML — redirecionar aqui)
// ══════════════════════════════════════════════
session_start();
include('config/conexao.php');

// ── MODO EDIÇÃO ──
$edit_id  = isset($_GET['edit']) ? intval($_GET['edit']) : 0;
$is_edit  = $edit_id > 0;
$edit_row = null;

if ($is_edit) {
    $stmt = $conn->prepare("SELECT id, descricao, data FROM homepagehero WHERE id = ?");
    $stmt->bind_param("i", $edit_id);
    $stmt->execute();
    $result   = $stmt->get_result();
    $edit_row = $result->fetch_assoc();
    $stmt->close();

    if (!$edit_row) {
        $_SESSION['flash'] = ['type' => 'danger', 'text' => 'Registo não encontrado.'];
        header('Location: homepagehero.php');
        exit;
    }
}

$error_message   = '';
$success_message = '';

// ── PROCESSAMENTO ──
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {

    $descricao = trim($_POST['descricao'] ?? '');
    $data      = trim($_POST['data']      ?? '');
    $pid       = intval($_POST['hero_id'] ?? 0);

    if (empty($data)) {
        $error_message = 'A data é obrigatória.';
    } else {
        $has_image = isset($_FILES['imagem']) && $_FILES['imagem']['error'] === UPLOAD_ERR_OK;

        // Validar imagem se enviada
        if ($has_image) {
            if (strpos($_FILES['imagem']['type'], 'image') === false) {
                $error_message = 'Apenas imagens são permitidas.';
                $has_image     = false;
            } elseif ($_FILES['imagem']['size'] > 10 * 1024 * 1024) {
                $error_message = 'A imagem deve ter no máximo 10MB.';
                $has_image     = false;
            }
        }

        // INSERT exige imagem; UPDATE não
        if (!$pid && !$has_image && empty($error_message)) {
            $error_message = 'Selecione uma imagem.';
        }

        if (empty($error_message)) {

            if ($pid > 0) {
                // ── ATUALIZAR ──
                if ($has_image) {
                    $imagem_bin = file_get_contents($_FILES['imagem']['tmp_name']);
                    $stmt = $conn->prepare("UPDATE homepagehero SET foto=?, descricao=?, data=? WHERE id=?");
                    $null = NULL;
                    $stmt->bind_param("bssi", $null, $descricao, $data, $pid);
                    $stmt->send_long_data(0, $imagem_bin);
                } else {
                    $stmt = $conn->prepare("UPDATE homepagehero SET descricao=?, data=? WHERE id=?");
                    $stmt->bind_param("ssi", $descricao, $data, $pid);
                }

                if ($stmt->execute()) {
                    $_SESSION['flash'] = ['type' => 'success', 'text' => 'Imagem atualizada com sucesso!'];
                    $stmt->close();
                    header('Location: homepagehero.php');
                    exit;
                } else {
                    $error_message = 'Erro ao atualizar: ' . $stmt->error;
                }
                $stmt->close();

            } else {
                // ── INSERIR ──
                $imagem_bin = file_get_contents($_FILES['imagem']['tmp_name']);
                $stmt = $conn->prepare("INSERT INTO homepagehero (foto, descricao, data) VALUES (?, ?, ?)");
                $null = NULL;
                $stmt->bind_param("bss", $null, $descricao, $data);
                $stmt->send_long_data(0, $imagem_bin);

                if ($stmt->execute()) {
                    $_SESSION['flash'] = ['type' => 'success', 'text' => 'Imagem adicionada com sucesso!'];
                    $stmt->close();
                    header('Location: homepagehero.php');
                    exit;
                } else {
                    $error_message = 'Erro na inserção: ' . $stmt->error;
                }
                $stmt->close();
            }
        }
    }
}

// Valores para preencher o form
$f_descricao = htmlspecialchars($_POST['descricao'] ?? $edit_row['descricao'] ?? '');
$f_data      = htmlspecialchars($_POST['data']      ?? ($edit_row ? date('Y-m-d', strtotime($edit_row['data'])) : date('Y-m-d')));
$page_title  = $is_edit ? 'Editar Imagem' : 'Adicionar Imagem à Página Principal';

// Só agora o header.php pode enviar output — todos os redirects já foram feitos acima
include('header.php');
?>

<!-- Content wrapper -->
<div class="content-wrapper">
    <div class="container-xxl flex-grow-1 container-p-y">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold py-3 mb-0"><?php echo $page_title; ?></h4>
            <a href="homepagehero.php" class="btn btn-outline-secondary">
                <i class="bx bx-arrow-back me-1"></i> Voltar
            </a>
        </div>

        <?php if ($error_message): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bx bx-error-circle me-2"></i>
            <?php echo htmlspecialchars($error_message); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>

        <?php if ($success_message): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bx bx-check-circle me-2"></i>
            <?php echo htmlspecialchars($success_message); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>

        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="bx <?php echo $is_edit ? 'bx-edit' : 'bx-image-add'; ?> me-2"></i>
                            <?php echo $is_edit ? 'Editar dados do slide' : 'Novo slide'; ?>
                        </h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" enctype="multipart/form-data" action="">

                            <?php if ($is_edit): ?>
                            <input type="hidden" name="hero_id" value="<?php echo $edit_id; ?>">
                            <?php endif; ?>

                            <div class="row g-3">

                                <!-- Descrição -->
                                <div class="col-12">
                                    <label class="form-label">Descrição</label>
                                    <textarea class="form-control" name="descricao" rows="3"
                                              placeholder="Descrição do slide..."><?php echo $f_descricao; ?></textarea>
                                </div>

                                <!-- Data -->
                                <div class="col-md-6">
                                    <label class="form-label">Data <span class="text-danger">*</span></label>
                                    <input type="date" name="data" class="form-control" required
                                           value="<?php echo $f_data; ?>">
                                </div>

                                <!-- Imagem -->
                                <div class="col-12">
                                    <label class="form-label">
                                        Imagem <?php echo $is_edit ? '<small class="text-muted">(deixe vazio para manter a atual)</small>' : '<span class="text-danger">*</span>'; ?>
                                    </label>

                                    <?php if ($is_edit): ?>
                                    <!-- Preview da imagem atual -->
                                    <div class="mb-2 p-2 border rounded bg-light d-flex align-items-center gap-2">
                                        <img src="get_hero_image.php?id=<?php echo $edit_id; ?>"
                                             alt="Imagem atual"
                                             style="height:70px;width:120px;object-fit:cover;border-radius:6px;"
                                             onerror="this.parentElement.style.display='none'">
                                        <div>
                                            <span class="badge bg-success"><i class="bx bx-image-check me-1"></i>Imagem atual</span>
                                            <p class="mb-0 mt-1 small text-muted">Envie uma nova imagem apenas para substituir.</p>
                                        </div>
                                    </div>
                                    <?php endif; ?>

                                    <input type="file" name="imagem" id="imagem" class="form-control"
                                           accept="image/*" <?php echo $is_edit ? '' : 'required'; ?>>
                                    <div class="form-text">JPG, PNG, GIF, WebP · Máximo 10MB</div>

                                    <!-- Preview nova imagem -->
                                    <div id="newImgPreview" class="mt-2" style="display:none;">
                                        <p class="small text-muted mb-1">Nova imagem:</p>
                                        <img id="previewImg" src="#" alt="Preview"
                                             style="max-height:160px;border-radius:8px;border:1px solid #dee2e6;">
                                    </div>
                                </div>

                                <!-- Botões -->
                                <div class="col-12 d-flex gap-2 pt-2">
                                    <button class="btn btn-dark" name="submit" type="submit">
                                        <i class="bx <?php echo $is_edit ? 'bx-save' : 'bx-plus'; ?> me-1"></i>
                                        <?php echo $is_edit ? 'Guardar alterações' : 'Submeter'; ?>
                                    </button>
                                    <a href="homepagehero.php" class="btn btn-outline-secondary">
                                        <i class="bx bx-x me-1"></i> Cancelar
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include('footerprincipal.php'); ?>
    <div class="content-backdrop fade"></div>
</div>

<script>
document.getElementById('imagem').addEventListener('change', function () {
    const file = this.files[0];
    if (!file) return;

    if (file.size > 10 * 1024 * 1024) {
        alert('A imagem deve ter no máximo 10MB.');
        this.value = '';
        document.getElementById('newImgPreview').style.display = 'none';
        return;
    }
    if (!file.type.match('image.*')) {
        alert('Selecione um arquivo de imagem válido.');
        this.value = '';
        return;
    }

    const reader = new FileReader();
    reader.onload = e => {
        document.getElementById('previewImg').src = e.target.result;
        document.getElementById('newImgPreview').style.display = 'block';
    };
    reader.readAsDataURL(file);
});
</script>

<?php
if (isset($conn) && $conn->ping()) $conn->close();
include('footer.php');
?>