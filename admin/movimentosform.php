<?php
/**
 * Pircom/admin/movimentosform.php
 *
 * Criar / Editar movimento + galeria de fotos
 * Os ficheiros são guardados em: Pircom/uploads/movimentos/
 */

// Processar ANTES do header (podem ocorrer redirects)
include('config/conexao.php');
require_once('helpers/upload.php');

$message      = '';
$message_type = '';
$editing      = false;
$movimento    = null;
$uploader     = new ImageUploader();

// ── Detectar nome da tabela de fotos ─────────────────────────────────────
function detectFotosTable(mysqli $conn): string {
    foreach (['movimentos_fotos', 'movimento_fotos'] as $t) {
        $r = $conn->query("SHOW TABLES LIKE '{$t}'");
        if ($r && $r->num_rows > 0) return $t;
    }
    return 'movimentos_fotos';
}
$fotos_table = detectFotosTable($conn);

// ── Modo edição ───────────────────────────────────────────────────────────
if (isset($_GET['id'])) {
    $editing      = true;
    $movimento_id = intval($_GET['id']);
    $st = $conn->prepare("SELECT * FROM movimentos WHERE id = ?");
    $st->bind_param("i", $movimento_id);
    $st->execute();
    $movimento = $st->get_result()->fetch_assoc();
    $st->close();
    if (!$movimento) { header('Location: movimentos.php'); exit; }
}

// ── Guardar / Actualizar movimento ───────────────────────────────────────
if (isset($_POST['submit'])) {
    $titulo      = trim($_POST['titulo']      ?? '');
    $tema        = trim($_POST['tema']        ?? '');
    $descricao   = trim($_POST['descricao']   ?? '');
    $local       = trim($_POST['local']       ?? '');
    $status      = $_POST['status']           ?? 'publicado';
    $data_evento = !empty($_POST['data_evento']) ? $_POST['data_evento'] : null;

    $imagem_principal = $editing ? ($movimento['imagem_principal'] ?? null) : null;

    // Upload da nova imagem principal
    if (!empty($_FILES['imagem_principal']['name'])) {
        $up = $uploader->uploadImage($_FILES['imagem_principal'], 'movimentos', 1200, 800);
        if ($up['success']) {
            // Apagar ficheiro antigo antes de substituir
            if ($imagem_principal) $uploader->deleteImage($imagem_principal);
            $imagem_principal = $up['path'];
            $_SESSION['toast'] = ['type' => 'success', 'message' => 'Imagem principal atualizada com sucesso.'];
        } else {
            $message      = 'Erro no upload da imagem: ' . $up['message'];
            $message_type = 'error';
        }
    }

    if (empty($message)) {
        if ($editing) {
            $st = $conn->prepare("UPDATE movimentos SET titulo=?,tema=?,descricao=?,data_evento=?,local=?,imagem_principal=?,status=? WHERE id=?");
            $st->bind_param("sssssssi", $titulo,$tema,$descricao,$data_evento,$local,$imagem_principal,$status,$movimento_id);
            if ($st->execute()) {
                $_SESSION['toast'] = [
                    'type' => 'success',
                    'message' => 'Movimento atualizado com sucesso.',
                    'description' => 'As alterações foram guardadas permanentemente.'
                ];
                header("Location: movimentosform.php?id={$movimento_id}&updated=1");
                exit;
            } else {
                $message = 'Erro ao actualizar: '.$conn->error;
                $message_type = 'error';
            }
            $st->close();
        } else {
            $st = $conn->prepare("INSERT INTO movimentos (titulo,tema,descricao,data_evento,local,imagem_principal,status) VALUES (?,?,?,?,?,?,?)");
            $st->bind_param("sssssss", $titulo,$tema,$descricao,$data_evento,$local,$imagem_principal,$status);
            if ($st->execute()) {
                $_SESSION['toast'] = [
                    'type' => 'success',
                    'message' => 'Movimento criado com sucesso.',
                    'description' => 'Já pode adicionar imagens à galeria.'
                ];
                header("Location: movimentosform.php?id={$conn->insert_id}&created=1");
                exit;
            } else {
                $message = 'Erro ao criar: '.$conn->error;
                $message_type = 'error';
            }
            $st->close();
        }
    }
}

// ── Upload de fotos da galeria ────────────────────────────────────────────
if (isset($_POST['upload_fotos']) && $editing) {
    if (!empty($_FILES['fotos']['name'][0])) {
        $ok = 0; $err = 0; $uploaded_files = [];
        for ($i = 0, $n = count($_FILES['fotos']['name']); $i < $n; $i++) {
            if ($_FILES['fotos']['error'][$i] !== UPLOAD_ERR_OK) continue;
            $file = [
                'name'     => $_FILES['fotos']['name'][$i],
                'type'     => $_FILES['fotos']['type'][$i],
                'tmp_name' => $_FILES['fotos']['tmp_name'][$i],
                'error'    => $_FILES['fotos']['error'][$i],
                'size'     => $_FILES['fotos']['size'][$i],
            ];
            $up = $uploader->uploadImage($file, 'movimentos', 1200, 1200);
            if ($up['success']) {
                $p  = $up['path'];
                $st = $conn->prepare("INSERT INTO {$fotos_table} (movimento_id, foto) VALUES (?,?)");
                $st->bind_param("is", $movimento_id, $p);
                if ($st->execute()) {
                    $ok++;
                    $uploaded_files[] = $file['name'];
                } else {
                    $err++;
                }
                $st->close();
            } else {
                $err++;
            }
        }
        
        if ($ok > 0) {
            $_SESSION['toast'] = [
                'type' => 'success',
                'message' => "{$ok} foto(s) adicionada(s) com sucesso.",
                'description' => $err > 0 ? "{$err} ficheiro(s) não foram processados." : null
            ];
        } else {
            $_SESSION['toast'] = [
                'type' => 'error',
                'message' => 'Nenhuma foto foi adicionada.',
                'description' => 'Verifique os formatos e tamanhos dos ficheiros.'
            ];
        }
        header("Location: movimentosform.php?id={$movimento_id}&upload=1");
        exit;
    } else {
        $_SESSION['toast'] = [
            'type' => 'warning',
            'message' => 'Nenhum ficheiro selecionado.',
            'description' => 'Selecione pelo menos uma imagem para fazer upload.'
        ];
        header("Location: movimentosform.php?id={$movimento_id}");
        exit;
    }
}

// ── Remover foto da galeria ───────────────────────────────────────────────
if (isset($_POST['remover_foto']) && $editing) {
    $fid = intval($_POST['foto_id']);
    $st  = $conn->prepare("SELECT foto FROM {$fotos_table} WHERE id=? AND movimento_id=?");
    $st->bind_param("ii", $fid, $movimento_id);
    $st->execute();
    $foto = $st->get_result()->fetch_assoc();
    $st->close();
    if ($foto) {
        $uploader->deleteImage($foto['foto']);
        $st = $conn->prepare("DELETE FROM {$fotos_table} WHERE id=?");
        $st->bind_param("i", $fid);
        $st->execute();
        $st->close();
        $_SESSION['toast'] = [
            'type' => 'success',
            'message' => 'Foto removida permanentemente.',
            'description' => 'A imagem foi apagada do servidor.'
        ];
    }
    header("Location: movimentosform.php?id={$movimento_id}&removed=1");
    exit;
}

// Header APÓS todo o processamento
include('header.php');

// Toast messages from session
if (isset($_SESSION['toast'])) {
    $message = $_SESSION['toast']['message'];
    $message_type = $_SESSION['toast']['type'];
    $description = $_SESSION['toast']['description'] ?? '';
    unset($_SESSION['toast']);
}

// Specific creation message
if (isset($_GET['created'])) {
    $message = 'Movimento criado com sucesso.';
    $description = 'Já pode adicionar fotos à galeria.';
    $message_type = 'success';
}

// Specific update message
if (isset($_GET['updated'])) {
    $message = 'Movimento atualizado com sucesso.';
    $description = 'As alterações foram guardadas.';
    $message_type = 'success';
}

// Specific upload message
if (isset($_GET['upload'])) {
    // Message already handled in session
}
?>

<style>
@import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');
:root {
    --primary: #FF6F0F; --pl: rgba(255,111,15,.08); --pm: rgba(255,111,15,.15);
    --success: #16a34a; --success-light: rgba(22,163,74,.08);
    --warning: #d97706; --warning-light: rgba(217,119,6,.08);
    --error: #dc2626; --error-light: rgba(220,38,38,.08);
    --info: #2563eb; --info-light: rgba(37,99,235,.08);
    --bg: #f4f5f7; --surface: #fff; --border: #e5e7eb;
    --text-primary: #111827; --text-secondary: #6b7280; --text-tertiary: #9ca3af;
    --radius-sm: 8px; --radius-md: 12px; --radius-lg: 16px;
    --shadow-sm: 0 1px 3px rgba(0,0,0,.06), 0 1px 2px rgba(0,0,0,.04);
    --shadow-md: 0 4px 16px rgba(0,0,0,.07), 0 2px 6px rgba(0,0,0,.04);
    --shadow-lg: 0 10px 25px -5px rgba(0,0,0,.1), 0 8px 10px -6px rgba(0,0,0,.02);
    --toast-bg: #fff;
}

* { font-family: 'Plus Jakarta Sans', sans-serif; box-sizing: border-box; }

.pw { padding: 1.5rem; background: var(--bg); min-height: 100vh; }

/* Toast/Alert styles */
.toast-container {
    position: fixed;
    top: 2rem;
    right: 2rem;
    z-index: 9999;
    max-width: 420px;
    animation: slideIn 0.3s ease;
}

.toast {
    background: var(--toast-bg);
    border-radius: var(--radius-lg);
    box-shadow: var(--shadow-lg);
    padding: 1.25rem;
    margin-bottom: 1rem;
    border-left: 4px solid;
    display: flex;
    align-items: flex-start;
    gap: 1rem;
    backdrop-filter: blur(8px);
    background: rgba(255,255,255,0.98);
    border: 1px solid rgba(0,0,0,0.05);
    min-width: 360px;
    pointer-events: auto;
}

.toast.success { border-left-color: var(--success); }
.toast.error { border-left-color: var(--error); }
.toast.warning { border-left-color: var(--warning); }
.toast.info { border-left-color: var(--info); }

.toast-icon {
    width: 24px;
    height: 24px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    font-size: 1.25rem;
}

.toast.success .toast-icon { color: var(--success); }
.toast.error .toast-icon { color: var(--error); }
.toast.warning .toast-icon { color: var(--warning); }
.toast.info .toast-icon { color: var(--info); }

.toast-content {
    flex: 1;
}

.toast-title {
    font-weight: 700;
    font-size: 0.9375rem;
    color: var(--text-primary);
    margin-bottom: 0.25rem;
}

.toast-description {
    font-size: 0.8125rem;
    color: var(--text-secondary);
    line-height: 1.5;
}

.toast-close {
    background: none;
    border: none;
    color: var(--text-tertiary);
    cursor: pointer;
    padding: 0.25rem;
    font-size: 1.125rem;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 4px;
    transition: all 0.2s;
}

.toast-close:hover {
    color: var(--text-primary);
    background: var(--bg);
}

@keyframes slideIn {
    from { transform: translateX(100%); opacity: 0; }
    to { transform: translateX(0); opacity: 1; }
}

/* Header styles */
.phd { display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:1rem; margin-bottom:1.75rem; }
.phl { display:flex; align-items:center; gap:.75rem; }
.phi { width:44px; height:44px; background:linear-gradient(135deg,var(--primary),#ff8c34); border-radius:var(--radius-md); display:flex; align-items:center; justify-content:center; color:#fff; font-size:1.25rem; box-shadow:0 4px 12px rgba(255,111,15,.35); }
.pbc { font-size:.8rem; color:var(--text-tertiary); font-weight:500; margin-bottom:.1rem; }
.phd h1 { font-size:1.375rem; font-weight:800; color:var(--text-primary); margin:0; letter-spacing:-.02em; }

/* Card styles */
.pcard { background:var(--surface); border-radius:var(--radius-lg); box-shadow:var(--shadow-sm); border:1px solid var(--border); overflow:hidden; margin-bottom:1.5rem; }
.pch { padding:1.25rem 1.5rem; border-bottom:1px solid var(--border); background:#fafafa; display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:.75rem; }
.pct { font-size:1rem; font-weight:700; color:var(--text-primary); display:flex; align-items:center; gap:.5rem; margin:0; }
.pct i { color:var(--primary); }
.pcb { padding:1.5rem; }

/* Form grid */
.pfg { display:grid; grid-template-columns:1fr 1fr 1fr 180px; gap:1.25rem; }
.pfg-g { display:flex; flex-direction:column; gap:.35rem; }
.s2 { grid-column:span 2; } .s3 { grid-column:span 3; } .sf { grid-column:1/-1; }
.plbl { font-size:.8125rem; font-weight:700; color:var(--text-secondary); }
.plbl .req { color:var(--error); margin-left:2px; }
.pinp, .psel, .ptxt {
    padding:.625rem .875rem; border:1.5px solid var(--border); border-radius:var(--radius-sm);
    font-size:.9rem; font-family:inherit; color:var(--text-primary); background:#fafafa;
    transition:border-color .2s, box-shadow .2s; width:100%;
}
.pinp:focus, .psel:focus, .ptxt:focus {
    outline:none; border-color:var(--primary);
    box-shadow:0 0 0 3px rgba(255,111,15,.12); background:#fff;
}
.ptxt { resize:vertical; min-height:140px; }

/* Upload zone */
.puz { border:2px dashed var(--border); border-radius:var(--radius-md); padding:1.5rem; text-align:center; cursor:pointer; transition:all .2s; background:#fafafa; position:relative; }
.puz:hover, .puz.on { border-color:var(--primary); background:var(--pl); }
.puz input[type=file] { position:absolute; inset:0; opacity:0; cursor:pointer; width:100%; height:100%; }
.puzi { font-size:2rem; color:var(--text-tertiary); margin-bottom:.5rem; display:block; }
.puzt { font-size:.875rem; font-weight:600; color:var(--text-secondary); }
.puzh { font-size:.75rem; color:var(--text-tertiary); margin-top:.25rem; }

/* Current image */
.pci { margin-bottom:.75rem; position:relative; display:inline-block; }
.pci img { max-width:300px; height:170px; object-fit:cover; border-radius:var(--radius-md); border:1px solid var(--border); box-shadow:var(--shadow-sm); display:block; }
.pcib { position:absolute; bottom:.5rem; left:.5rem; background:rgba(0,0,0,.65); color:#fff; font-size:.7rem; font-weight:600; padding:.2rem .5rem; border-radius:4px; }

/* New image preview */
.pnp { margin-top:.75rem; display:none; }
.pnp img { max-width:240px; height:140px; object-fit:cover; border-radius:var(--radius-md); border:1px solid var(--border); }
.pnp p { font-size:.75rem; color:var(--text-tertiary); margin:.25rem 0 0; }

/* Gallery upload zone */
.pguz { border:2px dashed var(--border); border-radius:var(--radius-md); padding:2rem 1.5rem; text-align:center; cursor:pointer; transition:all .2s; background:#fafafa; position:relative; margin-bottom:1rem; }
.pguz:hover, .pguz.on { border-color:var(--primary); background:var(--pl); }
.pguz input[type=file] { position:absolute; inset:0; opacity:0; cursor:pointer; width:100%; height:100%; }
.pguzi { font-size:2.5rem; color:var(--text-tertiary); margin-bottom:.75rem; display:block; }
.pguzt { font-size:1rem; font-weight:700; color:var(--text-secondary); }
.pguzh { font-size:.8rem; color:var(--text-tertiary); margin-top:.35rem; }
.ppill { display:none; margin-top:.75rem; background:var(--pl); color:var(--primary); border:1px solid var(--pm); padding:.3rem 1rem; border-radius:999px; font-size:.8rem; font-weight:700; align-items:center; gap:.4rem; }

/* Gallery grid */
.pgrd { display:grid; grid-template-columns:repeat(auto-fill, minmax(155px,1fr)); gap:1rem; }
.pgi { position:relative; border-radius:var(--radius-md); overflow:hidden; aspect-ratio:1; box-shadow:var(--shadow-sm); border:1px solid var(--border); transition:transform .2s, box-shadow .2s; background:#f3f4f6; }
.pgi:hover { transform:scale(1.02); box-shadow:var(--shadow-md); }
.pgi img { width:100%; height:100%; object-fit:cover; display:block; }
.pgio { position:absolute; inset:0; background:rgba(0,0,0,0); display:flex; align-items:center; justify-content:center; transition:background .2s; }
.pgi:hover .pgio { background:rgba(0,0,0,.45); }
.pbrm { opacity:0; transform:scale(.8); transition:all .2s; background:var(--error); color:#fff; border:none; border-radius:50%; width:40px; height:40px; display:flex; align-items:center; justify-content:center; font-size:1.125rem; cursor:pointer; }
.pgi:hover .pbrm { opacity:1; transform:scale(1); }
.pge { padding:2.5rem; text-align:center; color:var(--text-tertiary); font-size:.9rem; }
.pge i { font-size:2.5rem; display:block; margin-bottom:.5rem; }

/* Buttons */
.btn { display:inline-flex; align-items:center; justify-content:center; gap:.4rem; border:none; border-radius:var(--radius-sm); font-family:inherit; font-weight:700; cursor:pointer; transition:all .18s; text-decoration:none; white-space:nowrap; }
.pbp { background:linear-gradient(135deg,var(--primary),#ff8c34); color:#fff; padding:0 1.5rem; height:44px; font-size:.9375rem; box-shadow:0 2px 10px rgba(255,111,15,.28); }
.pbp:hover { color:#fff; transform:translateY(-1px); box-shadow:0 4px 16px rgba(255,111,15,.4); }
.pbg { background:#fff; color:var(--text-secondary); border:1.5px solid var(--border); padding:0 1.25rem; height:44px; font-size:.9rem; }
.pbg:hover { background:#f3f4f6; color:var(--text-primary); }
.pbu { background:var(--success-light); color:var(--success); border:1.5px solid rgba(22,163,74,.3); padding:0 1.25rem; height:40px; font-size:.875rem; }
.pbu:hover { background:var(--success); color:#fff; border-color:var(--success); }
.pfa { display:flex; align-items:center; gap:.75rem; padding-top:1.25rem; border-top:1px solid var(--border); margin-top:.5rem; flex-wrap:wrap; }

/* Responsive */
@media (max-width:991px) { .pfg { grid-template-columns:1fr 1fr; } .s3 { grid-column:span 2; } }
@media (max-width:767px) { .pw { padding:1rem; } .pfg { grid-template-columns:1fr; } .s2,.s3,.sf { grid-column:1; } .pgrd { grid-template-columns:repeat(auto-fill,minmax(130px,1fr)); } .pbp,.pbg { flex:1; } .toast-container { left: 1rem; right: 1rem; max-width: none; } .toast { min-width: auto; } }
@media (max-width:575px) { .pw { padding:.75rem; } .pcb { padding:1rem; } .pgrd { grid-template-columns:repeat(2,1fr); } }
</style>

<div class="content-wrapper">
<div class="pw">

<!-- Toast/Alert Container -->
<?php if ($message): ?>
<div class="toast-container" id="toastContainer">
    <div class="toast <?php echo $message_type; ?>">
        <div class="toast-icon">
            <?php if ($message_type === 'success'): ?>
                <i class="bx bx-check-circle"></i>
            <?php elseif ($message_type === 'error'): ?>
                <i class="bx bx-error-circle"></i>
            <?php elseif ($message_type === 'warning'): ?>
                <i class="bx bx-info-circle"></i>
            <?php else: ?>
                <i class="bx bx-info-circle"></i>
            <?php endif; ?>
        </div>
        <div class="toast-content">
            <div class="toast-title"><?php echo htmlspecialchars($message); ?></div>
            <?php if (!empty($description)): ?>
                <div class="toast-description"><?php echo htmlspecialchars($description); ?></div>
            <?php endif; ?>
        </div>
        <button class="toast-close" onclick="this.closest('.toast').remove()">
            <i class="bx bx-x"></i>
        </button>
    </div>
</div>

<script>
setTimeout(function() {
    const toast = document.getElementById('toastContainer');
    if (toast) {
        toast.style.animation = 'slideIn 0.3s ease reverse';
        setTimeout(() => toast.remove(), 300);
    }
}, 5000);
</script>
<?php endif; ?>

<!-- ── HEADER ──────────────────────────────────────────────────────────── -->
<div class="phd">
    <div class="phl">
        <div class="phi"><i class="bx bx-news"></i></div>
        <div>
            <div class="pbc">Movimentos /</div>
            <h1><?php echo $editing ? 'Editar Movimento' : 'Novo Movimento'; ?></h1>
        </div>
    </div>
    <a href="movimentos.php" class="btn pbg" style="height:40px;font-size:.875rem;">
        <i class="bx bx-arrow-back"></i> Voltar
    </a>
</div>

<!-- ── FORMULÁRIO PRINCIPAL ────────────────────────────────────────────── -->
<div class="pcard">
    <div class="pch">
        <h5 class="pct"><i class="bx bx-edit-alt"></i> Informações do Movimento</h5>
        <?php if ($editing): ?>
        <span style="font-size:.8rem;color:var(--text-tertiary);font-weight:600;">ID #<?php echo $movimento_id; ?></span>
        <?php endif; ?>
    </div>
    <div class="pcb">
        <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF'].($editing?'?id='.$movimento_id:'')); ?>"
              method="POST" enctype="multipart/form-data">
            <div class="pfg">

                <div class="pfg-g s3">
                    <label class="plbl">Título <span class="req">*</span></label>
                    <input type="text" name="titulo" class="pinp" required placeholder="Nome do movimento..."
                           value="<?php echo htmlspecialchars($movimento['titulo']??''); ?>">
                </div>

                <div class="pfg-g">
                    <label class="plbl">Status</label>
                    <select name="status" class="psel">
                        <option value="publicado" <?php echo(($movimento['status']??'publicado')==='publicado')?'selected':'';?>>✅ Publicado</option>
                        <option value="rascunho"  <?php echo(($movimento['status']??'')==='rascunho') ?'selected':'';?>>✏️ Rascunho</option>
                        <option value="arquivado" <?php echo(($movimento['status']??'')==='arquivado')?'selected':'';?>>📦 Arquivado</option>
                    </select>
                </div>

                <div class="pfg-g s2">
                    <label class="plbl">Tema</label>
                    <input type="text" name="tema" class="pinp" placeholder="Ex: Empoderamento Feminino"
                           value="<?php echo htmlspecialchars($movimento['tema']??''); ?>">
                </div>

                <div class="pfg-g">
                    <label class="plbl">Data do Evento</label>
                    <input type="date" name="data_evento" class="pinp"
                           value="<?php echo htmlspecialchars($movimento['data_evento']??''); ?>">
                </div>

                <div class="pfg-g">
                    <label class="plbl">Local</label>
                    <input type="text" name="local" class="pinp" placeholder="Ex: Maputo"
                           value="<?php echo htmlspecialchars($movimento['local']??''); ?>">
                </div>

                <div class="pfg-g sf">
                    <label class="plbl">Descrição <span class="req">*</span></label>
                    <textarea name="descricao" class="ptxt" rows="6" required
                              placeholder="Descreva o movimento..."><?php echo htmlspecialchars($movimento['descricao']??''); ?></textarea>
                </div>

                <div class="pfg-g sf">
                    <label class="plbl">Imagem Principal</label>

                    <?php if ($editing && !empty($movimento['imagem_principal'])): ?>
                    <div class="pci">
                        <img src="../<?php echo htmlspecialchars($movimento['imagem_principal']); ?>"
                             alt="Imagem actual"
                             onerror="this.style.opacity='.2'">
                        <span class="pcib"><i class="bx bx-camera"></i> Clique abaixo para substituir</span>
                    </div>
                    <?php endif; ?>

                    <div class="puz" id="mz">
                        <input type="file" name="imagem_principal" accept="image/*" onchange="previewMainImage(this)">
                        <span class="puzi"><i class="bx bx-image-add"></i></span>
                        <div class="puzt" id="mzt">
                            <?php echo($editing&&!empty($movimento['imagem_principal']))?'Clique para substituir a imagem':'Clique ou arraste uma imagem';?>
                        </div>
                        <div class="puzh">JPG, PNG, WebP · máx 10MB · 1200×800px recomendado</div>
                    </div>

                    <div class="pnp" id="pnp">
                        <img id="pnpt" src="" alt="Preview">
                        <p id="pnpn"></p>
                    </div>
                </div>

            </div><!-- /pfg -->

            <div class="pfa">
                <button type="submit" name="submit" class="btn pbp">
                    <i class="bx bx-save"></i>
                    <?php echo $editing ? 'Guardar Alterações' : 'Criar Movimento'; ?>
                </button>
                <a href="movimentos.php" class="btn pbg"><i class="bx bx-x"></i> Cancelar</a>
            </div>
        </form>
    </div>
</div>

<?php if ($editing): ?>
<!-- ── GALERIA DE FOTOS ─────────────────────────────────────────────────── -->
<div class="pcard">
    <div class="pch">
        <h5 class="pct"><i class="bx bx-photo-album"></i> Galeria de Fotos</h5>
        <?php
        $cs = $conn->prepare("SELECT COUNT(*) as c FROM {$fotos_table} WHERE movimento_id=?");
        $cs->bind_param("i", $movimento_id);
        $cs->execute();
        $cnt = intval($cs->get_result()->fetch_assoc()['c'] ?? 0);
        $cs->close();
        ?>
        <span style="font-size:.8rem;color:var(--text-tertiary);font-weight:600;">
            <?php echo $cnt; ?> foto<?php echo $cnt !== 1 ? 's' : ''; ?>
        </span>
    </div>
    <div class="pcb">

        <!-- Form upload -->
        <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']).'?id='.$movimento_id; ?>"
              method="POST" enctype="multipart/form-data">
            <div class="pguz" id="gz">
                <input type="file" name="fotos[]" accept="image/*" multiple onchange="updateGalleryCount(this)">
                <span class="pguzi"><i class="bx bx-cloud-upload"></i></span>
                <div class="pguzt">Clique ou arraste as fotos aqui</div>
                <div class="pguzh">Múltiplas fotos · JPG, PNG, WebP · máx 10MB cada</div>
                <div class="ppill" id="ppill">
                    <i class="bx bx-images"></i><span id="ppillt">0 fotos selecionadas</span>
                </div>
            </div>
            <div style="display:flex;justify-content:flex-end;margin-bottom:1.5rem;">
                <button type="submit" name="upload_fotos" class="btn pbu">
                    <i class="bx bx-upload"></i> Fazer Upload
                </button>
            </div>
        </form>

        <!-- Fotos existentes -->
        <?php
        $cols = [];
        $cr = $conn->query("SHOW COLUMNS FROM {$fotos_table}");
        while ($c = $cr->fetch_assoc()) $cols[] = $c['Field'];
        $ob = in_array('ordem', $cols) ? 'ordem, id' : 'id';

        $fs = $conn->prepare("SELECT * FROM {$fotos_table} WHERE movimento_id=? ORDER BY {$ob}");
        $fs->bind_param("i", $movimento_id);
        $fs->execute();
        $fr = $fs->get_result();
        $fs->close();
        ?>

        <?php if ($fr->num_rows > 0): ?>
        <div class="pgrd">
            <?php while ($foto = $fr->fetch_assoc()): ?>
            <div class="pgi">
                <img src="../<?php echo htmlspecialchars($foto['foto']); ?>"
                     alt="Foto da galeria"
                     loading="lazy"
                     onerror="this.style.opacity='.15'">
                <div class="pgio">
                    <form method="POST"
                          action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']).'?id='.$movimento_id; ?>"
                          style="display:contents;"
                          onsubmit="return confirm('Esta ação é permanente. Deseja remover a foto?')">
                        <input type="hidden" name="foto_id" value="<?php echo $foto['id']; ?>">
                        <button type="submit" name="remover_foto" class="pbrm" title="Remover foto">
                            <i class="bx bx-trash"></i>
                        </button>
                    </form>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
        <?php else: ?>
        <div class="pge">
            <i class="bx bx-images"></i>
            Nenhuma foto na galeria.<br>
            <small>Use o campo acima para adicionar imagens.</small>
        </div>
        <?php endif; ?>

    </div>
</div>
<?php endif; ?>

</div><!-- /.pw -->
</div><!-- /.content-wrapper -->

<?php include('footerprincipal.php'); ?>
<div class="content-backdrop fade"></div>

<script>
function previewMainImage(input) {
    if (!input.files || !input.files[0]) return;
    const file = input.files[0];
    const reader = new FileReader();
    reader.onload = e => {
        document.getElementById('pnpt').src = e.target.result;
        document.getElementById('pnp').style.display = 'block';
    };
    reader.readAsDataURL(file);
    document.getElementById('mzt').textContent = '✓ ' + file.name;
    document.getElementById('pnpn').textContent = file.name + ' · ' + (file.size / 1048576).toFixed(2) + 'MB';
    document.getElementById('mz').style.borderColor = 'var(--success)';
    document.getElementById('mz').style.background = 'var(--success-light)';
}

function updateGalleryCount(input) {
    const pill = document.getElementById('ppill');
    const txt  = document.getElementById('ppillt');
    const zone = document.getElementById('gz');
    if (input.files && input.files.length > 0) {
        const n = input.files.length;
        txt.textContent = n + ' foto' + (n > 1 ? 's' : '') + ' selecionada' + (n > 1 ? 's' : '');
        pill.style.display = 'inline-flex';
        zone.style.borderColor = 'var(--success)';
        zone.style.background = 'var(--success-light)';
    } else {
        pill.style.display = 'none';
        zone.style.borderColor = '';
        zone.style.background = '';
    }
}

// Drag & drop visual feedback
document.querySelectorAll('.puz, .pguz').forEach(z => {
    z.addEventListener('dragover', e => {
        e.preventDefault();
        z.classList.add('on');
    });
    z.addEventListener('dragleave', () => z.classList.remove('on'));
    z.addEventListener('drop', e => {
        e.preventDefault();
        z.classList.remove('on');
    });
});

// Auto-hide toast after 5 seconds
setTimeout(() => {
    const toast = document.getElementById('toastContainer');
    if (toast) {
        toast.style.animation = 'slideIn 0.3s ease reverse';
        setTimeout(() => toast?.remove(), 300);
    }
}, 5000);
</script>

<?php include('footer.php'); $conn->close(); ?>