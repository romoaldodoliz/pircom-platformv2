<?php
session_start();
include('header.php');
include('../config/conexao.php');

$max_file_size  = 10 * 1024 * 1024; // 10MB
$allowed_types  = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];

$error_message   = '';
$success_message = '';

// ── MODO EDIÇÃO ──
$edit_id   = isset($_GET['edit']) ? intval($_GET['edit']) : 0;
$is_edit   = $edit_id > 0;
$edit_row  = null;

if ($is_edit) {
    $stmt = $conn->prepare("SELECT id, titulo, descricao, data FROM noticias WHERE id = ?");
    $stmt->bind_param("i", $edit_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $edit_row = $result->fetch_assoc();
    $stmt->close();

    if (!$edit_row) {
        $_SESSION['flash'] = ['type' => 'danger', 'text' => 'Notícia não encontrada.'];
        header('Location: noticias.php');
        exit;
    }
}

// ── PROCESSAMENTO ──
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {

    $titulo    = trim($_POST['titulo']    ?? '');
    $descricao = trim($_POST['descricao'] ?? '');
    $data      = trim($_POST['data']      ?? '');
    $pid       = intval($_POST['noticia_id'] ?? 0);

    if (empty($titulo) || empty($data)) {
        $error_message = 'Título e data são obrigatórios.';
    } else {
        $has_new_image = isset($_FILES['imagem']) && $_FILES['imagem']['error'] === UPLOAD_ERR_OK;

        // Validar imagem se enviada
        if ($has_new_image) {
            if ($_FILES['imagem']['size'] > $max_file_size) {
                $error_message = 'A imagem deve ter no máximo 10MB.';
            } elseif (!in_array($_FILES['imagem']['type'], $allowed_types)) {
                $error_message = 'Apenas imagens (JPG, PNG, GIF, WebP) são permitidas.';
            }
        }

        // Inserção: imagem obrigatória
        if (!$pid && !$has_new_image && empty($error_message)) {
            $error_message = 'Selecione uma imagem para a notícia.';
        }

        if (empty($error_message)) {
            $titulo_safe    = $conn->real_escape_string($titulo);
            $descricao_safe = $conn->real_escape_string($descricao);
            $data_safe      = $conn->real_escape_string($data);

            if ($pid > 0) {
                // ── ATUALIZAR ──
                if ($has_new_image) {
                    $imagem_bin = file_get_contents($_FILES['imagem']['tmp_name']);
                    $stmt = $conn->prepare("UPDATE noticias SET titulo=?, descricao=?, data=?, foto=? WHERE id=?");
                    $null = NULL;
                    $stmt->bind_param("sssbi", $titulo, $descricao, $data, $null, $pid);
                    $stmt->send_long_data(3, $imagem_bin);
                } else {
                    $stmt = $conn->prepare("UPDATE noticias SET titulo=?, descricao=?, data=? WHERE id=?");
                    $stmt->bind_param("sssi", $titulo, $descricao, $data, $pid);
                }

                if ($stmt->execute()) {
                    $_SESSION['flash'] = ['type' => 'success', 'text' => 'Notícia atualizada com sucesso!'];
                    $stmt->close();
                    header('Location: noticias.php');
                    exit;
                } else {
                    $error_message = 'Erro ao atualizar: ' . $stmt->error;
                }
                $stmt->close();

            } else {
                // ── INSERIR ──
                $imagem_bin = file_get_contents($_FILES['imagem']['tmp_name']);
                $stmt = $conn->prepare("INSERT INTO noticias (titulo, foto, descricao, data) VALUES (?, ?, ?, ?)");
                $null = NULL;
                $stmt->bind_param("sbss", $titulo, $null, $descricao, $data);
                $stmt->send_long_data(1, $imagem_bin);

                if ($stmt->execute()) {
                    $_SESSION['flash'] = ['type' => 'success', 'text' => 'Notícia registrada com sucesso!'];
                    $stmt->close();
                    header('Location: noticias.php');
                    exit;
                } else {
                    $error_message = 'Erro ao inserir: ' . $stmt->error;
                }
                $stmt->close();
            }
        }
    }
}

// Valores para preencher o form (POST tem prioridade para re-exibir após erro)
$f_titulo    = htmlspecialchars($_POST['titulo']    ?? $edit_row['titulo']    ?? '');
$f_descricao = htmlspecialchars($_POST['descricao'] ?? $edit_row['descricao'] ?? '');
$f_data      = htmlspecialchars($_POST['data']      ?? ($edit_row ? date('Y-m-d', strtotime($edit_row['data'])) : date('Y-m-d')));
$page_title  = $is_edit ? 'Editar Notícia' : 'Nova Notícia';
?>

<style>
@import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');

:root {
    --primary: #2563eb;
    --primary-light: rgba(37,99,235,0.08);
    --success: #16a34a;
    --success-light: rgba(22,163,74,0.08);
    --danger: #dc2626;
    --danger-light: rgba(220,38,38,0.08);
    --bg: #f4f5f7;
    --surface: #ffffff;
    --border: #e5e7eb;
    --text-primary: #111827;
    --text-secondary: #6b7280;
    --text-muted: #9ca3af;
    --radius-sm: 8px;
    --radius-md: 12px;
    --radius-lg: 16px;
    --shadow-sm: 0 1px 3px rgba(0,0,0,0.06), 0 1px 2px rgba(0,0,0,0.04);
    --shadow-md: 0 4px 16px rgba(0,0,0,0.07), 0 2px 6px rgba(0,0,0,0.04);
}

body, .content-wrapper * { font-family: 'Plus Jakarta Sans', sans-serif; }

.form-wrapper {
    padding: 1.5rem;
    background: var(--bg);
    min-height: 100vh;
}

/* ── HEADER ── */
.form-page-header {
    display: flex; align-items: center;
    justify-content: space-between; flex-wrap: wrap;
    gap: 1rem; margin-bottom: 1.75rem;
}
.form-page-header-left { display: flex; align-items: center; gap: 0.75rem; }
.form-page-icon {
    width: 44px; height: 44px;
    background: linear-gradient(135deg, var(--primary), #1e40af);
    border-radius: var(--radius-md);
    display: flex; align-items: center; justify-content: center;
    color: white; font-size: 1.25rem;
    box-shadow: 0 4px 12px rgba(37,99,235,0.35);
}
.form-page-header h1 { font-size: 1.5rem; font-weight: 800; color: var(--text-primary); margin: 0; letter-spacing: -0.02em; }
.form-page-header p  { font-size: 0.875rem; color: var(--text-muted); margin: 0; }

/* ── ALERT ── */
.form-alert {
    display: flex; align-items: center; gap: 0.75rem;
    padding: 1rem 1.25rem; border-radius: var(--radius-md);
    margin-bottom: 1.5rem; font-weight: 500; font-size: 0.9375rem;
    animation: fadeSlideDown 0.3s ease;
}
.form-alert.success { background: var(--success-light); color: var(--success); border: 1px solid rgba(22,163,74,0.2); }
.form-alert.danger  { background: var(--danger-light);  color: var(--danger);  border: 1px solid rgba(220,38,38,0.2); }
@keyframes fadeSlideDown {
    from { opacity: 0; transform: translateY(-12px); }
    to   { opacity: 1; transform: translateY(0); }
}

/* ── CARD ── */
.form-card {
    background: var(--surface);
    border-radius: var(--radius-lg);
    box-shadow: var(--shadow-sm);
    border: 1px solid var(--border);
    overflow: hidden;
}
.form-card-header {
    padding: 1.25rem 1.5rem;
    border-bottom: 1px solid var(--border);
    display: flex; align-items: center; gap: 0.5rem;
    font-size: 1rem; font-weight: 700; color: var(--text-primary);
}
.form-card-header i { color: var(--primary); }
.form-card-body { padding: 1.5rem; }

/* ── FORM CONTROLS ── */
.field-group { display: flex; flex-direction: column; gap: 0.375rem; margin-bottom: 1.25rem; }
.field-label {
    font-size: 0.8125rem; font-weight: 700;
    color: var(--text-secondary); letter-spacing: 0.02em;
}
.field-label span { color: var(--danger); margin-left: 2px; }

.field-control {
    height: 44px; padding: 0 0.875rem;
    border: 1.5px solid var(--border); border-radius: var(--radius-sm);
    font-size: 0.9rem; font-family: inherit; color: var(--text-primary);
    background: #fafafa; transition: border-color 0.2s, box-shadow 0.2s; width: 100%;
}
.field-control:focus {
    outline: none; border-color: var(--primary);
    box-shadow: 0 0 0 3px rgba(37,99,235,0.12); background: white;
}
.field-control.textarea { height: auto; padding: 0.75rem 0.875rem; resize: vertical; }
.field-hint { font-size: 0.75rem; color: var(--text-muted); margin-top: 0.2rem; }

/* ── IMAGE UPLOAD ── */
.upload-zone {
    border: 2px dashed var(--border); border-radius: var(--radius-md);
    padding: 1.5rem; text-align: center; cursor: pointer;
    transition: border-color 0.2s, background 0.2s; position: relative;
    background: #fafafa;
}
.upload-zone:hover, .upload-zone.dragover {
    border-color: var(--primary);
    background: var(--primary-light);
}
.upload-zone input[type="file"] {
    position: absolute; inset: 0; opacity: 0; cursor: pointer; width: 100%; height: 100%;
}
.upload-icon { font-size: 2rem; color: var(--primary); margin-bottom: 0.5rem; }
.upload-text { font-size: 0.9rem; font-weight: 600; color: var(--text-secondary); }
.upload-hint { font-size: 0.75rem; color: var(--text-muted); margin-top: 0.25rem; }

/* ── IMAGE PREVIEW ── */
.img-preview-wrap {
    margin-top: 1rem; border-radius: var(--radius-md);
    overflow: hidden; border: 1px solid var(--border);
    display: none; position: relative;
}
.img-preview-wrap.visible { display: block; }
.img-preview-wrap img { width: 100%; max-height: 280px; object-fit: cover; display: block; }
.img-preview-label {
    position: absolute; top: 0.5rem; left: 0.5rem;
    background: rgba(0,0,0,0.55); color: white;
    font-size: 0.7rem; font-weight: 700; padding: 0.2rem 0.5rem;
    border-radius: 4px; letter-spacing: 0.05em; text-transform: uppercase;
}
.img-preview-remove {
    position: absolute; top: 0.5rem; right: 0.5rem;
    width: 28px; height: 28px; border-radius: 50%;
    background: rgba(220,38,38,0.85); border: none; cursor: pointer;
    display: flex; align-items: center; justify-content: center;
    color: white; font-size: 0.875rem; transition: background 0.15s;
}
.img-preview-remove:hover { background: var(--danger); }

/* Current image badge */
.current-img-badge {
    display: inline-flex; align-items: center; gap: 0.4rem;
    background: var(--success-light); color: var(--success);
    border: 1px solid rgba(22,163,74,0.2);
    padding: 0.35rem 0.75rem; border-radius: 6px;
    font-size: 0.8rem; font-weight: 600; margin-bottom: 0.75rem;
}

/* ── GRID ── */
.field-row { display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem; }

/* ── BUTTONS ── */
.form-actions { display: flex; gap: 0.75rem; padding-top: 0.5rem; flex-wrap: wrap; }
.btn {
    display: inline-flex; align-items: center; justify-content: center;
    gap: 0.4rem; padding: 0 1.25rem; height: 44px;
    border-radius: var(--radius-sm); font-size: 0.9rem; font-weight: 600;
    font-family: inherit; cursor: pointer; border: none;
    transition: all 0.18s ease; white-space: nowrap; text-decoration: none;
}
.btn-primary {
    background: linear-gradient(135deg, var(--primary), #1e40af);
    color: white; box-shadow: 0 2px 8px rgba(37,99,235,0.28);
}
.btn-primary:hover { box-shadow: 0 4px 14px rgba(37,99,235,0.38); transform: translateY(-1px); color: white; }
.btn-ghost  { background: #f3f4f6; color: var(--text-secondary); border: 1.5px solid var(--border); }
.btn-ghost:hover  { background: #e5e7eb; color: var(--text-primary); }
.btn-back   { background: transparent; color: var(--text-secondary); border: 1.5px solid var(--border); }
.btn-back:hover   { background: #f3f4f6; color: var(--text-primary); }

/* ── RESPONSIVE ── */
@media (max-width: 640px) {
    .form-wrapper    { padding: 1rem; }
    .form-card-body  { padding: 1rem; }
    .field-row       { grid-template-columns: 1fr; }
    .form-page-header h1 { font-size: 1.25rem; }
}
</style>

<div class="content-wrapper">
<div class="form-wrapper">

    <!-- Header -->
    <div class="form-page-header">
        <div class="form-page-header-left">
            <div class="form-page-icon">
                <i class="bx <?php echo $is_edit ? 'bx-edit' : 'bx-plus'; ?>"></i>
            </div>
            <div>
                <h1><?php echo $page_title; ?></h1>
                <p><?php echo $is_edit ? 'Atualize os dados da notícia abaixo.' : 'Preencha os campos para publicar uma nova notícia.'; ?></p>
            </div>
        </div>
        <a href="noticias.php" class="btn btn-back">
            <i class="bx bx-arrow-back"></i> Voltar
        </a>
    </div>

    <!-- Alert -->
    <?php if ($error_message): ?>
    <div class="form-alert danger">
        <i class="bx bx-error-circle" style="font-size:1.25rem;flex-shrink:0;"></i>
        <span><?php echo htmlspecialchars($error_message); ?></span>
    </div>
    <?php endif; ?>

    <?php if ($success_message): ?>
    <div class="form-alert success">
        <i class="bx bx-check-circle" style="font-size:1.25rem;flex-shrink:0;"></i>
        <span><?php echo htmlspecialchars($success_message); ?></span>
    </div>
    <?php endif; ?>

    <div style="max-width:780px;">
        <div class="form-card">
            <div class="form-card-header">
                <i class="bx bx-news"></i>
                <?php echo $is_edit ? 'Editar dados da notícia' : 'Dados da nova notícia'; ?>
            </div>
            <div class="form-card-body">
                <form method="POST" enctype="multipart/form-data" id="noticiaForm" action="">
                    <?php if ($is_edit): ?>
                    <input type="hidden" name="noticia_id" value="<?php echo $edit_id; ?>">
                    <?php endif; ?>

                    <!-- Título -->
                    <div class="field-group">
                        <label class="field-label" for="titulo">Título <span>*</span></label>
                        <input type="text" id="titulo" name="titulo" class="field-control"
                               placeholder="Digite o título da notícia" required
                               value="<?php echo $f_titulo; ?>">
                    </div>

                    <!-- Descrição -->
                    <div class="field-group">
                        <label class="field-label" for="descricao">Descrição</label>
                        <textarea id="descricao" name="descricao" class="field-control textarea"
                                  rows="4" placeholder="Descreva a notícia..."><?php echo $f_descricao; ?></textarea>
                    </div>

                    <!-- Data -->
                    <div class="field-row">
                        <div class="field-group" style="margin-bottom:0;">
                            <label class="field-label" for="data">Data <span>*</span></label>
                            <input type="date" id="data" name="data" class="field-control" required
                                   value="<?php echo $f_data; ?>">
                        </div>
                        <div class="field-group" style="margin-bottom:0;">
                            <!-- espaço reservado para campo futuro -->
                        </div>
                    </div>

                    <!-- Imagem -->
                    <div class="field-group" style="margin-top:1.25rem;">
                        <label class="field-label" for="imagem">
                            Imagem <?php echo $is_edit ? '' : '<span>*</span>'; ?>
                        </label>

                        <?php if ($is_edit): ?>
                        <div class="current-img-badge">
                            <i class="bx bx-image-check"></i>
                            Imagem atual mantida — envie nova apenas para substituir
                        </div>

                        <!-- Pré-visualização da imagem atual (via helper) -->
                        <div class="img-preview-wrap visible" id="currentImgWrap" style="margin-bottom:0.75rem;">
                            <span class="img-preview-label">Imagem atual</span>
                            <img src="get_news_image.php?id=<?php echo $edit_id; ?>" alt="Imagem atual"
                                 onerror="document.getElementById('currentImgWrap').style.display='none'">
                        </div>
                        <?php endif; ?>

                        <div class="upload-zone" id="uploadZone">
                            <input type="file" name="imagem" id="imagem" accept="image/*"
                                   <?php echo $is_edit ? '' : 'required'; ?>>
                            <div class="upload-icon"><i class="bx bx-cloud-upload"></i></div>
                            <div class="upload-text">Clique ou arraste uma imagem aqui</div>
                            <div class="upload-hint">JPG, PNG, GIF, WebP · Máximo 10MB</div>
                        </div>

                        <!-- Preview nova imagem -->
                        <div class="img-preview-wrap" id="newImgPreview">
                            <span class="img-preview-label">Nova imagem</span>
                            <button type="button" class="img-preview-remove" id="removeImg" title="Remover">
                                <i class="bx bx-x"></i>
                            </button>
                            <img id="previewImg" src="#" alt="Preview">
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="form-actions">
                        <button type="submit" name="submit" class="btn btn-primary">
                            <i class="bx <?php echo $is_edit ? 'bx-save' : 'bx-plus'; ?>"></i>
                            <?php echo $is_edit ? 'Salvar alterações' : 'Publicar notícia'; ?>
                        </button>
                        <a href="noticias.php" class="btn btn-ghost">
                            <i class="bx bx-x"></i> Cancelar
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>
</div>

<?php include('footerprincipal.php'); ?>
<div class="content-backdrop fade"></div>

<script>
const imgInput     = document.getElementById('imagem');
const previewWrap  = document.getElementById('newImgPreview');
const previewImg   = document.getElementById('previewImg');
const removeBtn    = document.getElementById('removeImg');
const uploadZone   = document.getElementById('uploadZone');

imgInput.addEventListener('change', function() {
    const file = this.files[0];
    if (!file) return;

    if (file.size > 10 * 1024 * 1024) {
        alert('A imagem deve ter no máximo 10MB.');
        this.value = ''; return;
    }
    if (!file.type.match('image.*')) {
        alert('Selecione um arquivo de imagem válido.');
        this.value = ''; return;
    }

    const reader = new FileReader();
    reader.onload = e => {
        previewImg.src = e.target.result;
        previewWrap.classList.add('visible');
    };
    reader.readAsDataURL(file);
});

removeBtn?.addEventListener('click', function() {
    imgInput.value = '';
    previewWrap.classList.remove('visible');
    previewImg.src = '#';
});

// Drag & drop
uploadZone.addEventListener('dragover',  e => { e.preventDefault(); uploadZone.classList.add('dragover'); });
uploadZone.addEventListener('dragleave', () => uploadZone.classList.remove('dragover'));
uploadZone.addEventListener('drop', e => {
    e.preventDefault();
    uploadZone.classList.remove('dragover');
    if (e.dataTransfer.files.length) {
        imgInput.files = e.dataTransfer.files;
        imgInput.dispatchEvent(new Event('change'));
    }
});
</script>

<?php
if (isset($conn) && $conn->ping()) $conn->close();
include('footer.php');
?>