<?php
include('header.php');
include('../config/conexao.php');

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
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
$mensagem = '';
$tipo_mensagem = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome']);
    $latitude = floatval($_POST['latitude']);
    $longitude = floatval($_POST['longitude']);
    
    // Validações
    if (empty($nome)) {
        $mensagem = 'Nome da província é obrigatório';
        $tipo_mensagem = 'danger';
    } elseif (empty($_POST['latitude']) || empty($_POST['longitude'])) {
        $mensagem = 'Latitude e longitude são obrigatórias';
        $tipo_mensagem = 'danger';
    } elseif ($latitude < -27 || $latitude > -10 || $longitude < 30 || $longitude > 41) {
        $mensagem = 'Coordenadas inválidas para Moçambique';
        $tipo_mensagem = 'danger';
    } else {
        if ($id > 0) {
            // Atualizar província existente
            $stmt = $conn->prepare("UPDATE provincias SET nome = ?, latitude = ?, longitude = ? WHERE id = ?");
            $stmt->bind_param("sddi", $nome, $latitude, $longitude, $id);
            
            if ($stmt->execute()) {
                $mensagem = 'Província atualizada com sucesso!';
                $tipo_mensagem = 'success';
                
                // Atualizar dados para exibição
                $provincia['nome'] = $nome;
                $provincia['latitude'] = $latitude;
                $provincia['longitude'] = $longitude;
            } else {
                $mensagem = 'Erro ao atualizar província: ' . $conn->error;
                $tipo_mensagem = 'danger';
            }
        } else {
            // Inserir nova província
            $stmt = $conn->prepare("INSERT INTO provincias (nome, latitude, longitude) VALUES (?, ?, ?)");
            $stmt->bind_param("sdd", $nome, $latitude, $longitude);
            
            if ($stmt->execute()) {
                $mensagem = 'Província adicionada com sucesso!';
                $tipo_mensagem = 'success';
                $id = $conn->insert_id;
                
                // Redirecionar após 2 segundos
                echo "<script>setTimeout(function(){ window.location.href='provincias.php'; }, 2000);</script>";
            } else {
                if (strpos($conn->error, 'Duplicate entry') !== false) {
                    $mensagem = 'Já existe uma província com este nome';
                } else {
                    $mensagem = 'Erro ao adicionar província: ' . $conn->error;
                }
                $tipo_mensagem = 'danger';
            }
        }
    }
}

$conn->close();
?>

<!-- Content wrapper -->
<div class="content-wrapper">
    <!-- Content -->
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold py-3 mb-0">
                <span class="text-muted fw-light">Províncias /</span> <?php echo $acao; ?>
            </h4>
            <a href="provincias.php" class="btn btn-secondary">
                <i class='bx bx-arrow-back'></i> Voltar
            </a>
        </div>

        <?php if (!empty($mensagem)): ?>
        <div class="alert alert-<?php echo $tipo_mensagem; ?> alert-dismissible fade show" role="alert">
            <i class='bx <?php echo $tipo_mensagem === 'success' ? 'bx-check-circle' : 'bx-error-circle'; ?> me-2'></i>
            <?php echo $mensagem; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php endif; ?>

        <div class="row">
            <div class="col-md-8">
                <div class="card mb-4">
                    <div class="card-header d-flex align-items-center">
                        <i class='bx bx-map-pin text-primary me-2' style='font-size: 1.5rem;'></i>
                        <h5 class="mb-0"><?php echo $acao; ?> Província</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="">
                            <div class="mb-4">
                                <label for="nome" class="form-label">Nome da Província <span class="text-danger">*</span></label>
                                <input 
                                    type="text" 
                                    class="form-control" 
                                    id="nome" 
                                    name="nome" 
                                    placeholder="Ex: Maputo, Nampula, Cabo Delgado..."
                                    value="<?php echo $provincia ? htmlspecialchars($provincia['nome']) : ''; ?>"
                                    required
                                />
                                <div class="form-text">Digite o nome oficial da província</div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-4">
                                    <label for="latitude" class="form-label">Latitude <span class="text-danger">*</span></label>
                                    <input 
                                        type="number" 
                                        class="form-control" 
                                        id="latitude" 
                                        name="latitude" 
                                        step="0.00000001"
                                        placeholder="-25.9692"
                                        value="<?php echo $provincia ? $provincia['latitude'] : ''; ?>"
                                        required
                                    />
                                    <div class="form-text">Entre -27 e -10 para Moçambique</div>
                                </div>

                                <div class="col-md-6 mb-4">
                                    <label for="longitude" class="form-label">Longitude <span class="text-danger">*</span></label>
                                    <input 
                                        type="number" 
                                        class="form-control" 
                                        id="longitude" 
                                        name="longitude" 
                                        step="0.00000001"
                                        placeholder="32.5732"
                                        value="<?php echo $provincia ? $provincia['longitude'] : ''; ?>"
                                        required
                                    />
                                    <div class="form-text">Entre 30 e 41 para Moçambique</div>
                                </div>
                            </div>

                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class='bx bx-save'></i> <?php echo $acao === 'Adicionar' ? 'Adicionar' : 'Salvar Alterações'; ?>
                                </button>
                                <a href="provincias.php" class="btn btn-outline-secondary">
                                    <i class='bx bx-x'></i> Cancelar
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <!-- Ajuda -->
                <div class="card mb-4">
                    <div class="card-body">
                        <h6 class="card-title">
                            <i class='bx bx-help-circle text-primary'></i> Como obter coordenadas?
                        </h6>
                        <p class="card-text small">
                            <strong>Método 1: Google Maps</strong><br>
                            1. Acesse Google Maps<br>
                            2. Clique com o botão direito no local<br>
                            3. Copie as coordenadas exibidas
                        </p>
                        <hr>
                        <p class="card-text small mb-0">
                            <strong>Método 2: OpenStreetMap</strong><br>
                            1. Acesse openstreetmap.org<br>
                            2. Clique em "Compartilhar"<br>
                            3. Marque o local e copie as coordenadas
                        </p>
                    </div>
                </div>

                <!-- Preview do Mapa -->
                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title">
                            <i class='bx bx-map text-primary'></i> Preview das Coordenadas
                        </h6>
                        <div id="map-preview" style="height: 300px; border-radius: 8px; overflow: hidden;">
                            <div class="d-flex align-items-center justify-content-center h-100 bg-light">
                                <p class="text-muted mb-0">
                                    <i class='bx bx-map' style='font-size: 2rem;'></i><br>
                                    Digite as coordenadas para visualizar
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Províncias de Referência -->
                <div class="card mt-4">
                    <div class="card-body">
                        <h6 class="card-title">
                            <i class='bx bx-current-location text-primary'></i> Coordenadas de Referência
                        </h6>
                        <div class="small">
                            <strong>Maputo:</strong> -25.9692, 32.5732<br>
                            <strong>Nampula:</strong> -15.1165, 39.2666<br>
                            <strong>Beira:</strong> -19.8436, 34.8389<br>
                            <strong>Pemba:</strong> -12.9744, 40.5147<br>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- / Content -->

    <?php include('footerprincipal.php'); ?>
    <div class="content-backdrop fade"></div>
</div>
<!-- Content wrapper -->

<style>
    .btn-primary {
        background: linear-gradient(135deg, #FF6F0F 0%, #E05A00 100%);
        border: none;
        box-shadow: 0 4px 12px rgba(255, 111, 15, 0.3);
        transition: all 0.3s ease;
    }
    
    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(255, 111, 15, 0.4);
    }
    
    .text-primary {
        color: #FF6F0F !important;
    }
    
    .form-control:focus {
        border-color: #FF6F0F;
        box-shadow: 0 0 0 0.2rem rgba(255, 111, 15, 0.25);
    }
</style>

<?php include('footer.php'); ?>
