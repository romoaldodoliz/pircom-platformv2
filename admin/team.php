<?php
declare(strict_types=1);
include('header.php');
include('config/conexao.php');

ini_set('memory_limit', '256M');

$msg     = '';
$msgType = '';
$editing = null;

// ─────────────────────────────────────────────────────────────────────────────
// HELPER: detect MIME from raw binary content (no foto_mime column needed)
// ─────────────────────────────────────────────────────────────────────────────
function detectMimeFromBlob(?string $blob): string
{
    if (empty($blob)) return '';
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime  = $finfo->buffer($blob);
    $allowed = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    return in_array($mime, $allowed, true) ? $mime : '';
}

// ─────────────────────────────────────────────────────────────────────────────
// HELPER: BLOB → inline base64 data URI for <img src="">
// ─────────────────────────────────────────────────────────────────────────────
function blobToDataUri(?string $blob): string
{
    if (empty($blob)) return '';
    $mime = detectMimeFromBlob($blob);
    if (!$mime) return '';
    return 'data:' . $mime . ';base64,' . base64_encode($blob);
}

// ─────────────────────────────────────────────────────────────────────────────
// HELPER: validate & read uploaded image → ['blob'=>..,'error'=>..]
// ─────────────────────────────────────────────────────────────────────────────
function processPhoto(array $file): array
{
    $allowed = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];

    if ($file['error'] !== UPLOAD_ERR_OK) {
        $codes = [
            UPLOAD_ERR_INI_SIZE   => 'Ficheiro excede o limite do servidor.',
            UPLOAD_ERR_FORM_SIZE  => 'Ficheiro excede o limite do formulário.',
            UPLOAD_ERR_PARTIAL    => 'Upload incompleto.',
            UPLOAD_ERR_NO_FILE    => 'Nenhum ficheiro enviado.',
            UPLOAD_ERR_NO_TMP_DIR => 'Pasta temporária em falta.',
            UPLOAD_ERR_CANT_WRITE => 'Não foi possível gravar o ficheiro.',
            UPLOAD_ERR_EXTENSION  => 'Upload bloqueado por extensão PHP.',
        ];
        return ['blob' => null, 'error' => $codes[$file['error']] ?? 'Erro desconhecido.'];
    }

    if ($file['size'] > 5 * 1024 * 1024) {
        return ['blob' => null, 'error' => 'Imagem demasiado grande. Máximo 5 MB.'];
    }

    // Validate by actual file content, NOT by extension
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime  = $finfo->file($file['tmp_name']);

    if (!in_array($mime, $allowed, true)) {
        return ['blob' => null, 'error' => 'Tipo não permitido (' . $mime . '). Use JPEG, PNG, GIF ou WEBP.'];
    }

    $blob = file_get_contents($file['tmp_name']);
    if ($blob === false) {
        return ['blob' => null, 'error' => 'Não foi possível ler o ficheiro.'];
    }

    return ['blob' => $blob, 'error' => null];
}

// ─────────────────────────────────────────────────────────────────────────────
// DELETE
// ─────────────────────────────────────────────────────────────────────────────
if (isset($_POST['action']) && $_POST['action'] === 'delete') {
    $id   = (int)$_POST['id'];
    $stmt = $conn->prepare("DELETE FROM team_members WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        $msg = 'Membro removido com sucesso.'; $msgType = 'success';
    } else {
        $msg = 'Erro ao remover: ' . $stmt->error; $msgType = 'danger';
    }
    $stmt->close();
}

// ─────────────────────────────────────────────────────────────────────────────
// INSERT
// ─────────────────────────────────────────────────────────────────────────────
if (isset($_POST['action']) && $_POST['action'] === 'save' && empty($_POST['id'])) {
    $nome      = trim($_POST['nome']);
    $cargo     = trim($_POST['cargo']);
    $descricao = trim($_POST['descricao']);
    $email     = trim($_POST['email']);
    $linkedin  = trim($_POST['linkedin']);
    $categoria = $_POST['categoria'];
    $ordem     = (int)$_POST['ordem'];
    $ativo     = isset($_POST['ativo']) ? 1 : 0;
    $blob      = null;

    if (isset($_FILES['foto']) && $_FILES['foto']['error'] !== UPLOAD_ERR_NO_FILE) {
        $r = processPhoto($_FILES['foto']);
        if ($r['error']) {
            $msg = 'Erro na imagem: ' . $r['error']; $msgType = 'danger';
        } else {
            $blob = $r['blob'];
        }
    }

    if (empty($msg)) {
        $null = null;
        $stmt = $conn->prepare(
            "INSERT INTO team_members (nome, cargo, descricao, foto, email, linkedin, categoria, ordem, ativo)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)"
        );
        $stmt->bind_param("sssb" . "ssis",
            $nome, $cargo, $descricao,
            $null,   // blob placeholder — index 3
            $email, $linkedin, $categoria, $ordem, $ativo
        );
        if ($blob !== null) {
            $stmt->send_long_data(3, $blob);
        }
        if ($stmt->execute()) {
            $msg = 'Membro <strong>' . htmlspecialchars($nome) . '</strong> adicionado.';
            $msgType = 'success';
        } else {
            $msg = 'Erro ao inserir: ' . $stmt->error; $msgType = 'danger';
        }
        $stmt->close();
    }
}

// ─────────────────────────────────────────────────────────────────────────────
// UPDATE
// ─────────────────────────────────────────────────────────────────────────────
if (isset($_POST['action']) && $_POST['action'] === 'save' && !empty($_POST['id'])) {
    $id        = (int)$_POST['id'];
    $nome      = trim($_POST['nome']);
    $cargo     = trim($_POST['cargo']);
    $descricao = trim($_POST['descricao']);
    $email     = trim($_POST['email']);
    $linkedin  = trim($_POST['linkedin']);
    $categoria = $_POST['categoria'];
    $ordem     = (int)$_POST['ordem'];
    $ativo     = isset($_POST['ativo']) ? 1 : 0;
    $newBlob   = null;
    $hasNewPhoto = false;

    if (isset($_FILES['foto']) && $_FILES['foto']['error'] !== UPLOAD_ERR_NO_FILE) {
        $r = processPhoto($_FILES['foto']);
        if ($r['error']) {
            $msg = 'Erro na imagem: ' . $r['error']; $msgType = 'danger';
        } else {
            $newBlob     = $r['blob'];
            $hasNewPhoto = true;
        }
    }

    if (empty($msg)) {
        if ($hasNewPhoto) {
            // Replace photo BLOB
            $null = null;
            $stmt = $conn->prepare(
                "UPDATE team_members
                 SET nome=?, cargo=?, descricao=?, foto=?, email=?, linkedin=?, categoria=?, ordem=?, ativo=?
                 WHERE id=?"
            );
            $stmt->bind_param("sssb" . "ssiii",
                $nome, $cargo, $descricao,
                $null,   // blob placeholder — index 3
                $email, $linkedin, $categoria, $ordem, $ativo,
                $id
            );
            $stmt->send_long_data(3, $newBlob);
        } else {
            // No new photo — leave existing BLOB untouched
            $stmt = $conn->prepare(
                "UPDATE team_members
                 SET nome=?, cargo=?, descricao=?, email=?, linkedin=?, categoria=?, ordem=?, ativo=?
                 WHERE id=?"
            );
            $stmt->bind_param("ssssssiii",
                $nome, $cargo, $descricao,
                $email, $linkedin, $categoria, $ordem, $ativo,
                $id
            );
        }

        if ($stmt->execute()) {
            $msg = 'Membro <strong>' . htmlspecialchars($nome) . '</strong> actualizado.';
            $msgType = 'success';
        } else {
            $msg = 'Erro ao actualizar: ' . $stmt->error; $msgType = 'danger';
        }
        $stmt->close();
    }
}

// ─────────────────────────────────────────────────────────────────────────────
// LOAD for editing
// ─────────────────────────────────────────────────────────────────────────────
if (isset($_GET['edit'])) {
    $eid  = (int)$_GET['edit'];
    $stmt = $conn->prepare(
        "SELECT id, nome, cargo, descricao, foto, email, linkedin, categoria, ordem, ativo
         FROM team_members WHERE id = ? LIMIT 1"
    );
    $stmt->bind_param("i", $eid);
    $stmt->execute();
    $res     = $stmt->get_result();
    $editing = ($res && $res->num_rows > 0) ? $res->fetch_assoc() : null;
    $stmt->close();
}

// ─────────────────────────────────────────────────────────────────────────────
// LIST — SELECT only columns needed (BLOB included for avatar)
// ─────────────────────────────────────────────────────────────────────────────
$members = [];
$res = $conn->query(
    "SELECT id, nome, cargo, foto, email, categoria, ordem, ativo
     FROM team_members ORDER BY ordem ASC, id ASC"
);
if ($res) {
    while ($r = $res->fetch_assoc()) $members[] = $r;
}
$conn->close();

$total  = count($members);
$ativos = count(array_filter($members, fn($m) => $m['ativo']));

function getInitials(string $nome): string
{
    $words = preg_split('/\s+/', trim($nome));
    $i = '';
    foreach (array_slice($words, 0, 2) as $w)
        $i .= mb_strtoupper(mb_substr($w, 0, 1, 'UTF-8'), 'UTF-8');
    return $i;
}

function catBadge(string $cat): string
{
    $map = [
        'lideranca' => 'b-or',  'liderança' => 'b-or',
        'direcao'   => 'b-or',  'direcção'  => 'b-or',
        'conselho'  => 'b-amber',
        'tecnica'   => 'b-blue', 'técnico'  => 'b-blue',
        'administrativa' => 'b-purple', 'voluntário' => 'b-purple',
    ];
    $key = mb_strtolower(preg_replace('/[^a-zA-ZÀ-ú]/u', '', $cat));
    return $map[$key] ?? 'b-slate';
}
?>
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<style>
.pm{
  --or:#FF6F0F; --or-dk:#D95A00; --or-lt:#FFF3EA;
  --blk:#0D0D0D; --ink:#222; --ink-s:#555; --ink-m:#999;
  --wht:#fff; --bg:#F5F6F8; --bdr:#E5E7EB;
  --rad:10px; --ease:.2s cubic-bezier(.4,0,.2,1);
  --sh:0 1px 4px rgba(0,0,0,.07),0 4px 16px rgba(0,0,0,.05);
  --sh-or:0 4px 16px rgba(255,111,15,.22);
  font-family:'Outfit',sans-serif; color:var(--ink);
}
.pm-page{ background:var(--bg); padding:1.75rem; min-height:100vh; }
.pm-breadcrumb{ font-size:.75rem; color:var(--ink-m); display:flex; align-items:center; gap:.3rem; margin-bottom:.3rem; }
.pm-breadcrumb a{ color:var(--ink-m); text-decoration:none; }
.pm-breadcrumb a:hover{ color:var(--or); }
.pm-title{ font-size:1.55rem; font-weight:800; color:var(--blk); letter-spacing:-.3px; margin:0 0 1.25rem; }
.pm-title em{ font-style:normal; color:var(--or); }
.pm-alert{ display:flex; align-items:center; gap:.7rem; padding:.85rem 1.1rem; border-radius:var(--rad); font-size:.875rem; font-weight:500; margin-bottom:1.25rem; border-left:4px solid transparent; animation:fadeIn .25s ease; }
.pm-alert.success{ background:#ECFDF5; color:#059669; border-color:#059669; }
.pm-alert.danger { background:#FFF1F2; color:#E11D48; border-color:#E11D48; }
@keyframes fadeIn{ from{opacity:0;transform:translateY(-4px)} to{opacity:1;transform:none} }
.pm-stats{ display:flex; flex-wrap:wrap; gap:.6rem; margin-bottom:1.5rem; }
.pm-chip{ display:inline-flex; align-items:center; gap:.4rem; background:var(--wht); border:1px solid var(--bdr); border-radius:999px; padding:.35rem .9rem; font-size:.8rem; font-weight:600; color:var(--ink-s); box-shadow:0 1px 3px rgba(0,0,0,.05); }
.pm-dot{ width:7px; height:7px; border-radius:50%; flex-shrink:0; }
.pm-layout{ display:grid; grid-template-columns:1fr 390px; gap:1.5rem; align-items:start; }
@media(max-width:1050px){ .pm-layout{ grid-template-columns:1fr; } }
.pm-card{ background:var(--wht); border:1px solid var(--bdr); border-radius:16px; box-shadow:var(--sh); overflow:hidden; }
.pm-card-head{ padding:.9rem 1.35rem; border-bottom:1px solid var(--bdr); display:flex; align-items:center; justify-content:space-between; background:#FAFBFC; }
.pm-card-title{ display:flex; align-items:center; gap:.5rem; font-size:.95rem; font-weight:700; color:var(--blk); margin:0; }
.pm-card-icon{ width:30px; height:30px; border-radius:8px; display:flex; align-items:center; justify-content:center; font-size:.9rem; flex-shrink:0; }
.pm-card-meta{ font-size:.78rem; color:var(--ink-m); }
.pm-card-body{ padding:1.35rem; }
.pm-tbl-wrap{ overflow-x:auto; }
table.pm-tbl{ width:100%; border-collapse:collapse; font-size:.865rem; }
.pm-tbl thead tr{ background:#F8FAFC; border-bottom:2px solid var(--bdr); }
.pm-tbl th{ padding:.6rem 1rem; text-align:left; font-size:.7rem; font-weight:700; text-transform:uppercase; letter-spacing:.7px; color:var(--ink-m); white-space:nowrap; }
.pm-tbl td{ padding:.8rem 1rem; border-bottom:1px solid var(--bdr); vertical-align:middle; color:var(--ink-s); }
.pm-tbl tbody tr:last-child td{ border-bottom:none; }
.pm-tbl tbody tr{ transition:background var(--ease); }
.pm-tbl tbody tr:hover{ background:#FAFBFF; }
.pm-tbl tbody tr.row-edit{ background:var(--or-lt); outline:2px solid var(--or); outline-offset:-2px; }
.m-cell{ display:flex; align-items:center; gap:.65rem; }
.av{ width:42px; height:42px; border-radius:8px; object-fit:cover; border:2px solid var(--bdr); flex-shrink:0; display:block; }
.av-ph{ width:42px; height:42px; border-radius:8px; flex-shrink:0; background:var(--blk); color:var(--or); display:flex; align-items:center; justify-content:center; font-size:.9rem; font-weight:800; }
.m-info strong{ display:block; font-weight:700; color:var(--blk); font-size:.875rem; }
.m-info small{ color:var(--ink-m); font-size:.76rem; }
.pm-badge{ display:inline-flex; align-items:center; gap:.28rem; padding:.2rem .65rem; border-radius:999px; font-size:.7rem; font-weight:700; text-transform:uppercase; letter-spacing:.3px; white-space:nowrap; }
.b-green{ background:#ECFDF5; color:#059669; }
.b-slate{ background:#F1F5F9; color:#64748B; }
.b-or{ background:var(--or-lt); color:var(--or-dk); }
.b-blue{ background:#EFF6FF; color:#2563EB; }
.b-purple{ background:#F5F3FF; color:#7C3AED; }
.b-amber{ background:#FFFBEB; color:#D97706; }
.ib{ display:inline-flex; align-items:center; justify-content:center; width:32px; height:32px; border-radius:8px; border:1px solid var(--bdr); background:var(--wht); color:var(--ink-s); font-size:.85rem; cursor:pointer; transition:all var(--ease); text-decoration:none; }
.ib.edit:hover{ background:var(--or-lt); color:var(--or-dk); border-color:#FFD4B0; }
.ib.del:hover{ background:#FFF1F2; color:#E11D48; border-color:#FECDD3; }
.ib-row{ display:flex; gap:.35rem; }
.pm-empty{ text-align:center; padding:3rem 1.5rem; }
.pm-empty i{ font-size:2.5rem; color:var(--ink-m); opacity:.3; display:block; margin-bottom:.75rem; }
.pm-empty p{ font-size:.875rem; color:var(--ink-m); margin:0; }
.pm-form-panel{ position:sticky; top:1.5rem; }
.pm-mode-pill{ display:inline-flex; align-items:center; gap:.35rem; padding:.25rem .75rem; border-radius:999px; font-size:.7rem; font-weight:700; text-transform:uppercase; letter-spacing:.4px; }
.mode-add{ background:#ECFDF5; color:#059669; }
.mode-edit{ background:#FFFBEB; color:#D97706; }
.fs-title{ font-size:.7rem; font-weight:700; text-transform:uppercase; letter-spacing:.7px; color:var(--ink-m); padding-bottom:.4rem; margin:1.1rem 0 .75rem; border-bottom:1px solid var(--bdr); }
.fs-title:first-of-type{ margin-top:0; }
.ff{ margin-bottom:.75rem; }
.fl{ display:block; font-size:.8rem; font-weight:600; color:var(--ink-s); margin-bottom:.3rem; }
.fl sup{ color:#E11D48; }
.fi,.fs,.ft{ width:100%; padding:.56rem .85rem; border:1.5px solid var(--bdr); border-radius:8px; background:var(--bg); font-family:inherit; font-size:.875rem; color:var(--ink); outline:none; transition:border-color var(--ease),box-shadow var(--ease),background var(--ease); box-sizing:border-box; }
.fi:focus,.fs:focus,.ft:focus{ border-color:var(--or); box-shadow:0 0 0 3px rgba(255,111,15,.14); background:var(--wht); }
.ft{ resize:vertical; min-height:78px; }
.fs{ appearance:none; cursor:pointer; }
.frow{ display:grid; grid-template-columns:1fr 1fr; gap:.75rem; }
.ftog{ display:flex; align-items:center; gap:.65rem; padding:.56rem .85rem; border:1.5px solid var(--bdr); border-radius:8px; background:var(--bg); cursor:pointer; transition:border-color var(--ease); }
.ftog:hover{ border-color:var(--or); }
.sw{ position:relative; width:38px; height:22px; flex-shrink:0; }
.sw input{ opacity:0; width:0; height:0; }
.sw-track{ position:absolute; inset:0; background:#CBD5E1; border-radius:999px; transition:background var(--ease); }
.sw-track::before{ content:''; position:absolute; width:16px; height:16px; border-radius:50%; background:white; top:3px; left:3px; transition:transform var(--ease); box-shadow:0 1px 3px rgba(0,0,0,.25); }
.sw input:checked~.sw-track{ background:var(--or); }
.sw input:checked~.sw-track::before{ transform:translateX(16px); }
.sw-lbl{ font-size:.855rem; font-weight:500; color:var(--ink-s); }
.fphoto{ border:2px dashed var(--bdr); border-radius:12px; padding:1.2rem; text-align:center; cursor:pointer; transition:border-color var(--ease),background var(--ease); }
.fphoto:hover{ border-color:var(--or); background:var(--or-lt); }
.fphoto input[type=file]{ display:none; }
.fphoto-inner{ display:flex; flex-direction:column; align-items:center; gap:.4rem; }
.fphoto-img{ width:72px; height:72px; border-radius:50%; object-fit:cover; border:3px solid var(--or); display:none; }
.fphoto-ico{ font-size:1.7rem; color:var(--ink-m); line-height:1; }
.fphoto-hint{ font-size:.77rem; color:var(--ink-m); }
.pm-btn{ display:inline-flex; align-items:center; gap:.45rem; padding:.62rem 1.2rem; border-radius:8px; font-family:inherit; font-size:.875rem; font-weight:700; cursor:pointer; border:none; transition:all var(--ease); text-decoration:none; white-space:nowrap; }
.pm-btn-primary{ background:var(--or); color:var(--wht); box-shadow:var(--sh-or); }
.pm-btn-primary:hover{ background:var(--or-dk); transform:translateY(-1px); box-shadow:0 6px 20px rgba(255,111,15,.35); }
.pm-btn-ghost{ background:transparent; color:var(--ink-s); border:1.5px solid var(--bdr); }
.pm-btn-ghost:hover{ background:var(--bg); color:var(--blk); }
.pm-btn-full{ width:100%; justify-content:center; }
.pm-btn-group{ display:flex; flex-direction:column; gap:.6rem; margin-top:1.35rem; }
</style>

<div class="pm pm-page">
<div class="pm-breadcrumb">
  <a href="dashboard.php">Dashboard</a>
  <i class="bi bi-chevron-right" style="font-size:.6rem;"></i>
  <span>Equipa</span>
</div>
<h1 class="pm-title">Gestão de <em>Equipa</em></h1>

<?php if ($msg): ?>
<div class="pm-alert <?= $msgType ?>" role="alert">
  <i class="bi bi-<?= $msgType === 'success' ? 'check-circle-fill' : 'exclamation-triangle-fill' ?>"></i>
  <span><?= $msg ?></span>
</div>
<?php endif; ?>

<div class="pm-stats">
  <div class="pm-chip"><span class="pm-dot" style="background:var(--or)"></span><?= $total ?> membros</div>
  <div class="pm-chip"><span class="pm-dot" style="background:#059669"></span><?= $ativos ?> activos</div>
  <div class="pm-chip"><span class="pm-dot" style="background:#94A3B8"></span><?= $total - $ativos ?> inactivos</div>
</div>

<div class="pm-layout">

  <!-- TABLE -->
  <div class="pm-card">
    <div class="pm-card-head">
      <h2 class="pm-card-title">
        <span class="pm-card-icon" style="background:var(--or-lt);color:var(--or)"><i class="bi bi-people-fill"></i></span>
        Membros da Equipa
      </h2>
      <span class="pm-card-meta"><?= $total ?> registos</span>
    </div>
    <div class="pm-tbl-wrap">
      <table class="pm-tbl" aria-label="Lista de membros">
        <thead>
          <tr>
            <th>Membro</th><th>Cargo</th><th>Categoria</th>
            <th style="text-align:center">Ord.</th><th>Estado</th><th>Acções</th>
          </tr>
        </thead>
        <tbody>
        <?php if (empty($members)): ?>
          <tr><td colspan="6">
            <div class="pm-empty">
              <i class="bi bi-person-plus"></i>
              <p>Nenhum membro ainda.<br>Adicione o primeiro usando o formulário ao lado.</p>
            </div>
          </td></tr>
        <?php else: ?>
          <?php foreach ($members as $m):
            $ini     = getInitials($m['nome']);
            $isEdit  = ($editing && $editing['id'] == $m['id']);
            // ── THE FIX: finfo detects MIME directly from the BLOB bytes ──
            $dataUri = blobToDataUri($m['foto'] ?? null);
          ?>
          <tr class="<?= $isEdit ? 'row-edit' : '' ?>">
            <td>
              <div class="m-cell">
                <?php if ($dataUri): ?>
                  <img class="av" src="<?= $dataUri ?>" alt="<?= htmlspecialchars($m['nome']) ?>">
                <?php else: ?>
                  <div class="av-ph"><?= $ini ?></div>
                <?php endif; ?>
                <div class="m-info">
                  <strong><?= htmlspecialchars($m['nome']) ?></strong>
                  <?php if (!empty($m['email'])): ?>
                    <small><?= htmlspecialchars($m['email']) ?></small>
                  <?php endif; ?>
                </div>
              </div>
            </td>
            <td style="font-size:.84rem;max-width:160px;white-space:normal"><?= htmlspecialchars($m['cargo']) ?></td>
            <td><span class="pm-badge <?= catBadge($m['categoria']) ?>"><?= htmlspecialchars($m['categoria']) ?></span></td>
            <td style="text-align:center;font-weight:700"><?= $m['ordem'] ?></td>
            <td>
              <span class="pm-badge <?= $m['ativo'] ? 'b-green' : 'b-slate' ?>">
                <i class="bi bi-<?= $m['ativo'] ? 'check-circle' : 'dash-circle' ?>"></i>
                <?= $m['ativo'] ? 'Activo' : 'Inactivo' ?>
              </span>
            </td>
            <td>
              <div class="ib-row">
                <a href="?edit=<?= $m['id'] ?>#fp" class="ib edit" title="Editar"><i class="bi bi-pencil"></i></a>
                <form method="POST" style="margin:0"
                      onsubmit="return confirm('Remover <?= htmlspecialchars(addslashes($m['nome'])) ?>?\nEsta acção não pode ser revertida.')">
                  <input type="hidden" name="action" value="delete">
                  <input type="hidden" name="id"     value="<?= $m['id'] ?>">
                  <button class="ib del" type="submit" title="Remover"><i class="bi bi-trash"></i></button>
                </form>
              </div>
            </td>
          </tr>
          <?php endforeach; ?>
        <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- FORM PANEL -->
  <div class="pm-form-panel" id="fp">
    <div class="pm-card">
      <div class="pm-card-head">
        <h2 class="pm-card-title">
          <span class="pm-card-icon"
                style="background:<?= $editing ? '#FFFBEB' : '#ECFDF5' ?>;color:<?= $editing ? '#D97706' : '#059669' ?>">
            <i class="bi bi-<?= $editing ? 'pencil-square' : 'person-plus-fill' ?>"></i>
          </span>
          <?= $editing ? 'Editar Membro' : 'Novo Membro' ?>
        </h2>
        <span class="pm-mode-pill <?= $editing ? 'mode-edit' : 'mode-add' ?>">
          <i class="bi bi-<?= $editing ? 'pencil' : 'plus-circle' ?>"></i>
          <?= $editing ? 'Edição' : 'Adição' ?>
        </span>
      </div>
      <div class="pm-card-body">
        <form action="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>"
              method="POST" enctype="multipart/form-data">
          <input type="hidden" name="action" value="save">
          <input type="hidden" name="id"     value="<?= $editing['id'] ?? '' ?>">

          <!-- FOTO -->
          <p class="fs-title">Fotografia</p>
          <?php
            // ── THE FIX: build edit preview URI from BLOB, no file path ──
            $editDataUri = $editing ? blobToDataUri($editing['foto'] ?? null) : '';
          ?>
          <div class="fphoto ff"
               onclick="document.getElementById('fi-foto').click()"
               role="button" tabindex="0" aria-label="Carregar foto">
            <input type="file" id="fi-foto" name="foto"
                   accept="image/jpeg,image/png,image/gif,image/webp"
                   onchange="previewImg(this)">
            <div class="fphoto-inner">
              <?php if ($editDataUri): ?>
                <img id="fi-preview" src="<?= $editDataUri ?>"
                     class="fphoto-img" style="display:block" alt="Foto actual">
                <span id="fi-ico" class="fphoto-ico" style="display:none"><i class="bi bi-camera-fill"></i></span>
              <?php else: ?>
                <img id="fi-preview" src="#" class="fphoto-img" alt="">
                <span id="fi-ico" class="fphoto-ico"><i class="bi bi-camera-fill"></i></span>
              <?php endif; ?>
              <span class="fphoto-hint">
                <?= $editDataUri
                    ? 'Clique para substituir (max. 5 MB)'
                    : 'Clique para carregar foto (opcional, max. 5 MB)' ?>
              </span>
            </div>
          </div>

          <!-- IDENTIFICACAO -->
          <p class="fs-title">Identificação</p>
          <div class="ff">
            <label class="fl" for="fi-nome">Nome completo <sup>*</sup></label>
            <input id="fi-nome" class="fi" type="text" name="nome" required
                   placeholder="Ex: Bispo Dinis Matsolo"
                   value="<?= htmlspecialchars($editing['nome'] ?? '') ?>">
          </div>
          <div class="ff">
            <label class="fl" for="fi-cargo">Cargo / Posição <sup>*</sup></label>
            <input id="fi-cargo" class="fi" type="text" name="cargo" required
                   placeholder="Ex: Director Executivo"
                   value="<?= htmlspecialchars($editing['cargo'] ?? '') ?>">
          </div>
          <div class="ff">
            <label class="fl" for="fi-desc">Descrição / Biografia</label>
            <textarea id="fi-desc" class="ft" name="descricao"
                      placeholder="Breve descrição e responsabilidades..."><?= htmlspecialchars($editing['descricao'] ?? '') ?></textarea>
          </div>

          <!-- CONTACTOS -->
          <p class="fs-title">Contactos</p>
          <div class="frow">
            <div class="ff" style="margin-bottom:0">
              <label class="fl" for="fi-email">E-mail</label>
              <input id="fi-email" class="fi" type="email" name="email"
                     placeholder="membro@pircom.org"
                     value="<?= htmlspecialchars($editing['email'] ?? '') ?>">
            </div>
            <div class="ff" style="margin-bottom:0">
              <label class="fl" for="fi-li">LinkedIn URL</label>
              <input id="fi-li" class="fi" type="url" name="linkedin"
                     placeholder="https://linkedin.com/in/..."
                     value="<?= htmlspecialchars($editing['linkedin'] ?? '') ?>">
            </div>
          </div>

          <!-- CONFIGURACOES -->
          <p class="fs-title">Configurações</p>
          <div class="frow">
            <div class="ff" style="margin-bottom:0">
              <label class="fl" for="fi-cat">Categoria <sup>*</sup></label>
              <select id="fi-cat" class="fs" name="categoria" required>
                <?php
                $cats = [
                    'liderança'      => 'Liderança',
                    'conselho'       => 'Conselho',
                    'técnico'        => 'Técnico',
                    'voluntário'     => 'Voluntário',
                    'direcao'        => 'Direcção',
                    'tecnica'        => 'Equipa Técnica',
                    'administrativa' => 'Administrativa',
                ];
                foreach ($cats as $v => $l):
                    $sel = (($editing['categoria'] ?? 'liderança') === $v) ? 'selected' : '';
                ?>
                  <option value="<?= $v ?>" <?= $sel ?>><?= $l ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="ff" style="margin-bottom:0">
              <label class="fl" for="fi-ord">Ordem (1 = primeiro)</label>
              <input id="fi-ord" class="fi" type="number" name="ordem" min="0"
                     value="<?= (int)($editing['ordem'] ?? 0) ?>">
            </div>
          </div>

          <div class="ff" style="margin-top:.75rem">
            <label class="fl">Estado de publicação</label>
            <label class="ftog">
              <div class="sw">
                <input type="checkbox" name="ativo" id="fi-ativo"
                       <?= ($editing ? (int)$editing['ativo'] : 1) ? 'checked' : '' ?>>
                <span class="sw-track"></span>
              </div>
              <span class="sw-lbl" id="swLabel">
                <?= ($editing ? (int)$editing['ativo'] : 1)
                    ? 'Activo – visível no site'
                    : 'Inactivo – oculto no site' ?>
              </span>
            </label>
          </div>

          <div class="pm-btn-group">
            <button type="submit" class="pm-btn pm-btn-primary pm-btn-full">
              <i class="bi bi-<?= $editing ? 'arrow-repeat' : 'plus-circle' ?>"></i>
              <?= $editing ? 'Guardar Alterações' : 'Adicionar Membro' ?>
            </button>
            <?php if ($editing): ?>
            <a href="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>"
               class="pm-btn pm-btn-ghost pm-btn-full">
              <i class="bi bi-x-lg"></i> Cancelar edição
            </a>
            <?php endif; ?>
          </div>

        </form>
      </div>
    </div>
  </div>

</div><!-- /pm-layout -->
</div><!-- /pm-page -->

<script>
function previewImg(input) {
  if (!input.files || !input.files[0]) return;
  const f = input.files[0];
  if (f.size > 5 * 1024 * 1024) { alert('Máximo 5 MB!'); input.value = ''; return; }
  if (!f.type.match(/image\/(jpeg|jpg|png|gif|webp)/)) {
    alert('Apenas JPEG, PNG, GIF ou WEBP.'); input.value = ''; return;
  }
  const r = new FileReader();
  r.onload = e => {
    const img = document.getElementById('fi-preview');
    const ico = document.getElementById('fi-ico');
    img.src = e.target.result;
    img.style.display = 'block';
    if (ico) ico.style.display = 'none';
  };
  r.readAsDataURL(f);
}
const tog = document.getElementById('fi-ativo');
const lbl = document.getElementById('swLabel');
if (tog && lbl) {
  tog.addEventListener('change', () => {
    lbl.textContent = tog.checked ? 'Activo – visível no site' : 'Inactivo – oculto no site';
  });
}
if (window.location.hash === '#fp') {
  setTimeout(() => document.getElementById('fp')?.scrollIntoView({ behavior: 'smooth', block: 'start' }), 120);
}
document.querySelector('.fphoto')?.addEventListener('keydown', e => {
  if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); document.getElementById('fi-foto').click(); }
});
</script>

<?php include('footer.php'); ?>