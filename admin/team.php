<?php
include('header.php');
include('config/conexao.php');

$msg     = '';
$msgType = '';
$editing = null;

// ── DELETE ────────────────────────────────────────────────
if (isset($_POST['action']) && $_POST['action'] === 'delete') {
    $id = (int)$_POST['id'];
    
    // Primeiro buscar a foto para deletar do disco
    $stmt = $conn->prepare("SELECT foto FROM team_members WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $membro = $result->fetch_assoc();
    $stmt->close();
    
    // Deletar a foto do disco se existir
    if ($membro && !empty($membro['foto'])) {
        $foto_path = '../' . $membro['foto']; // Volta uma pasta por estar em /admin/
        if (file_exists($foto_path)) {
            unlink($foto_path);
        }
    }
    
    // Deletar do banco
    $stmt = $conn->prepare("DELETE FROM team_members WHERE id = ?");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        $msg = 'Membro removido com sucesso.';
        $msgType = 'success';
    } else {
        $msg = 'Erro ao remover: ' . $stmt->error;
        $msgType = 'danger';
    }
    $stmt->close();
}

// Função para fazer upload da foto
function uploadFoto($file, $existing_foto = null) {
    // Deletar foto antiga se existir
    if ($existing_foto && file_exists('../' . $existing_foto)) {
        unlink('../' . $existing_foto);
    }
    
    // Configurações de upload
    $target_dir = "../uploads/team/";
    $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
    $max_size = 5 * 1024 * 1024; // 5MB
    
    // Criar diretório se não existir
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0755, true);
    }
    
    // Validar arquivo
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'error' => 'Erro no upload do arquivo'];
    }
    
    // Validar tipo
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime_type = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);
    
    if (!in_array($mime_type, $allowed_types)) {
        return ['success' => false, 'error' => 'Tipo de arquivo não permitido. Apenas imagens (JPEG, PNG, GIF, WEBP)'];
    }
    
    // Validar tamanho
    if ($file['size'] > $max_size) {
        return ['success' => false, 'error' => 'Arquivo muito grande. Máximo 5MB'];
    }
    
    // Gerar nome único
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = uniqid() . '_' . time() . '.' . $extension;
    $target_file = $target_dir . $filename;
    
    // Fazer upload
    if (move_uploaded_file($file['tmp_name'], $target_file)) {
        // Retornar caminho relativo para salvar no banco
        return ['success' => true, 'path' => 'uploads/team/' . $filename];
    } else {
        return ['success' => false, 'error' => 'Erro ao salvar o arquivo'];
    }
}

// ── INSERT ────────────────────────────────────────────────
if (isset($_POST['action']) && $_POST['action'] === 'save' && empty($_POST['id'])) {
    $nome      = trim($_POST['nome']);
    $cargo     = trim($_POST['cargo']);
    $descricao = trim($_POST['descricao']);
    $email     = trim($_POST['email']);
    $linkedin  = trim($_POST['linkedin']);
    $categoria = $_POST['categoria'];
    $ordem     = (int)$_POST['ordem'];
    $ativo     = isset($_POST['ativo']) ? 1 : 0;
    
    // Processar upload da foto
    $foto_path = null;
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
        $upload_result = uploadFoto($_FILES['foto']);
        if ($upload_result['success']) {
            $foto_path = $upload_result['path'];
        } else {
            $msg = 'Erro na imagem: ' . $upload_result['error'];
            $msgType = 'danger';
        }
    }
    
    // Se não houve erro ou não tinha foto, prosseguir
    if (empty($msg)) {
        $sql = "INSERT INTO team_members (nome, cargo, descricao, foto, email, linkedin, categoria, ordem, ativo) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssssis", 
            $nome, $cargo, $descricao, $foto_path, $email, $linkedin, $categoria, $ordem, $ativo
        );
        
        if ($stmt->execute()) {
            $msg = "Membro <strong>$nome</strong> adicionado com sucesso.";
            $msgType = 'success';
        } else {
            $msg = 'Erro ao adicionar: ' . $stmt->error;
            $msgType = 'danger';
        }
        $stmt->close();
    }
}

// ── UPDATE ────────────────────────────────────────────────
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
    
    // Buscar foto atual para possível deleção
    $stmt = $conn->prepare("SELECT foto FROM team_members WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $membro_atual = $result->fetch_assoc();
    $stmt->close();
    
    $foto_path = $membro_atual['foto']; // Manter foto atual por padrão
    
    // Processar nova foto se enviada
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
        $upload_result = uploadFoto($_FILES['foto'], $membro_atual['foto']);
        if ($upload_result['success']) {
            $foto_path = $upload_result['path'];
        } else {
            $msg = 'Erro na imagem: ' . $upload_result['error'];
            $msgType = 'danger';
        }
    }
    
    // Se não houve erro, prosseguir com update
    if (empty($msg)) {
        $sql = "UPDATE team_members SET 
                nome = ?, cargo = ?, descricao = ?, foto = ?, email = ?, 
                linkedin = ?, categoria = ?, ordem = ?, ativo = ? 
                WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssssiii", 
            $nome, $cargo, $descricao, $foto_path, $email, $linkedin, $categoria, $ordem, $ativo, $id
        );
        
        if ($stmt->execute()) {
            $msg = "Membro <strong>$nome</strong> actualizado com sucesso.";
            $msgType = 'success';
        } else {
            $msg = 'Erro ao actualizar: ' . $stmt->error;
            $msgType = 'danger';
        }
        $stmt->close();
    }
}

// ── LOAD EDIT ─────────────────────────────────────────────
if (isset($_GET['edit'])) {
    $eid = (int)$_GET['edit'];
    $stmt = $conn->prepare("SELECT * FROM team_members WHERE id = ? LIMIT 1");
    $stmt->bind_param("i", $eid);
    $stmt->execute();
    $res = $stmt->get_result();
    $editing = ($res && $res->num_rows > 0) ? $res->fetch_assoc() : null;
    $stmt->close();
}

// ── LIST ──────────────────────────────────────────────────
$members = [];
$res = $conn->query("SELECT * FROM team_members ORDER BY ordem ASC, id ASC");
if ($res) while ($r = $res->fetch_assoc()) $members[] = $r;
$conn->close();

// Helpers
$total  = count($members);
$ativos = count(array_filter($members, fn($m) => $m['ativo']));
$cats   = array_count_values(array_column($members, 'categoria'));
?>

<!DOCTYPE html>
<html lang="pt">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Gestão de Equipa · PIRCOM</title>
<!-- Seus estilos CSS existentes permanecem IGUAIS -->
</head>
<body>
<!-- Todo o HTML permanece IGUAL até a parte da tabela -->

<!-- MODIFICAÇÃO: Na tabela, alterar a exibição da imagem -->
<td>
    <div class="m-cell">
        <?php if (!empty($m['foto'])): ?>
            <img class="av" src="../<?= htmlspecialchars($m['foto']) ?>" alt="<?= htmlspecialchars($m['nome']) ?>">
        <?php else: ?>
            <div class="av-ph"><?= $initials ?></div>
        <?php endif; ?>
        <div class="m-info">
            <strong><?= htmlspecialchars($m['nome']) ?></strong>
            <?php if (!empty($m['email'])): ?>
                <small><?= htmlspecialchars($m['email']) ?></small>
            <?php endif; ?>
        </div>
    </div>
</td>

<!-- MODIFICAÇÃO: No formulário, ajustar a pré-visualização da imagem -->
<!-- Dentro do form, na parte da FOTO: -->
<p class="fs-title">Fotografia</p>
<div class="fphoto field" onclick="document.getElementById('fi-foto').click()" role="button" tabindex="0">
    <input type="file" id="fi-foto" name="foto" accept="image/jpeg,image/png,image/gif,image/webp" onchange="previewImg(this)">
    <div class="fphoto-inner">
        <?php if ($editing && !empty($editing['foto'])): ?>
            <img id="fi-preview"
                 src="../<?= htmlspecialchars($editing['foto']) ?>"
                 class="fphoto-img" style="display:block;" alt="">
            <span id="fi-ico" class="fphoto-ico" style="display:none;"><i class="bi bi-camera-fill"></i></span>
        <?php else: ?>
            <img id="fi-preview" src="#" class="fphoto-img" alt="">
            <span id="fi-ico" class="fphoto-ico"><i class="bi bi-camera-fill"></i></span>
        <?php endif; ?>
        <span class="fphoto-hint">
            <?= ($editing && !empty($editing['foto'])) ? 'Clique para substituir a foto (máx. 5MB)' : 'Clique para carregar foto (opcional, máx. 5MB)' ?>
        </span>
    </div>
</div>

<!-- O resto do HTML permanece IGUAL -->

<script>
// Photo preview - MODIFICADO para mostrar preview antes do upload
function previewImg(input) {
    if (!input.files || !input.files[0]) return;
    
    // Validar tamanho (5MB)
    if (input.files[0].size > 5 * 1024 * 1024) {
        alert('Arquivo muito grande! Máximo permitido: 5MB');
        input.value = '';
        return;
    }
    
    // Validar tipo
    if (!input.files[0].type.match(/image\/(jpeg|jpg|png|gif|webp)/)) {
        alert('Apenas imagens são permitidas (JPEG, PNG, GIF, WEBP)');
        input.value = '';
        return;
    }
    
    const r = new FileReader();
    r.onload = e => {
        const img = document.getElementById('fi-preview');
        const ico = document.getElementById('fi-ico');
        img.src = e.target.result;
        img.style.display = 'block';
        if (ico) ico.style.display = 'none';
    };
    r.readAsDataURL(input.files[0]);
}

// Toggle label (mantém igual)
const tog = document.getElementById('fi-ativo');
const lbl = document.getElementById('swLabel');
if (tog && lbl) {
    tog.addEventListener('change', () => {
        lbl.textContent = tog.checked ? 'Activo — visível no site' : 'Inactivo — oculto no site';
    });
}

// Scroll to form when editing (mantém igual)
if (window.location.hash === '#fp') {
    setTimeout(() => document.getElementById('fp')?.scrollIntoView({ behavior:'smooth', block:'start' }), 120);
}

// Keyboard support for photo zone (mantém igual)
document.querySelector('.fphoto')?.addEventListener('keydown', e => {
    if (e.key === 'Enter' || e.key === ' ') {
        e.preventDefault();
        document.getElementById('fi-foto').click();
    }
});
</script>

<?php include('footer.php'); ?>