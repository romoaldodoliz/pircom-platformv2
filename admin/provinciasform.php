<?php
session_start();
include('../config/conexao.php');

$id   = isset($_GET['id']) ? intval($_GET['id']) : 0;
$acao = $id > 0 ? 'Editar' : 'Adicionar';

// Buscar dados da província se for edição
$provincia = null;
if ($id > 0) {
    $stmt = $conn->prepare("SELECT * FROM provincias WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $provincia = $result->fetch_assoc();
    } else {
        header('Location: provincias.php');
        exit();
    }
}

// Processar formulário
$mensagem      = '';
$tipo_mensagem = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome      = trim($_POST['nome']);
    $latitude  = floatval($_POST['latitude']);
    $longitude = floatval($_POST['longitude']);

    if (empty($nome)) {
        $mensagem      = 'Nome da província é obrigatório.';
        $tipo_mensagem = 'danger';
    } elseif (empty($_POST['latitude']) || empty($_POST['longitude'])) {
        $mensagem      = 'Latitude e longitude são obrigatórias.';
        $tipo_mensagem = 'danger';
    } elseif ($latitude < -27 || $latitude > -10 || $longitude < 30 || $longitude > 41) {
        $mensagem      = 'Coordenadas inválidas para Moçambique (lat: -27 a -10 / lng: 30 a 41).';
        $tipo_mensagem = 'danger';
    } else {
        if ($id > 0) {
            $stmt = $conn->prepare("UPDATE provincias SET nome = ?, latitude = ?, longitude = ? WHERE id = ?");
            $stmt->bind_param("sddi", $nome, $latitude, $longitude, $id);

            if ($stmt->execute()) {
                $_SESSION['flash'] = ['type' => 'success', 'text' => 'Província "' . htmlspecialchars($nome) . '" atualizada com sucesso!'];
                header('Location: provincias.php');
                exit;
            } else {
                $mensagem      = 'Erro ao atualizar: ' . $conn->error;
                $tipo_mensagem = 'danger';
            }
        } else {
            $stmt = $conn->prepare("INSERT INTO provincias (nome, latitude, longitude) VALUES (?, ?, ?)");
            $stmt->bind_param("sdd", $nome, $latitude, $longitude);

            if ($stmt->execute()) {
                $_SESSION['flash'] = ['type' => 'success', 'text' => 'Província "' . htmlspecialchars($nome) . '" adicionada com sucesso!'];
                header('Location: provincias.php');
                exit;
            } else {
                $mensagem      = strpos($conn->error, 'Duplicate entry') !== false
                    ? 'Já existe uma província com este nome.'
                    : 'Erro ao adicionar: ' . $conn->error;
                $tipo_mensagem = 'danger';
            }
        }
    }
}

// Valores do form (POST > BD)
$f_nome = htmlspecialchars($_POST['nome']      ?? $provincia['nome']      ?? '');
$f_lat  = htmlspecialchars($_POST['latitude']  ?? $provincia['latitude']  ?? '');
$f_lng  = htmlspecialchars($_POST['longitude'] ?? $provincia['longitude'] ?? '');

$conn->close();

include('header.php');
?>

<style>
@import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');

:root {
    --primary: #2563eb;
    --primary-light: rgba(37,99,235,0.08);
    --primary-mid: rgba(37,99,235,0.15);
    --success: #16a34a;
    --success-light: rgba(22,163,74,0.08);
    --danger: #dc2626;
    --danger-light: rgba(220,38,38,0.08);
    --info: #0891b2;
    --info-light: rgba(8,145,178,0.08);
    --warning: #d97706;
    --warning-light: rgba(217,119,6,0.08);
    --bg: #f4f5f7;
    --surface: #ffffff;
    --border: #e5e7eb;
    --text-primary: #111827;
    --text-secondary: #6b7280;
    --text-muted: #9ca3af;
    --radius-sm: 8px;
    --radius-md: 12px;
    --radius-lg: 16px;
    --shadow-sm: 0 1px 3px rgba(0,0,0,0.06);
    --shadow-md: 0 4px 16px rgba(0,0,0,0.07);
}

body, .content-wrapper * { font-family: 'Plus Jakarta Sans', sans-serif; }

.form-wrapper { padding: 1.5rem; background: var(--bg); min-height: 100vh; }

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
.form-page-header p  { font-size: 0.85rem; color: var(--text-muted); margin: 0; }

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

/* ── LAYOUT GRID ── */
.form-layout { display: grid; grid-template-columns: 1fr 380px; gap: 1.25rem; align-items: start; }

/* ── CARDS ── */
.form-card {
    background: var(--surface); border-radius: var(--radius-lg);
    box-shadow: var(--shadow-sm); border: 1px solid var(--border); overflow: hidden;
}
.form-card-header {
    padding: 1.125rem 1.5rem; border-bottom: 1px solid var(--border);
    display: flex; align-items: center; gap: 0.5rem;
    font-size: 0.9375rem; font-weight: 700; color: var(--text-primary);
}
.form-card-header i { color: var(--primary); font-size: 1.1rem; }
.form-card-body { padding: 1.5rem; }

/* ── FORM CONTROLS ── */
.field-group { display: flex; flex-direction: column; gap: 0.375rem; margin-bottom: 1.25rem; }
.field-group:last-of-type { margin-bottom: 0; }
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
.field-control.valid   { border-color: var(--success); }
.field-control.invalid { border-color: var(--danger); }
.field-hint { font-size: 0.75rem; color: var(--text-muted); margin-top: 0.2rem; display: flex; align-items: center; gap: 0.3rem; }

.coord-row { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }

/* ── COORD STATUS ── */
.coord-status {
    display: none; align-items: center; gap: 0.5rem;
    padding: 0.625rem 0.875rem; border-radius: var(--radius-sm);
    font-size: 0.8125rem; font-weight: 600; margin-bottom: 1.25rem;
}
.coord-status.show  { display: flex; }
.coord-status.valid { background: var(--success-light); color: var(--success); border: 1px solid rgba(22,163,74,0.2); }
.coord-status.invalid { background: var(--danger-light); color: var(--danger); border: 1px solid rgba(220,38,38,0.2); }

/* ── BUTTONS ── */
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
.btn-ghost:hover { background: #e5e7eb; color: var(--text-primary); }
.btn-back   { background: transparent; color: var(--text-secondary); border: 1.5px solid var(--border); }
.btn-back:hover { background: #f3f4f6; color: var(--text-primary); }

/* ── MAP PREVIEW ── */
.map-container {
    height: 280px;
    border-radius: var(--radius-md);
    overflow: hidden;
    position: relative;
    background: linear-gradient(135deg, #1e3a8a 0%, #1e40af 60%, #2563eb 100%);
    transition: all 0.3s ease;
}

/* Placeholder (sem coords) */
.map-placeholder {
    position: absolute; inset: 0;
    display: flex; flex-direction: column;
    align-items: center; justify-content: center;
    color: rgba(255,255,255,0.5); gap: 0.5rem;
    transition: opacity 0.3s;
}
.map-placeholder i    { font-size: 2.5rem; }
.map-placeholder span { font-size: 0.8rem; font-weight: 600; text-align: center; max-width: 160px; line-height: 1.4; }

/* Mapa ativo */
.map-active { display: none; position: absolute; inset: 0; }
.map-active.show { display: block; }

/* Grid de "mapa" visual */
.map-grid {
    position: absolute; inset: 0;
    background-image:
        linear-gradient(rgba(255,255,255,0.04) 1px, transparent 1px),
        linear-gradient(90deg, rgba(255,255,255,0.04) 1px, transparent 1px);
    background-size: 40px 40px;
}

/* Pin central animado */
.map-pin-wrap {
    position: absolute; top: 50%; left: 50%;
    transform: translate(-50%, -50%);
    display: flex; flex-direction: column; align-items: center;
}
.map-pin-icon {
    font-size: 2.5rem; color: #fbbf24;
    filter: drop-shadow(0 4px 8px rgba(0,0,0,0.5));
    animation: pinBounce 1s ease-out;
}
@keyframes pinBounce {
    0%   { transform: translateY(-20px); opacity: 0; }
    60%  { transform: translateY(4px); }
    80%  { transform: translateY(-4px); }
    100% { transform: translateY(0); opacity: 1; }
}
.map-pin-pulse {
    width: 32px; height: 8px; margin-top: -4px;
    background: radial-gradient(ellipse, rgba(0,0,0,0.4), transparent 70%);
    border-radius: 50%;
    animation: shadowPulse 2s ease-in-out infinite;
}
@keyframes shadowPulse {
    0%, 100% { transform: scaleX(1); opacity: 0.6; }
    50%       { transform: scaleX(0.7); opacity: 0.3; }
}

/* Overlay de info no mapa */
.map-info-overlay {
    position: absolute; bottom: 0; left: 0; right: 0;
    background: linear-gradient(to top, rgba(15,23,42,0.9) 0%, transparent 100%);
    padding: 1rem 1rem 0.875rem;
    display: flex; align-items: flex-end; justify-content: space-between;
}
.map-info-name  { font-size: 0.9375rem; font-weight: 700; color: white; }
.map-info-coord { font-size: 0.75rem; color: rgba(255,255,255,0.65); font-family: 'Courier New', monospace; }
.map-open-btn {
    display: inline-flex; align-items: center; gap: 0.35rem;
    background: rgba(255,255,255,0.15); border: 1px solid rgba(255,255,255,0.25);
    color: white; padding: 0.35rem 0.75rem; border-radius: 999px;
    font-size: 0.75rem; font-weight: 600; text-decoration: none;
    transition: background 0.15s; white-space: nowrap; flex-shrink: 0;
}
.map-open-btn:hover { background: rgba(255,255,255,0.28); color: white; }

/* ── REF CARD ── */
.ref-table { width: 100%; border-collapse: collapse; font-size: 0.8125rem; }
.ref-table td { padding: 0.4rem 0; border-bottom: 1px solid var(--border); color: var(--text-secondary); }
.ref-table td:first-child { font-weight: 700; color: var(--text-primary); width: 50%; }
.ref-table tr:last-child td { border-bottom: none; }
.ref-coord {
    font-family: 'Courier New', monospace; font-size: 0.75rem;
    cursor: pointer; color: var(--info);
    transition: color 0.15s;
}
.ref-coord:hover { color: var(--primary); text-decoration: underline; }

/* ── HELP STEPS ── */
.help-step {
    display: flex; gap: 0.75rem; align-items: flex-start;
    padding: 0.625rem 0; border-bottom: 1px solid var(--border);
}
.help-step:last-child { border-bottom: none; padding-bottom: 0; }
.help-step-num {
    width: 22px; height: 22px; flex-shrink: 0;
    background: var(--primary-light); color: var(--primary);
    border-radius: 50%; display: flex; align-items: center; justify-content: center;
    font-size: 0.7rem; font-weight: 800; margin-top: 1px;
}
.help-step-text { font-size: 0.8125rem; color: var(--text-secondary); line-height: 1.5; }
.help-step-text strong { color: var(--text-primary); }

/* ── RESPONSIVE ── */
@media (max-width: 900px) {
    .form-layout { grid-template-columns: 1fr; }
    .side-col { display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem; }
}
@media (max-width: 640px) {
    .form-wrapper { padding: 1rem; }
    .form-page-header h1 { font-size: 1.25rem; }
    .form-card-body { padding: 1rem; }
    .coord-row { grid-template-columns: 1fr; }
    .side-col { grid-template-columns: 1fr; }
}
</style>

<div class="content-wrapper">
<div class="form-wrapper">

    <!-- Header -->
    <div class="form-page-header">
        <div class="form-page-header-left">
            <div class="form-page-icon">
                <i class="bx <?php echo $id > 0 ? 'bx-edit' : 'bx-map-pin'; ?>"></i>
            </div>
            <div>
                <h1><?php echo $acao; ?> Província</h1>
                <p>Províncias / <?php echo $acao; ?> cobertura geográfica</p>
            </div>
        </div>
        <a href="provincias.php" class="btn btn-back">
            <i class="bx bx-arrow-back"></i> Voltar
        </a>
    </div>

    <!-- Alert erro/validação -->
    <?php if (!empty($mensagem)): ?>
    <div class="form-alert <?php echo $tipo_mensagem; ?>">
        <i class="bx <?php echo $tipo_mensagem === 'success' ? 'bx-check-circle' : 'bx-error-circle'; ?>" style="font-size:1.25rem;flex-shrink:0;"></i>
        <span><?php echo htmlspecialchars($mensagem); ?></span>
    </div>
    <?php endif; ?>

    <div class="form-layout">

        <!-- ── FORM ── -->
        <div>
            <div class="form-card">
                <div class="form-card-header">
                    <i class="bx bx-map-pin"></i>
                    <?php echo $id > 0 ? 'Editar dados da província' : 'Nova província'; ?>
                </div>
                <div class="form-card-body">
                    <form method="POST" action="" id="provinciaForm">

                        <!-- Nome -->
                        <div class="field-group">
                            <label class="field-label" for="nome">
                                Nome da Província <span>*</span>
                            </label>
                            <input type="text" id="nome" name="nome" class="field-control"
                                   placeholder="Ex: Maputo, Nampula, Cabo Delgado..."
                                   value="<?php echo $f_nome; ?>" required>
                            <span class="field-hint"><i class="bx bx-info-circle"></i> Digite o nome oficial da província.</span>
                        </div>

                        <!-- Status das coordenadas -->
                        <div class="coord-status" id="coordStatus">
                            <i class="bx bx-map-pin"></i>
                            <span id="coordStatusText"></span>
                        </div>

                        <!-- Coordenadas -->
                        <div class="coord-row">
                            <div class="field-group">
                                <label class="field-label" for="latitude">
                                    Latitude <span>*</span>
                                </label>
                                <input type="number" id="latitude" name="latitude"
                                       class="field-control" step="0.00000001"
                                       placeholder="-25.9692"
                                       value="<?php echo $f_lat; ?>" required>
                                <span class="field-hint"><i class="bx bx-info-circle"></i> Entre -27 e -10 (Moçambique)</span>
                            </div>
                            <div class="field-group">
                                <label class="field-label" for="longitude">
                                    Longitude <span>*</span>
                                </label>
                                <input type="number" id="longitude" name="longitude"
                                       class="field-control" step="0.00000001"
                                       placeholder="32.5732"
                                       value="<?php echo $f_lng; ?>" required>
                                <span class="field-hint"><i class="bx bx-info-circle"></i> Entre 30 e 41 (Moçambique)</span>
                            </div>
                        </div>

                        <!-- Botões -->
                        <div style="display:flex;gap:0.75rem;padding-top:0.5rem;flex-wrap:wrap;">
                            <button type="submit" class="btn btn-primary">
                                <i class="bx <?php echo $id > 0 ? 'bx-save' : 'bx-plus'; ?>"></i>
                                <?php echo $id > 0 ? 'Guardar alterações' : 'Adicionar Província'; ?>
                            </button>
                            <a href="provincias.php" class="btn btn-ghost">
                                <i class="bx bx-x"></i> Cancelar
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- ── SIDEBAR ── -->
        <div class="side-col">

            <!-- Mapa preview -->
            <div class="form-card">
                <div class="form-card-header">
                    <i class="bx bx-map"></i> Pré-visualização
                </div>
                <div class="form-card-body" style="padding:1rem;">
                    <div class="map-container" id="mapContainer">

                        <!-- Placeholder (coords vazias) -->
                        <div class="map-placeholder" id="mapPlaceholder">
                            <i class="bx bx-map"></i>
                            <span>Digite as coordenadas para visualizar a localização</span>
                        </div>

                        <!-- Mapa visual ativo -->
                        <div class="map-active" id="mapActive">
                            <div class="map-grid"></div>
                            <div class="map-pin-wrap" id="mapPinWrap">
                                <i class="bx bx-map-pin map-pin-icon"></i>
                                <div class="map-pin-pulse"></div>
                            </div>
                            <div class="map-info-overlay">
                                <div>
                                    <div class="map-info-name"  id="mapInfoName">—</div>
                                    <div class="map-info-coord" id="mapInfoCoord">—</div>
                                </div>
                                <a href="#" id="mapGoogleBtn" class="map-open-btn" target="_blank">
                                    <i class="bx bx-map-alt"></i> Google Maps
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Coordenadas de referência -->
            <div class="form-card">
                <div class="form-card-header">
                    <i class="bx bx-current-location"></i> Referências
                </div>
                <div class="form-card-body" style="padding:1rem 1.25rem;">
                    <table class="ref-table">
                        <?php
                        $refs = [
                            'Maputo'       => ['-25.9692', '32.5732'],
                            'Nampula'      => ['-15.1165', '39.2666'],
                            'Beira'        => ['-19.8436', '34.8389'],
                            'Pemba'        => ['-12.9744', '40.5147'],
                            'Quelimane'    => ['-17.8784', '36.8881'],
                            'Tete'         => ['-16.1564', '33.5867'],
                            'Lichinga'     => ['-13.3122', '35.2433'],
                            'Chimoio'      => ['-19.1164', '33.4833'],
                            'Inhambane'    => ['-23.8650', '35.3833'],
                            'Xai-Xai'     => ['-25.0519', '33.6442'],
                        ];
                        foreach ($refs as $cidade => [$lat, $lng]):
                        ?>
                        <tr>
                            <td><?php echo $cidade; ?></td>
                            <td>
                                <span class="ref-coord"
                                      onclick="fillCoords(<?php echo $lat; ?>, <?php echo $lng; ?>, '<?php echo $cidade; ?>')"
                                      title="Clique para usar estas coordenadas">
                                    <?php echo $lat; ?>, <?php echo $lng; ?>
                                </span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </table>
                </div>
            </div>

            <!-- Como obter coordenadas -->
            <div class="form-card">
                <div class="form-card-header">
                    <i class="bx bx-help-circle"></i> Como obter coordenadas?
                </div>
                <div class="form-card-body" style="padding:1rem 1.25rem;">
                    <p style="font-size:0.8125rem;font-weight:700;color:var(--text-primary);margin-bottom:0.75rem;">Google Maps</p>
                    <div>
                        <?php
                        $steps = [
                            ['Acesse', 'maps.google.com'],
                            ['Clique com o botão direito', 'no local desejado'],
                            ['Copie as', 'coordenadas exibidas no topo'],
                            ['Cole nos campos', 'latitude e longitude acima'],
                        ];
                        foreach ($steps as $i => [$a, $b]):
                        ?>
                        <div class="help-step">
                            <div class="help-step-num"><?php echo $i + 1; ?></div>
                            <div class="help-step-text"><?php echo $a; ?> <strong><?php echo $b; ?></strong></div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

        </div><!-- /side-col -->
    </div><!-- /form-layout -->

</div>
</div>

<?php include('footerprincipal.php'); ?>
<div class="content-backdrop fade"></div>

<script>
(function () {
    const latInput  = document.getElementById('latitude');
    const lngInput  = document.getElementById('longitude');
    const nomeInput = document.getElementById('nome');

    const mapPlaceholder = document.getElementById('mapPlaceholder');
    const mapActive      = document.getElementById('mapActive');
    const mapInfoName    = document.getElementById('mapInfoName');
    const mapInfoCoord   = document.getElementById('mapInfoCoord');
    const mapGoogleBtn   = document.getElementById('mapGoogleBtn');
    const mapPinWrap     = document.getElementById('mapPinWrap');
    const coordStatus    = document.getElementById('coordStatus');
    const coordStatusTxt = document.getElementById('coordStatusText');

    // ── Validação de coordenadas Moçambique ──
    function isValidMoz(lat, lng) {
        return lat >= -27 && lat <= -10 && lng >= 30 && lng <= 41;
    }

    // ── Actualiza o mapa visual ──
    function updateMap() {
        const lat  = parseFloat(latInput.value);
        const lng  = parseFloat(lngInput.value);
        const nome = nomeInput.value.trim() || 'Localização';

        const hasCoords = !isNaN(lat) && !isNaN(lng) && latInput.value !== '' && lngInput.value !== '';

        if (!hasCoords) {
            mapPlaceholder.style.opacity = '1';
            mapActive.classList.remove('show');
            coordStatus.classList.remove('show', 'valid', 'invalid');
            latInput.classList.remove('valid', 'invalid');
            lngInput.classList.remove('valid', 'invalid');
            return;
        }

        const valid = isValidMoz(lat, lng);

        // Status badge
        coordStatus.classList.add('show');
        coordStatus.classList.toggle('valid',   valid);
        coordStatus.classList.toggle('invalid', !valid);
        coordStatusTxt.textContent = valid
            ? `Coordenadas válidas para Moçambique — ${lat.toFixed(4)}, ${lng.toFixed(4)}`
            : 'Coordenadas fora do intervalo de Moçambique (lat: -27 a -10 / lng: 30 a 41)';

        // Estilo dos inputs
        latInput.classList.toggle('valid',   valid);
        latInput.classList.toggle('invalid', !valid);
        lngInput.classList.toggle('valid',   valid);
        lngInput.classList.toggle('invalid', !valid);

        // Mapa visual
        mapPlaceholder.style.opacity = '0';
        mapActive.classList.add('show');

        // Re-trigger pin animation
        mapPinWrap.style.animation = 'none';
        void mapPinWrap.offsetHeight;
        const pinIcon = mapPinWrap.querySelector('.map-pin-icon');
        pinIcon.style.animation = 'none';
        void pinIcon.offsetHeight;
        pinIcon.style.animation = '';

        mapInfoName.textContent  = nome;
        mapInfoCoord.textContent = `${lat.toFixed(6)}, ${lng.toFixed(6)}`;
        mapGoogleBtn.href = `https://www.google.com/maps?q=${lat},${lng}&z=10`;
    }

    // ── Eventos ──
    latInput.addEventListener('input',  updateMap);
    lngInput.addEventListener('input',  updateMap);
    nomeInput.addEventListener('input', updateMap);

    // ── Clique nas referências ──
    window.fillCoords = function(lat, lng, nome) {
        latInput.value  = lat;
        lngInput.value  = lng;
        if (!nomeInput.value.trim()) nomeInput.value = nome;
        updateMap();
        latInput.focus();
    };

    // ── Inicializar se já tiver valores (modo edição) ──
    if (latInput.value && lngInput.value) updateMap();
})();
</script>

<?php include('footer.php'); ?>